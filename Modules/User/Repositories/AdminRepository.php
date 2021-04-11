<?php

namespace Modules\User\Repositories;

use Exception;
use Modules\User\Entities\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Modules\User\Contracts\AdminInterface;
use Modules\User\Exceptions\CannotDeleteSelfException;
use Modules\User\Exceptions\InvalidCredentialException;
use Modules\User\Exceptions\CannotDeleteSuperAdminException;

class AdminRepository implements AdminInterface
{
    protected $model, $model_key, $main_image_dimensions, $gallery_image_dimensions;

    public function __construct(Admin $admin)
    {
        $this->model = $admin;
        $this->model_key = "admin";
        $this->main_image_dimensions = config('user_image.image_dimensions.user.main_image');
        $this->gallery_image_dimensions = config('user_image.image_dimensions.user.gallery_images');
    }

    /**
     * Get current Model
     * 
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Create a new resource
     * 
     * @param array $data
     * @return Model
     */
    public function create($data)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            $created = $this->model->create($data);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
        DB::commit();

        return $created;
    }

    /**
     * Update requested resource
     * 
     * @param array $data
     * @param int $id
     * @return Model
     */
    public function update($data, $id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update.before");

        try
        {
            $updated = $this->model->findOrFail($id);
            $updated->fill($data);
            $updated->save();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.update.after", $updated);
        DB::commit();

        return $updated;
    }

    /**
     * Upload profile image
     * 
     * @param Request $request
     * @param int $id
     * @return Model
     */
    public function uploadProfileImage($request, $id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.profile_image.update.before");

        try
        {
            $request->validate([
                'image' => 'required|mimes:jpeg,jpg,png',
            ]);
            $updated = $this->model->findOrFail($id);

            // Store File
            $file = $request->file("image");
            $key = \Str::random(6);
            $file_name = $file->getClientOriginalName();
            $file_path = $file->storeAs("images/users/{$key}", $file_name, ["disk" => "public"]);

            $updated->fill(["profile_image" => $file_path]);
            $updated->save();

            // Store main_image and gallery_image variations
            foreach (["main_image_dimensions" => "main", "gallery_image_dimensions" => "gallery"] as $type => $folder) {
                foreach ($this->{$type} as $dimension) {
                    $width = $dimension["width"];
                    $height = $dimension["height"];
                    $path = "images/users/{$key}/{$folder}/{$width}x{$height}";
                    if(!Storage::disk('public')->has($path)) Storage::disk('public')->makeDirectory($path, 0777, true, true);

                    $image = Image::make($file)->resize($width, $height);
                    $image->save(public_path().Storage::url("$path/{$file_name}"));
                }
            }
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.profile_image.update.after", $updated);
        DB::commit();

        return $updated;
    }

    /**
     * Delete requested image
     * 
     * @param int $id
     * @return Model
     */
    public function removeOldImage($id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.profile_image.before");

        try
        {
            $updated = $this->model->findOrFail($id);
            if (!$updated->profile_image) {
                DB::commit();
                return $updated;
            }

            $path_array = explode("/", $updated->profile_image);
            unset($path_array[count($path_array) - 1]);
    
            $delete_folder = implode("/", $path_array);
            Storage::disk("public")->deleteDirectory($delete_folder);

            $updated->fill(["profile_image" => null]);
            $updated->save();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.profile_image.after", $updated);
        DB::commit();

        return $updated;
    }

    /**
     * Delete requested resource
     * 
     * @param int $id
     * @return Model
     */
    public function delete($id)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $current_user = Auth::guard('admin')->user();
            $deleted = $this->model->findOrFail($id);

            if ( $deleted->id == $current_user->id ) throw new CannotDeleteSelfException("Admin cannot delete itself.");

            if ( $deleted->hasRole("super-admin") ) throw new CannotDeleteSuperAdminException("Super admin cannot be deleted.");

            $this->removeOldImage($deleted->id);
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.after", $deleted);
        DB::commit();

        return $deleted;
    }

    /**
     * Delete requested resources in bulk
     * 
     * @param Request $request
     * @return Model
     */
    public function bulkDelete($request)
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.delete.before");

        try
        {
            $request->validate([
                'ids' => 'array|required',
                'ids.*' => 'required|exists:activity_logs,id',
            ]);

            $deleted = $this->model->whereIn('id', $request->ids);
            $deleted->delete();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.delete.after", $deleted);
        DB::commit();

        return $deleted;
    }

    /**
     * Returns validation rules
     * 
     * @param int $id
     * @param array $merge
     * @return array
     */
    public function rules($id, $merge = [])
    {
        $id = $id ? ",{$id}" : null;

        return array_merge([
            "first_name" => "required|min:2|max:200",
            "last_name" => "required|min:2|max:200",
            "email" => "required|email|unique:admins,email{$id}",
            "current_password" => "sometimes|min:6|max:200",
            "password" => "sometimes|required|min:6|confirmed|max:200",
            "company" =>"sometimes|min:3|max:200",
            "address" =>"sometimes|min:3|max:200",
        ], $merge);
    }

    /**
     * Validates form request
     * 
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function validateData($request, $id=null, $merge = [], $check_current = true)
    {
        // Validation for current_password if new password is passed
        if ( $check_current ) {
            $current_password_validation = ($request->has("password") && $id) ? "required" : "sometimes";
            $merge = array_merge([
                "current_password" => "$current_password_validation|min:6|max:200"
            ], $merge);
        }

        $data = $request->validate($this->rules($id, $merge));

        // If user is trying to update password, check old password and hash the new password
        if ($request->has("password") && $request->has('current_password')) {
            if (!Hash::check($request->current_password, auth()->guard('admin')->user()->password)) {
                throw new InvalidCredentialException(__("core::app.users.users.incorrect-password"));
            }

            $data["password"] = Hash::make($request->password);
        } else {
            unset($data["password"]);
        }
        unset($data["current_password"]);

        return $data;
    }
}

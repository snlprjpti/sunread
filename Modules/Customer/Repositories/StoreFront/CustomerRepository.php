<?php

namespace Modules\Customer\Repositories\StoreFront;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Modules\Customer\Entities\Customer;
use Modules\Core\Repositories\BaseRepository;

class CustomerRepository extends BaseRepository
{
    protected $main_image_dimensions, $gallery_image_dimensions;

    public function __construct(Customer $customer)
    {
        $this->model = $customer;
        $this->model_key = "customers.customers";
        $this->main_image_dimensions = config('customer_image.image_dimensions.user.main_image');
        $this->gallery_image_dimensions = config('customer_image.image_dimensions.user.gallery_images');

        $this->rules = [
            "first_name" => "required|min:2|max:200",
            "middle_name" => "sometimes|nullable|min:2|max:200",
            "last_name" => "required|min:2|max:200",
            "email" => "required|email|unique:customers,email",
            "gender" => "required|in:male,female,other",
            "date_of_birth" => "date|before:today",
            "subscribed_to_news_letter" => "sometimes|boolean"
        ];
    }

    public function getPasswordRules(object $request) : array
    {
        $merge_rule = [];
        if ( isset($request->current_password) ||  isset($request->password) ) {
            $merge_rule = [
                "current_password" => "required|min:6|max:200",
                "password" => "required|min:6|confirmed"
            ];
        }
        return $merge_rule;
    }

    public function getPassword(object $request) : ?string
    {
        if (!Hash::check($request->current_password, auth()->guard('customer')->user()->password)) {
            throw new Exception("Password is incorrect.");
        }
        return Hash::make($request->password);        
    }

    public function uploadProfileImage(object $request, int $id): object
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
            $key = Str::random(6);
            $file_name = $this->generateFileName($file);
            $file_path = $file->storeAs("images/customers/{$key}", $file_name, ["disk" => "public"]);
            $updated->fill(["profile_image" => $file_path]);
            $updated->save();
            // Store main_image and gallery_image variations
            foreach (["main_image_dimensions" => "main", "gallery_image_dimensions" => "gallery"] as $type => $folder) {
                foreach ($this->{$type} as $dimension) {
                    $width = $dimension["width"];
                    $height = $dimension["height"];
                    $path = "images/customers/{$key}/{$folder}/{$width}x{$height}";
                    if(!Storage::has($path)) Storage::makeDirectory($path, 0777, true, true);

                    $image = Image::make($file)
                        ->fit($width, $height, function($constraint) {
                            $constraint->upsize();
                        })
                        ->encode('jpg', 80);
                    Storage::put("{$path}/{$file_name}", $image);
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

    public function removeOldImage(int $id): object
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
}

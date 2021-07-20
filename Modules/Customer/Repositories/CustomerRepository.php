<?php

namespace Modules\Customer\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Modules\Customer\Entities\Customer;
use Intervention\Image\Facades\Image;

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
            "middle_name" => "sometimes|min:2|max:200",
            "last_name" => "required|min:2|max:200",
            "email" => "required|email|unique:customers,email",
            "gender" => "sometimes|nullable|in:male,female,other",
            "date_of_birth" => "date|before:today",
            "status" => "sometimes|boolean",
            "is_lock" => "sometimes|boolean",
            "customer_group_id" => "nullable|exists:customer_groups,id",
            "website_id" => "required|exists:websites,id",
            "store_id" => "nullable|exists:stores,id",
            "subscribed_to_news_letter" => "sometimes|boolean",
            "password" => "sometimes|nullable|min:6|confirmed"
        ];
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
            $key = \Str::random(6);
            $file_name = $file->getClientOriginalName();
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

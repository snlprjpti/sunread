<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Modules\Core\Http\Controllers\BaseController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Account Controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class AccountController extends BaseController
{

    protected $folder_path,$main_image_dimensions,$gallery_image_dimensions;

    public function __construct()
    {
        $this->folder_path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR;
        $this->main_image_dimensions = config('user_image.image_dimensions.user.main_image');
        $this->gallery_image_dimensions = config('user_image.image_dimensions.user.gallery_images');
    }

    /**
     * Show the form for creating a new resource
     * @return JsonResponse
     */
    public function edit()
    {
        try {
            $me = auth()->guard('admin')->user();
            return $this->successResponse($me,"Admin User  fetched successfully.");

        } catch (UnauthorizedHttpException $exception) {
            return $this->errorResponse($exception->getMessage(), 401);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {

            $user = auth()->guard('admin')->user();

            $this->validate($request, [
                'first_name' => 'sometimes|min:2|max:200',
                'last_name' => 'sometimes|min:2|max:200',
                'email' => 'email|unique:admins,email,' . $user->id,
                'password' => 'sometimes|required|min:6|confirmed|max:200',
                'current_password' => 'sometimes|min:6|max:200',
                'company' =>'sometimes|min:3|max:200',
                'address' =>'sometimes|min:3|max:200',
            ]);

            if ($request->has('password')) {
                $current_password = request()->get('current_password');
                if(is_null($current_password) || empty($current_password)){
                    throw ValidationException::withMessages([
                        "current_password" => "current password field is required"
                    ]);
                }
                if (!Hash::check($current_password, auth()->guard('admin')->user()->password)) {
                    return $this->errorResponse(trans('core::app.users.users.incorrect-password'), 401);
                }
                $request->merge(['password' => bcrypt($request->get('password'))]);
            }
            $user->fill($request->only('first_name','last_name','address','company', 'email', 'password'));
            $user->save();
            return $this->successResponse($user,trans('core::app.response.update-success', ['name' => 'Admin account']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    public function uploadProfileImage(Request $request)
    {
        try {
            $user = Auth::user();

            $this->validate($request, [
                'image' => 'required | mimes:jpeg,jpg,png',
            ]);

            $this->removeOldImage($user);

            //upload an image
            self::createFolderIfNotExist($this->folder_path);
            $file = $request->file('image');
            $file_name = rand(10000, 99999) . strtotime("now") . '_' . "profile_" . $file->getClientOriginalName();
            $file->move($this->folder_path, $file_name);

            foreach ($this->main_image_dimensions as $dimension) {
                $img = Image::make($this->folder_path . DIRECTORY_SEPARATOR . $file_name)->resize($dimension['width'], $dimension['height']);
                $img->save($this->folder_path . DIRECTORY_SEPARATOR . $dimension['width'] . '_' . $dimension['height'] . '_' . $file_name);
            }

            foreach ($this->gallery_image_dimensions as $dimension) {
                $img = Image::make($this->folder_path . DIRECTORY_SEPARATOR . $file_name)->resize($dimension['width'], $dimension['height']);
                $img->save($this->folder_path . DIRECTORY_SEPARATOR . $dimension['width'] . '_' . $dimension['height'] . '_' . $file_name);
            }

            //update an image
            $user->update([
                'profile_image' => $file_name
            ]);
            return $this->successResponse([
                'profile_image' => $user->getImage()
            ], "Profile image updated successfully");

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            $this->errorResponse('Sorry ,the image can  not be uploaded.Please try again');
        }

    }

    protected function createFolderIfNotExist($path)
    {
        if (!file_exists($path)) {
            File::makeDirectory($path, $mode = 0755, true, true);
        }
    }

    public function deleteProfileImage()
    {
        $user = Auth::user();
        if (isset($user) && isset($user->profile_image)) {
            if (file_exists($this->folder_path . $user->profile_image))
                unlink($this->folder_path . $user->profile_image);
            $user->profile_image = null;
            $user->save();
            return $this->successResponseWithMessage("Image deleted successfully.");
        }
        return $this->errorResponse("Unable to delete profile image.");
    }

    private function removeOldImage($user)
    {
        if (isset($user->profile_image) && file_exists($this->folder_path . $user->profile_image) && ($user->profile_image != ''))
            unlink($this->folder_path . $user->profile_image);

        // remove old image
        if ($user->profile_image) {

            if (file_exists($this->folder_path . $user->profile_image))
                unlink($this->folder_path . $user->profile_image);

            foreach ($this->main_image_dimensions as $dimension) {
                $d = $dimension['width'] . '_' . $dimension['height'] . '_';
                if (file_exists($this->folder_path . $d . $user->profile_image))
                    unlink($this->folder_path . $d . $user->profile_image);

            }

            foreach ($this->gallery_image_dimensions as $dimension) {
                $d = $dimension['width'] . '_' . $dimension['height'] . '_';
                if (file_exists($this->folder_path . $d . $user->profile_image))
                    unlink($this->folder_path . $d . $user->profile_image);

            }

        }

    }
}

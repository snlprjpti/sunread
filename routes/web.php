<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'App is running!!',
    ]);
});

Route::get('/check', function (\Illuminate\Http\Request $request) {
    return view('check');
});
Route::get('/acl', function (\Illuminate\Http\Request $request) {

    $route =  "admin.dashboard.index";
    $key_for_route = array_search($route, array_column(config('acl'), 'route'),true) === false;

    //php zero index problem so has to check with false here .
    if($key_for_route === false) {
        return false;
    }
    return  true;
});

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

use Illuminate\Encryption\Encrypter;

Route::get('/', function () {

        $key = 'H75fvPyuqHKKLxdXtphAtu3d6riRgh5a';
        $crypt = new Encrypter($key, 'AES-128-CBC');
        $test_string = 'eyJpdiI6IlJaRVpCU1N0VjZVellJQnJHcjlGd3c9PSIsInZhbHVlIjoibDdwODFVejNmUGRxeVZQYkpNR2RPWGFqTDBkaDBhVldSaHkxb2dDYmpadz0iLCJtYWMiOiI2NzUzZTg1ZTcyMDgxYmRlMGIzZmRkZDFlN2E4ZmNlZmU0YmZiNmFmNmRlYzEwMzE1NTEzNzcxODdiNmRiMzU0In0=';
        dd($crypt->decrypt($test_string));

});

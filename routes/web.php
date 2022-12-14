<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ListController;
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

/*
Route::get('/', function () {
    return view('welcome');
});
*/
//管理システム
Route::get('/', [AuthController::class, 'index'])->name('front.index');
Route::post('/login', [AuthController::class, 'login']);
//認可処理
Route::middleware(['auth'])->group(function () {
    Route::prefix('/shopping_list')->group(function () {
        Route::get('/list', [ListController::class, 'list']);
        Route::post('/register', [ListController::class, 'register']);
        Route::delete('/delete/{shopping_list_id}', [ListController::class, 'delete'])->whereNumber('shopping_list_id')->name('delete');
    });
    //
    Route::get('/logout', [AuthController::class, 'logout']);
});




//会員登録
Route::get('/user/register', [UserController::class, 'register'])->name('front.user.register');
Route::post('/user/register', [UserController::class, 'user'])->name('front.user.register.post');




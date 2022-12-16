<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\CompletedListController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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
        Route::post('/complete/{shopping_list_id}', [ListController::class, 'complete'])->whereNumber('shopping_list_id')->name('complete');
        Route::get('/completed_shopping_lists/list', [CompletedListController::class, 'list']);
    });
    
    //購入済み「買うもの」一覧
    
    
    //ログアウト
    Route::get('/logout', [AuthController::class, 'logout']);
});

//管理画面
Route::prefix('/admin')->group(function () {
    Route::get('', [AdminAuthController::class, 'index'])->name('admin.index');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/top', [AdminHomeController::class, 'top'])->name('admin.top');
        Route::get('/user/list', [AdminUserController::class, 'list'])->name('admin.user.list');
    });
    Route::get('/logout', [AdminAuthController::class, 'logout']);
    
});




//会員登録
Route::get('/user/register', [UserController::class, 'register'])->name('front.user.register');
Route::post('/user/register', [UserController::class, 'user'])->name('front.user.register.post');




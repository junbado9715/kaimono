<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginPostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterPostRequest;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Hash;
use App\Models\User as UserModel;

class UserController extends Controller
{
    /**
     * トップページ を表示する
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index');
    }
    
    /**
     * ログイン処理
     * 
     */
    public function register()
    {
       
       return view ('user.register');
       
    }
    
    public function user(UserRegisterPostRequest $request)
    {
        //validate済みのデータ取得
        $datum = $request->validated();
        
        try {
            //Hash化したpasswordの追加
              $datum['password'] = Hash::make($datum['password']);
            
            //データベースへの登録
            UserModel::create($datum);
        } catch(\Throwable $e) {
            //エラー処理
            echo $e->getMessage();
            exit;
        }
        
        //会員登録成功
        $request->session()->flash('front.user_register_success', true);
        
        //ログイン画面へリダイレクト
        return redirect('/');
    }
    
}
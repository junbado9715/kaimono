<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginPostRequest;
use App\Http\Requests\ListRegisterPostRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Shopping_list as Shopping_listModel;
use Illuminate\Http\Request;


class ListController extends Controller
{
    
     /**
     * ログイン処理
     * 
     */
    public function list()
    {
        //1ページ辺りのの表示アイテム数を設定
        $per_page = 2;
        
        //一覧の取得
        $list = Shopping_listModel::where('user_id', Auth::id())
                                    ->paginate($per_page);
        //
        return view('shopping_list.list', ['list' => $list]);
    }
    
    /**
     * 買うものの新規登録
     */
    public function register(ListRegisterPostRequest $request)
    {
         // validate済みのデータの取得
        $datum = $request->validated();
        
        //user_idの追加
        $datum['user_id'] = Auth::id();
        
        //テーブルへのインサート
        try {
            $r = Shopping_listModel::create($datum);
        } catch(\Throwable $e) {
            // XXX 本当はログに書く等の処理をする。今回は一端「出力する」だけ
            echo $e->getMessage();
            exit;
        }
        
        //買うものの登録成功
        $request->session()->flash('front.shopping_list_register_success', true);
        
        //
        return redirect('/shopping_list/list');
    }
    
    /**
     * 削除処理
     */
    public function delete(Request $request, $shopping_list_id)
    {
        //shopping_list_idのレコードを取得する
        $shopping_list = $this->getShopping_listModel($shopping_list_id);
        
        //買うものを削除する
        if ($shopping_list !== null) {
            $shopping_list->delete();
            $request->session()->flash('front.shopping_list_delete_success', true);
        }
        
        //一覧に遷移する
       return redirect('/shopping_list/list');
    }
}
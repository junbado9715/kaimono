<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginPostRequest;
use App\Http\Requests\ListRegisterPostRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Shopping_list as Shopping_listModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CompletedShopping_list as CompletedShopping_listModel;

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
    
    protected function getShopping_listModel($shopping_list_id)
    {
        // task_idのレコードを取得する
        $shopping_list = Shopping_listModel::find($shopping_list_id);
        if ($shopping_list === null) {
            return null;
        }
        // 本人以外のタスクならNGとする
        if ($shopping_list->user_id !== Auth::id()) {
            return null;
        }
        //
        return $shopping_list;
    }
    
    /**
     * タスクの完了
     */
    public function complete(Request $request, $shopping_list_id)
    {
        /*買うものをを完了テーブルに移動させる*/
        try {
            // トランザクション開始
            DB::beginTransaction();
            
        
            //shopping_list_idのレコードを取得する
            $shopping_list = $this->getShopping_listModel($shopping_list_id);
            if ($shopping_list === null) {
                //shopping_list_idが不正なのでトランザクション終了
                throw new \Exception('');
            }
            //shopping_lists側を削除する
            $shopping_list->delete();
            
            //completed_shopping_lists側にinsertする
            $dask_datum = $shopping_list->toArray();
            unset($dask_datum['created_at']);
            unset($dask_datum['updated_at']);
            $r = CompletedShopping_listModel::create($dask_datum);
            if ($r === null) {
                // insertで失敗したのでトランザクション終了
                throw new \Exception('');
            }
            
            //トランザクション終了
            DB::commit();
            //完了メッセージ出力
            $request->session()->flash('front.shopping_list_completed_success', true);
        } catch(\Throwable $e) {
            //トランザクション異常終了
            DB::rollBack();
            //完了失敗メッセージ出力
            $request->session()->flash('front.shopping_list_completed_failure', true);
        }
        
        //一覧に遷移する
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
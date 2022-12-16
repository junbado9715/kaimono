<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\CompletedShopping_list as CompletedShopping_listModel;
use App\Http\Controllers\CompletedListController;

class CompletedListController extends Controller
{
    public function list()
    {
         $per_page = 1;
         
        $list = CompletedShopping_listModel::where('user_id', Auth::id())
                         ->orderBy('created_at')
                         ->paginate($per_page);
        return view('shopping_list.completed_shopping_lists.list', ['list' => $list]);
    }
}
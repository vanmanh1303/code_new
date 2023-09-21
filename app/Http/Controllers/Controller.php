<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function checkRule_get($id)
    {
        $admin       = Auth::guard('admin')->user();
        if ($admin->is_master) {
            return true;
        }
        // $groupAdmin  = Quyen::find($admin->id_quyen);

        if ($admin->id_quyen == $id) {
            // $listRule    = explode(',', $groupAdmin->list_rule);
            return true;
        } else {
            return false;
        }
    }
}

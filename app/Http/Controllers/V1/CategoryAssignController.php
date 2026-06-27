<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryAssignController extends Controller
{
    //
    public function loadCreate(){
          return view('admin.dashboard.pages.product-assign.create');
    }
    public function Create(Request $request){
        $request->validate(
            [
                'product_id'=>"required|array|numeric",
                'product_id*'=>"required|numeric|exists;product_models",
                'category_id'=>""
            ]
        );

    }
}

<?php
namespace App\Api\Controllers\Admin;

use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index(Request $request)
    {
        return success(['aa' => 'bb']);
    }
}

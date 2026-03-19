<?php
namespace App\Api\Controllers\Admin;

use App\Api\Services\AdminAuthService;
use Illuminate\Http\Request;

class AdminAuthController extends BaseController
{
    /**
     * 菜单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function menuList(Request $request)
    {
        try {
            $params = $request->all();

            $data = AdminAuthService::menuList($params);

            return success($data);

        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }
}

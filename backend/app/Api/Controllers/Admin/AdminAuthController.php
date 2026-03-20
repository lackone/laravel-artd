<?php

namespace App\Api\Controllers\Admin;

use App\Api\Services\AdminAuthService;
use App\Models\AdminAuth;
use Illuminate\Http\Request;

class AdminAuthController extends BaseController
{
    /**
     * 菜单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $params = $request->all();
        try {
            $auth_list = AdminAuth::where(function ($query) use ($params) {
                if ($params) {
                    foreach ($params as $key => $value) {
                        if (in_array($key, ['title', 'path', 'component']) && $value != '') {
                            switch ($key) {
                                case 'title':
                                case 'path':
                                case 'component':
                                    $query->where($key, 'like', $value . '%');
                                    break;
                                default:
                                    $query->where($key, $value);
                                    break;
                            }
                        }
                    }
                }
            })->orderBy('sort', 'asc')->orderBy('id', 'asc')->get()->toArray();

            $menu_tree = AdminAuthService::makeMenuTree($auth_list);

            $menu_tree = AdminAuthService::transformMenu($menu_tree);

            return success($menu_tree);

        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 菜单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function menuList(Request $request)
    {
        $params = $request->all();
        try {
            $data = AdminAuthService::menuList($params);

            return success($data);

        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 新增或修改权限
     * @param Request $request
     */
    public function save(Request $request, AdminAuth $auth)
    {
        $params = $request->all();
        try {
            if (!$params['name']) {
                throw new \Exception('权限标识不能为空');
            }

            if ($auth['id']) {
                $auth->update($params);
            } else {
                AdminAuth::create($params);
            }

            return success();
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 删除
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $params = $request->all();
        try {
            $ids = $params['ids'] ?: '';

            if (!$ids) {
                throw new \Exception('ids不能为空');
            }

            $ids = explode(',', $ids) ?: [];

            AdminAuth::whereIn('id', $ids)->delete();

            return success();
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }
}

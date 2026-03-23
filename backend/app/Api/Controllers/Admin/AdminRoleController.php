<?php

namespace App\Api\Controllers\Admin;

use App\Models\AdminRole;
use Illuminate\Http\Request;

class AdminRoleController extends BaseController
{
    /**
     * 角色列表
     * @param Request $request
     */
    public function list(Request $request)
    {
        $params = $request->all();

        $query = AdminRole::where(function ($query) use ($params) {
            if ($params) {
                foreach ($params as $key => $value) {
                    if (in_array($key, ['name', 'start_time', 'end_time', 'status']) && $value != '') {
                        switch ($key) {
                            case 'name':
                                $query->where($key, 'like', $value . '%');
                                break;
                            case 'start_time':
                                $query->where('created', '>=', strtotime($value));
                                break;
                            case 'end_time':
                                $query->where('created', '<', strtotime($value) + 86400);
                                break;
                            default:
                                $query->where($key, $value);
                                break;
                        }
                    }
                }
            }
        })->orderBy('id', 'desc');

        $list = $query->paginate($params['limit'] ?? 10);

        return toList($list);
    }

    /**
     * 新增或修改角色
     * @param Request $request
     */
    public function save(Request $request, AdminRole $role)
    {
        $params = $request->all();
        try {
            if (!$params['name']) {
                throw new \Exception('角色名不能为空');
            }

            $auth_ids = array_unique(array_filter(explode(',', $params['auth_ids']) ?: []));
            sort($auth_ids);
            $params['auth_ids'] = implode(',', $auth_ids);

            if ($role['id']) {
                $role->update($params);
            } else {
                AdminRole::create($params);
            }

            return success();
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 删除角色
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

            AdminRole::whereIn('id', $ids)->delete();

            return success();
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }
}

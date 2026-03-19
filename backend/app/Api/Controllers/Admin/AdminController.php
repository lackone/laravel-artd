<?php
namespace App\Api\Controllers\Admin;

use App\Api\Services\AdminService;
use App\Models\Admin;
use App\Models\AdminRoleAssoc;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends BaseController
{
    /**
     * 列表
     * @param Request $request
     */
    public function list(Request $request)
    {
        $params = $request->all();

        $query = Admin::with(['roles:id,name'])->where(function ($query) use ($params) {
            if ($params) {
                foreach ($params as $key => $value) {
                    if (in_array($key, ['account', 'phone', 'email', 'start_time', 'end_time', 'status']) && $value != '') {
                        switch ($key) {
                            case 'account':
                            case 'phone':
                            case 'email':
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


        $list = $query->paginate($params['size'] ?? 10);

        return toList($list);
    }

    /**
     * 新增或修改用户
     * @param Request $request
     */
    public function save(Request $request, Admin $admin)
    {
        $params = $request->all();
        try {
            if (!$params['account']) {
                throw new \Exception('账号不能为空');
            }

            if ($params['password']) {
                $params['salt'] = Str::random(6);
                $params['password'] = AdminService::makePassword($params['password'], $params['salt']);
            } else {
                unset($params['password']);
            }

            if ($admin['id']) {
                if (Admin::where('account', $params['account'])->where('id', '<>', $admin['id'])->first()) {
                    throw new \Exception('已经存在相同账号');
                }

                $admin->update($params);
            } else {
                if (Admin::where('account', $params['account'])->first()) {
                    throw new \Exception('已经存在相同账号');
                }
                if (!$params['password']) {
                    throw new \Exception('密码不能为空');
                }
                Admin::create($params);
            }

            return success();
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function info(Request $request)
    {
        $params = $request->all();
        try {
            $data = AdminService::info($params);

            return success($data);

        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 分配角色
     * @param Request $request
     */
    public function setRole(Request $request, Admin $admin)
    {
        $params = $request->all();
        try {
            if (!$admin['id']) {
                throw new \Exception('请选择用户');
            }

            AdminRoleAssoc::where('admin_id', $admin['id'])->delete();

            $insert = [];
            $role_ids = explode(',', $params['role_ids']) ?: [];
            $now = time();
            if ($role_ids) {
                foreach ($role_ids as $role_id) {
                    $insert[] = ['admin_id' => $admin['id'], 'role_id' => $role_id, 'created' => $now, 'updated' => $now];
                }
            }
            $insert && AdminRoleAssoc::insert($insert);

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

            Admin::whereIn('id', $ids)->delete();

            return success();
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }
}

<?php

namespace App\Api\Services;

use App\Models\Admin;
use App\Models\AdminAuth;
use App\Models\AdminRole;
use App\Models\AdminRoleAssoc;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminService
{
    /**
     * 登录
     * @param $params
     * @return string[]
     * @throws \Exception
     */
    public static function login($params)
    {
        $account = $params['account'];
        $password = $params['password'];

        $admin = Admin::where('account', $account)->first();
        if (!$admin) {
            throw new \Exception('账号不存在');
        }
        if ($admin['status'] == Admin::STATUS_DISABLE) {
            throw new \Exception('账号禁用');
        }
        $pwd = self::makePassword($password, $admin['salt']);
        if ($pwd != $admin['password']) {
            throw new \Exception('账号密码错误');
        }

        $token = self::createTokenWithTTL($admin, 1440);

        return [
            'token' => 'Bearer ' . $token,
        ];
    }

    /**
     * 用户信息
     * @param $params
     * @return []|array
     * @throws \Exception
     */
    public static function info($params)
    {
        $admin_id = $params['admin_id'];

        $admin = Admin::find($admin_id);
        if (!$admin) {
            throw new \Exception('账号不存在');
        }

        unset($admin['password'], $admin['salt'], $admin['is_super']);

        return array_merge($admin->toArray(), [
            'userId' => $admin['id'],
            'userName' => $admin['real_name'] ?: $admin['account'],
        ]);
    }

    /**
     * 创建token
     * @param $model
     * @param $minutes
     * @return mixed
     */
    public static function createTokenWithTTL($model, $minutes = 120)
    {
        $payload = [
            'exp' => now()->addMinutes($minutes)->timestamp,
        ];

        return JWTAuth::customClaims($payload)->fromUser($model);
    }

    /**
     * 生成密码
     * @param $password
     * @param $salt
     * @return string
     */
    public static function makePassword($password, $salt)
    {
        return md5(md5($salt) . $password);
    }

    /**
     * 获取用户角色ID
     * @param $admin_id
     * @return mixed
     */
    public static function getRoleIds($admin_id)
    {
        return AdminRoleAssoc::where('admin_id', $admin_id)->pluck('role_id')->toArray();
    }

    /**
     * 获取权限ID
     * @param $role_ids
     * @return array
     */
    public static function getAuthIds($role_ids = [])
    {
        $auth_ids = AdminRole::whereIn('id', $role_ids)
            ->where('status', AdminRole::STATUS_ENABLE)
            ->pluck('auth_ids')
            ->toArray();
        $data = [];
        if ($auth_ids) {
            foreach ($auth_ids as $auth_id) {
                $data = array_merge($data, explode(',', $auth_id) ?: []);
            }
        }
        return array_unique(array_filter($data));
    }

    /**
     * 获取权限列表
     * @param $auth_ids
     * @return mixed
     */
    public static function getAuthList($auth_ids = [], $status = 0, $type = 0)
    {
        return AdminAuth::whereIn('id', $auth_ids)
            ->where(function ($query) use ($status, $type) {
                $status && $query->where('is_enable', $status);
                $type && $query->where('type', $type);
            })
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * 获取所有权限列表
     * @return mixed
     */
    public static function getAllAuthList($status = 0, $type = 0, $select = ['*'])
    {
        return AdminAuth::select($select)->where(function ($query) use ($status, $type) {
            $status && $query->where('is_enable', $status);
            $type && $query->where('type', $type);
        })
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * 获取权限列表
     * @param $admin
     * @return mixed
     */
    public static function getAuthListByAdmin($admin, $status = 0, $type = 0)
    {
        if ($admin['is_super'] == Admin::IS_SUPER_YES) {
            $auth_list = self::getAllAuthList($status, $type);
        } else {
            $role_ids = self::getRoleIds($admin['id']);
            $auth_ids = self::getAuthIds($role_ids);
            $auth_list = self::getAuthList($auth_ids, $status, $type);
        }

        return $auth_list;
    }
}

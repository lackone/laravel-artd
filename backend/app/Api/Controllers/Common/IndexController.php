<?php

namespace App\Api\Controllers\Common;

use App\Api\Services\AdminAuthService;
use App\Api\Services\AdminService;
use App\Api\Services\UploadService;
use App\Models\AdminAuth;
use App\Models\AdminRole;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    /**
     * 文件上传
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('file')) {

                $res = UploadService::uploadOne($request->file);

                return success($res);
            }
            return error('请重新上传文件');
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 角色列表
     * @param Request $request
     */
    public function roleList(Request $request)
    {
        $data = AdminRole::select('id', 'name')->where('status', AdminRole::STATUS_ENABLE)->get()->toArray();
        return success($data);
    }

    /**
     * 权限列表
     * @param Request $request
     */
    public function authList(Request $request)
    {
        $auth_list = AdminService::getAllAuthList(AdminAuth::STATUS_ENABLE, 0, ['id as value', 'title as label', 'pid']);
        return success(makeTree($auth_list, 'value'));
    }
}

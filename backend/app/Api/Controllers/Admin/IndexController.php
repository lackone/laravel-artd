<?php
namespace App\Api\Controllers\Admin;

use App\Api\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IndexController extends BaseController
{
    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $params = $request->all();

            $valid = Validator::make($params, [
                'account' => 'required',
                'password' => 'required',
            ], [
                'account.required' => '账号必填',
                'password.required' => '密码必填',
            ]);

            if ($valid->fails()) {
                throw new \Exception($valid->errors()->first());
            }

            $data = AdminService::login($params);

            return success($data);

        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }
}

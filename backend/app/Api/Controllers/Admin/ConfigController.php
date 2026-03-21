<?php

namespace App\Api\Controllers\Admin;

use App\Api\Services\ConfigService;
use Illuminate\Http\Request;

class ConfigController extends BaseController
{
    /**
     * 获取配置
     * @param Request $request
     */
    public function getConfig(Request $request)
    {
        $params = $request->all();
        try {
            $key = $params['key'];

            $data = ConfigService::getConfigByKey($key);

            return success($data);
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    /**
     * 设置配置
     * @param Request $request
     */
    public function setConfig(Request $request)
    {
        $params = $request->all();
        try {
            $key = $params['key'];

            unset($params['key'], $params['admin_id']);

            $data = ConfigService::setConfigByKey($key, $params);

            return success($data);
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }
}

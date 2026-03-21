<?php

namespace App\Api\Services;

use App\Models\Config;

class ConfigService
{
    const ImageFields = [
        'website_logo',
        'website_favicon',
    ];

    /**
     * 获取配置
     * @param $key
     * @return mixed
     */
    public static function getConfigByKey($key)
    {
        $data = Config::where('key', $key)->pluck('value', 'sub_key')->toArray();

        foreach ($data as $k => &$v) {
            if (in_array($k, self::ImageFields)) {
                $v = imgUrl($v);
            }
        }
        unset($v);

        return $data;
    }

    /**
     * 设置配置
     * @param $key
     * @param $params
     */
    public static function setConfigByKey($key, $params)
    {
        $insert = [];

        if ($params) {
            foreach ($params as $sub_key => $value) {
                if (in_array($sub_key, self::ImageFields)) {
                    $value = removeDomainPrefix($value);
                }
                $insert[] = [
                    'key' => $key,
                    'sub_key' => $sub_key,
                    'value' => $value,
                ];
            }
        }

        $insert && Config::upsert($insert, ['key', 'sub_key', 'value']);

        return [];
    }
}

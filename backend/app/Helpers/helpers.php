<?php

if (!function_exists('ps')) {
    /**
     * 打印sql
     */
    function ps()
    {
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            $bindings = $query->bindings;
            $sql = $query->sql;
            foreach ($bindings as $replace) {
                $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                $sql = preg_replace('/\?/', $value, $sql, 1);
            }
            dump($sql);
        });
    }
}

if (!function_exists('success')) {
    /**
     * 成功
     */
    function success($data = [], $msg = 'success', $code = 200, $http_code = 200)
    {
        if (is_array($data) && empty($data)) {
            $data = (object)$data;
        }
        $result = [
            'code' => $code,
            'msg' => $msg ?: 'success',
            'data' => $data,
        ];
        return response()->json($result, $http_code);
    }
}

if (!function_exists('toList')) {
    /**
     * 返回列表
     */
    function toList(\Illuminate\Pagination\LengthAwarePaginator $list, $totalRow = [])
    {
        $list = $list->toArray();
        $result = [
            'code' => 200,
            'msg' => 'success',
            'data' => [
                'current' => $list['current_page'] ?: 1,
                'records' => $list['data'] ?: [],
                'size' => $list['per_page'] ?: 10,
                'total' => $list['total'] ?: 0,
            ],
        ];
        return response()->json($result, 200);
    }
}

if (!function_exists('error')) {
    /**
     * 错误
     */
    function error($msg = 'error', $data = [], $code = 500, $http_code = 200)
    {
        if (is_array($data) && empty($data)) {
            $data = (object)$data;
        }
        $result = [
            'code' => $code,
            'msg' => $msg ?: 'error',
            'data' => $data,
        ];
        return response()->json($result, $http_code);
    }
}

if (!function_exists('getRealIp')) {
    /**
     * 获取真实IP
     * @return mixed|string
     */
    function getRealIp()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim(current($ip));
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '0.0.0.0';
        }
        return $ip;
    }
}

if (!function_exists('getDeviceType')) {
    /**
     * 获取设备类型
     * @return string
     */
    function getDeviceType($user_agent = '')
    {
        // 获取 User-Agent 字符串
        $user_agent = $user_agent ?: $_SERVER['HTTP_USER_AGENT'];
        $device_type = 'Unknown';

        // 检查操作系统和设备类型
        if (preg_match('/android/i', $user_agent)) {
            $device_type = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $user_agent)) {
            $device_type = 'IOS';
        } elseif (preg_match('/windows/i', $user_agent)) {
            $device_type = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $device_type = 'Mac';
        } elseif (preg_match('/linux/i', $user_agent)) {
            $device_type = 'Linux';
        }

        return $device_type;
    }
}

if (!function_exists('makeTree')) {
    /**
     * 生成树型数据
     * @param array $items 数组数据
     * @param string $id ID字段名
     * @param string $pid 父级ID字段名
     * @param string $son 子类数组下标名
     */
    function makeTree($items, $id = 'id', $pid = 'pid', $son = 'children')
    {
        $tree = [];
        $tmp = [];
        foreach ($items as $item) {
            $tmp[$item[$id]] = $item;
        }
        foreach ($items as $item) {
            if (isset($tmp[$item[$pid]])) {
                $tmp[$item[$pid]][$son][] = &$tmp[$item[$id]];
            } else {
                $tree[] = &$tmp[$item[$id]];
            }
        }
        unset($tmp);
        return $tree;
    }
}

if (!function_exists('jsonEncode')) {
    /**
     * json编码
     */
    function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('getWeek')) {
    /**
     * 获取指定时间所在的周一与周日
     */
    function getWeek($today = 0)
    {
        !$today && $today = date('Y-m-d');
        return [
            date('Y-m-d', strtotime('monday this week', strtotime($today))),
            date('Y-m-d', strtotime('sunday this week', strtotime($today)))
        ];
    }
}

if (!function_exists('isJson')) {
    /**
     * 判断是不是json
     * @param $string
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('gmtIso8601')) {
    /**
     * 生成ISO时间
     * @param int $time
     * @return string
     */
    function gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }
}

if (!function_exists('secondsFormat')) {
    /**
     * 把秒格式化成可读的
     */
    function secondsFormat($seconds, $displayUnits = ['days', 'hours', 'minutes', 'seconds'])
    {
        // 确保输入是整数
        $seconds = (int)$seconds;

        // 计算天、小时、分钟和秒
        $days = floor($seconds / 86400); // 一天有 86400 秒
        $hours = floor(($seconds % 86400) / 3600); // 剩余小时
        $minutes = floor((($seconds % 86400) % 3600) / 60); // 剩余分钟
        $remainingSeconds = ($seconds % 86400) % 3600 % 60; // 剩余秒

        // 构造可读字符串
        $parts = [];
        if (in_array('days', $displayUnits) && $days > 0) {
            $parts[] = $days . '天';
        }
        if (in_array('hours', $displayUnits) && $hours > 0) {
            $parts[] = $hours . '时';
        }
        if (in_array('minutes', $displayUnits) && $minutes > 0) {
            $parts[] = $minutes . '分';
        }
        if (in_array('seconds', $displayUnits) && $remainingSeconds > 0) {
            $parts[] = $remainingSeconds . '秒';
        }

        // 如果没有任何单位被显示，默认返回 ""
        return !empty($parts) ? implode('', $parts) : '';
    }
}

if (!function_exists('expire')) {
    /**
     * 根据给定过期时间生成随机一个时间
     * @param $time
     * @return array|float|int|mixed|string|string[]
     */
    function expire($time = '5m')
    {
        $second_maps = [
            'M' => 30 * 24 * 60 * 60,
            'd' => 24 * 60 * 60,
            'H' => 60 * 60,
            'm' => 1 * 60
        ];
        foreach ($second_maps as $key => $val) {
            $time = str_replace($key, "*{$val}", $time);
        }

        eval("\$time = $time;");

        $rank_time = $time / 5;

        return $time - $rank_time + mt_rand(0, $rank_time * 2);
    }
}

if (!function_exists('numberFormat')) {
    /**
     * 格式化数字
     * @param $num
     * @param $decimals
     * @param $decimal_separator
     * @param $thousands_separator
     * @return string
     */
    function numberFormat($num, $decimals = 2, $decimal_separator = '.', $thousands_separator = '')
    {
        return number_format($num, $decimals, $decimal_separator, $thousands_separator);
    }
}

if (!function_exists('getCacheKey')) {
    /**
     * 获取缓存key
     * @param $class_name
     * @return string
     */
    function getCacheKey($class_name = '')
    {
        if ($class_name) {
            return class_basename($class_name) . ':';
        }
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1] ?? null;
        if (isset($caller['class'])) {
            $class = $caller['class'];
            $basename = class_basename($class);
            return $basename . ':';
        }
        return 'global:';
    }
}

if (!function_exists('delCache')) {
    /**
     * 删除缓存
     * @param $key
     * @param $class_name
     * @return bool
     */
    function delCache($key, $class_name = '')
    {
        return \Illuminate\Support\Facades\Cache::forget(getCacheKey($class_name) . $key);
    }
}

if (!function_exists('batchDelCache')) {
    /**
     * 批量删除缓存
     * @param $key
     * @param $class_name
     * @return bool
     */
    function batchDelCache($key, $class_name = '')
    {
        $redis = \Illuminate\Support\Facades\Redis::connection('cache');
        $redis_prefix = config('database.redis.options.prefix');
        $cache_prefix = config('cache.prefix');
        $match = $redis_prefix . $cache_prefix . rtrim(getCacheKey($class_name) . $key, ':') . ':*';

        $loop = 0;
        try {
            $cursor = null;
            do {
                $loop++;
                if ($loop > 9999) {
                    break; //防止死循环
                }
                $result = $redis->scan($cursor, [
                    'match' => $match,
                    'count' => 500,
                ]);

                if ($result === false) {
                    break; // 扫描完成
                }

                [$cursor, $keys] = $result;

                // 处理匹配的 key
                if ($keys) {
                    array_walk($keys, function (&$item) use ($redis_prefix) {
                        $item = str_replace($redis_prefix, '', $item);
                    });
                    $redis->del($keys);
                }

            } while ($cursor !== 0);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('imgUrl')) {
    /**
     * 获取图片的URL
     * @param $path
     */
    function imgUrl($path, $resize = 0)
    {
        if (!$path) {
            return '';
        }

        $url = '//' . preg_replace('/^https?:\/\//', '', asset($path));

        return $url;
    }
}

if (!function_exists('removeDomainPrefix')) {
    /**
     * 去除域名前缀
     * @param $url
     */
    function removeDomainPrefix($url)
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        if (stripos($path, '/uploads/') === 0) {
            return $path;
        }

        $pos = stripos($url, '/uploads/');

        if ($pos !== false) {
            return substr($url, $pos);
        }

        return $url;
    }
}

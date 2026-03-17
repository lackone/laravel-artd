<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redis;
use Cache;

class BaseModel extends Model
{
    //use SoftDeletes;

    const CREATED_AT = 'created';

    const UPDATED_AT = 'updated';

    const DELETED_AT = 'deleted';

    protected $connection = 'mysql';

    protected $guarded = ['_token'];

    // 缓存时间数
    const CACHE_ONE_MONTH = 30 * 24 * 60 * 60; //1月
    const CACHE_ONE_DAY = 24 * 60 * 60; //1天
    const CACHE_ONE_HOUR = 60 * 60; //1小时
    const CACHE_ONE_MINUTE = 1 * 60; //1分钟

    /**
     * 可空字段
     *
     * @var array
     */
    public $nullable = [];

    /**
     * 指示模型是否主动维护时间戳。
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 模型日期字段的存储格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 序列化日期
     *
     * @param DateTimeInterface $date
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }

    /**
     * 获取列表
     * @param $key
     * @param $value
     * @param $where
     * @return array
     */
    protected function optionList($key, $value, $where = [])
    {
        if (stripos($value, ',') === false) {
            return $this->where($where)->pluck($value, $key)->toArray();
        }

        $list = $this->where($where)->get();
        $data = [];
        if ($list) {
            $value = explode(',', $value) ?: [];
            foreach ($list as $item) {
                if ($value) {
                    foreach ($value as $v) {
                        $data[$item[$key]] .= $item[$v] . '-';
                    }
                }
                $data[$item[$key]] = rtrim($data[$item[$key]], '-');
            }
        }
        return $data;
    }

    /**
     * 获取指定ID的列表
     * @param $ids
     * @param $key
     * @param $field
     * @return array
     */
    protected function listByIds($ids, $key = 'id', $field = '*', $lock = false, $with = [], $with_trashed = false)
    {
        $ids = array_unique(array_filter($ids));
        if (!$ids) {
            return [];
        }
        $query = $this->with($with)->select(DB::raw($field))->whereIn($key, $ids);
        if ($lock) {
            $query->lockForUpdate();
        }
        if ($with_trashed) {
            $query->withTrashed();
        }
        $list = $query->get();
        $data = [];
        if ($list) {
            foreach ($list as $value) {
                $data[$value[$key]] = $value;
            }
        }
        return $data;
    }

    /**
     * 根据给定过期时间生成随机一个时间
     * @param string $time M、月，d、天，H、小时，m、分钟
     * @return int
     */
    protected function expire($time = '5m')
    {
        $second_maps = [
            'M' => self::CACHE_ONE_MONTH,
            'd' => self::CACHE_ONE_DAY,
            'H' => self::CACHE_ONE_HOUR,
            'm' => self::CACHE_ONE_MINUTE
        ];
        foreach ($second_maps as $key => $val) {
            $time = str_replace($key, "*{$val}", $time);
        }

        eval("\$time = $time;");

        $rank_time = $time / 5;

        return $time - $rank_time + mt_rand(0, $rank_time * 2);
    }

    /**
     * 获取当前缓存key
     */
    protected function getCacheKey()
    {
        return getCacheKey(static::class);
    }

    /**
     * 删除缓存
     */
    protected function delCache($key)
    {
        return delCache($key, static::class);
    }

    /**
     * 批量删除缓存下所有子缓存
     */
    protected function batchDelCache($key)
    {
        return batchDelCache($key, static::class);
    }
}

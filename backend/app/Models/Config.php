<?php

namespace App\Models;

class Config extends BaseModel
{
    /**
     * 获取孩子
     */
    public function children()
    {
        return $this->hasMany(Config::class, 'pid', 'id');
    }
}

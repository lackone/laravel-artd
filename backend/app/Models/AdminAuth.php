<?php

namespace App\Models;

class AdminAuth extends BaseModel
{
    //状态(-1:禁用,1:启用)
    const STATUS_DISABLE = -1;
    const STATUS_ENABLE = 1;

    //类型(1:菜单,2:按钮)
    const TYPE_MENU = 1;
    const TYPE_BUTTON = 2;

    /**
     * 获取孩子
     */
    public function children()
    {
        return $this->hasMany(AdminAuth::class, 'pid', 'id');
    }
}

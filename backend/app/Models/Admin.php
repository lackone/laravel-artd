<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends BaseModel implements JWTSubject
{
    //状态(-1:禁用,1:启用)
    const STATUS_DISABLE = -1;
    const STATUS_ENABLE = 1;

    //超级管理员(-1:否,1:是)
    const IS_SUPER_YES = 1;
    const IS_SUPER_NO = -1;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'account' => $this->account,
        ];
    }

    /**
     * 用户所拥有的角色
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_assocs', 'admin_id', 'role_id');
    }

    /**
     * 图像
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? imgUrl($value) : '',
            set: fn($value) => $value ? removeDomainPrefix($value) : '',
        );
    }
}

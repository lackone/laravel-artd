<?php

namespace App\Models;

class AdminRole extends BaseModel
{
    //状态(-1:禁用,1:启用)
    const STATUS_DISABLE = -1;
    const STATUS_ENABLE = 1;
}

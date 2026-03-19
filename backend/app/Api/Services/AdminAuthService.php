<?php

namespace App\Api\Services;

use App\Models\Admin;
use App\Models\AdminAuth;

class AdminAuthService
{
    public static function menuList($params)
    {
        $admin_id = $params['admin_id'];
        $admin = Admin::find($admin_id);
        if (!$admin) {
            throw new \Exception('账号不存在');
        }

        $auth_list = AdminService::getAuthListByAdmin($admin, AdminAuth::STATUS_ENABLE, AdminAuth::TYPE_MENU);

        $menu_tree = self::makeMenuTree($auth_list);

        return $menu_tree;
    }

    /**
     *
     * @param $auth_list
     * @return array
     */
    public static function makeMenuTree($auth_list)
    {
        $tree = [];
        if ($auth_list) {
            foreach ($auth_list as $auth) {
                $tree[] = [
                    'id' => $auth['id'],
                    'path' => $auth['path'],
                    'name' => $auth['name'],
                    'component' => $auth['component'],
                    'pid' => $auth['pid'],
                    'meta' => [
                        'title' => $auth['title'],
                        'icon' => $auth['icon'],
                        'showBadge' => $auth['show_badge'] == 1,
                        'showTextBadge' => $auth['show_text_badge'],
                        'isHide' => $auth['is_hide'] == 1,
                        'isHideTab' => $auth['is_hide_tab'] == 1,
                        'link' => $auth['link'],
                        'isIframe' => $auth['is_iframe'] == 1,
                        'keepAlive' => $auth['keepalive'] == 1,
                        'fixedTab' => $auth['fixed_tab'] == 1,
                        'isFullPage' => $auth['is_full_page'] == 1,
                        'activePath' => $auth['active_path'],
                    ],
                ];
            }
        }
        return makeTree($tree);
    }
}

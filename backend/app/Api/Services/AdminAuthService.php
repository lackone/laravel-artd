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

        $auth_list = AdminService::getAuthListByAdmin($admin, 0, 0);

        $menu_tree = self::makeMenuTree($auth_list);

        $menu_tree = self::transformMenu($menu_tree);

        return $menu_tree;
    }

    /**
     * 转换菜单树：将 meta.type == 2 的子节点提取为 authList，保留 meta.type == 1 的子菜单
     * @param array $menuList 菜单列表
     * @return array 转换后的菜单列表
     */
    public static function transformMenu(array $menuList): array
    {
        $result = [];

        foreach ($menuList as $item) {
            $newItem = $item;

            // 如果有 children 且是数组
            if (!empty($newItem['children']) && is_array($newItem['children'])) {
                $authList = [];          // 存放 type=2 的按钮权限
                $submenuChildren = [];   // 存放 type=1 的子菜单（保留）

                foreach ($newItem['children'] as $child) {
                    // 判断是否为按钮权限项（type == 2）
                    if (isset($child['meta']['type']) && $child['meta']['type'] == AdminAuth::TYPE_BUTTON) {
                        $authList[] = [
                            'id' => $child['id'],
                            'pid' => $child['meta']['pid'] ?? 0,
                            'sort' => $child['meta']['sort'] ?? 0,
                            'title' => $child['meta']['title'] ?? '',
                            'authMark' => $child['name'] ?? '', // 可改为 $child['path'] 若需
                            'created' => (int)$child['meta']['created'],
                            'updated' => (int)$child['meta']['updated'],
                            'type' => (int)$child['meta']['type'],
                            'isEnable' => $child['meta']['isEnable'],
                        ];
                    } else {
                        // 否则视为子菜单（type == 1 或其他），递归处理后保留
                        $submenuChildren[] = self::transformMenu([$child])[0];
                    }
                }

                $newItem['meta']['authList'] = $authList;

                $newItem['children'] = $submenuChildren;
            }

            $result[] = $newItem;
        }

        return $result;
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
                        'isEnable' => $auth['is_enable'] == 1,
                        'created' => (int)$auth['created'],
                        'updated' => (int)$auth['updated'],
                        'type' => (int)$auth['type'],
                        'pid' => (int)$auth['pid'],
                        'sort' => (int)$auth['sort'],
                    ],
                ];
            }
        }
        return makeTree($tree);
    }
}

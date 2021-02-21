<?php

/**
 * Created by PhpStorm.
 * User: orginly
 * Date: 2021/2/12
 * Time: 17:24
 */

namespace app\admin\model;

use app\common\model\CSysAdmin;

class SysAdmin extends CSysAdmin
{
    /**
     * Notes:获取系统用户
     * @param $page
     * @param $pageSize
     * @param $username
     * @param $status
     * @param $dateran
     * @param $sortField
     * @param $sortOrder
     * @return array|null
     */
    public static function getSysUserList($page, $pageSize, $username, $status, $dateran, $sortField, $sortOrder){
        try {
            $where = [
                ['del_time', '=', 0]
            ];
            if (!empty($dateran)) {
                $where[] = ['create_time', '=', get_dateran($dateran)];
            }
            if ($status != -1) {
                $where[] = ['status', '=', $status];
            }
            if (!empty($username)) {
                $where[] = ['username', '=', $username];
            }
            if (!empty($sortField) && !empty($sortOrder)) {
                $order[$sortField] = substrAssign($sortOrder, 'end');
            } else {
                $order['create_time'] = 'desc';
            }
            $list = self::where($where)
                ->order($order)
                ->paginate([
                    'list_rows' => $pageSize,
                    'page'      => $page
                ]);
            return paginateToAnt($list);
        } catch (\Exception $ex) {
            setErrLog('SysAdmin >  > '.paramToString(), $ex);
            return null;
        }
    }

    /**
     * Notes:通过用户名获取系统用户信息
     * @param string $username
     * @return array
     */
    public static function getSysUserInfoByUsername(string $username){
        try {
            $where = [
                ['username', '=', $username],
                ['del_time', '=', 0]
            ];
            $result = self::where($where)
                ->find();
            return toArray($result);
        } catch (\Exception $ex) {
            setErrLog('SysAdmin > getSysUserInfoByUsername > '.paramToString(), $ex);
            return [];
        }
    }

}

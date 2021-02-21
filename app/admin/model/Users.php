<?php

/**
 * 用户模型层
 * User: orginly
 * Date: 2021/2/10
 * Time: 13:54
 */
declare(strict_types = 1);

namespace app\admin\model;

use app\common\model\CUsers;

class Users extends CUsers
{
    /**
     * Notes:获取用户列表
     * @param int $page [页码]
     * @param int $pageSize [数量]
     * @param string $keyword [用户名|手机号|邮箱]
     * @param int $status [1正常、0禁用]
     * @param array $dateran [日期]
     * @param string $sortField
     * @param string $sortOrder
     * @return array
     */
    public static function getUserList(int $page, int $pageSize, string $keyword = '', int $status = -1, array $dateran = [], string $sortField = '', string $sortOrder = ''){
        try {
            $where = [
                ['del_time', '=', 0]
            ];
            if (!empty($keyword)) {
                $where[] = ['username|mobile|email', 'like', '%'.$keyword.'%'];
            }
            if ($status != -1) {
                $where[] = ['status', '=', $status];
            }
            if (!empty($dateran)){
                $where[] = ['create_time','between',get_dateran($dateran)];
            }
            $order = [];
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
            setErrLog('Users > getUserList > '.paramToString($page, $pageSize, $keyword, $status, $dateran), $ex);
            return [];
        }
    }

    /**
     * Notes:编辑用户信息保存
     * @param $id
     * @param $mobile [手机号]
     * @param string $nickname
     * @return Users|null|boolean
     */
    public static function editUser($id, $mobile, $nickname = ''){
        try {
            $data   = [
                'mobile'      => $mobile,
                'nickname'    => $nickname,
                'update_time' => msectime()
            ];
            return self::where('id', $id)->update($data);
        } catch (\Exception $ex) {
            setErrLog('Users > editUser > '.paramToString($id, $mobile, $nickname), $ex);
            return false;
        }
    }
}

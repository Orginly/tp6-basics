<?php

/**
 * Created by PhpStorm.
 * User: orginly
 * Date: 2021/2/10
 * Time: 16:01
 */


namespace app\admin\logic;

use app\admin\model\Users;
use app\common\logic\CUsersLogic;

class UsersLogic
{
    /**
     * Notes:获取用户列表
     * @param int $page [页码]
     * @param int $pageSize [数量]
     * @param string $keyword [用户名|手机号|邮箱]
     * @param int $status [1正常、0禁用]
     * @param array $dateran [日期]
     * @param string $sortField [排序字段]
     * @param string $sortOrder [排序]
     * @return array
     */
    public static function getUserList(int $page, int $pageSize, string $keyword, int $status, array $dateran, string $sortField, string $sortOrder){
        try {
            $list = Users::getUserList($page, $pageSize, $keyword, $status, $dateran, $sortField, $sortOrder);
            foreach ($list['data'] as &$item) {
                format_time($item['login_time']);
                format_time($item['create_time']);
                format_time($item['update_time']);
            }
            unset($item);
            return return_success('ok', $list);
        } catch (\Exception $ex) {
            setErrLog('UsersLogic > getUserList > '.paramToString($page, $pageSize, $keyword, $status, $dateran, $sortField, $sortOrder), $ex);
            return return_fail(8003);
        }
    }

    /**
     * Notes:添加用户
     * @param string $mobile [手机号]
     * @param string $mobile_area [区域码]
     * @param string $password [密码]
     * @param string $nickname
     * @return array
     */
    public static function addUser(string $mobile, string $mobile_area, string $password, string $nickname = ''){
        try {
            //验证手机号是否存在
            $u_info = Users::getInfoByMobile($mobile);
            if (!empty($u_info)) {
                return return_fail(1102);//手机号已被占用
            }
            $result = CUsersLogic::registerUser($mobile, $mobile_area, $password, $nickname);
            if (!$result) {
                return return_fail(1119);
            }
            return return_success('注册成功');
        } catch (\Exception $ex) {
            setErrLog('UsersLogic > addUser > '.paramToString($mobile, $mobile_area, $password), $ex);
            return return_fail(8003);
        }
    }


    /**
     * Notes: 编辑用户信息
     * @param $id
     * @param $mobile [手机号]
     * @param string $nickname [昵称]
     * @return array
     */
    public static function editUser($id, $mobile, $nickname = ''){
        try {
            $u_info = Users::getInfoById($id);
            if ($u_info['mobile'] != $mobile) {
                //验证手机号是否可用
                $code = CUsersLogic::checkMobile($mobile);
                if ($code !== 0) {
                    return return_fail($code);
                }
            } elseif ($u_info['mobile'] == $mobile && $u_info['nickname'] == $nickname) {
                return return_success('未做更改');
            }

            $result = Users::editUser($id, $mobile, $nickname);
            if (!$result) {
                return return_fail(8003, '编辑用户失败');
            }
            return return_success('编辑用户成功');

        } catch (\Exception $ex) {
            setErrLog('UsersLogic > editUser > '.paramToString($id, $mobile, $nickname), $ex);
            return return_fail(8003);
        }
    }

    /**
     * Notes:软删除用户
     * @param $id
     * @return array
     */
    public static function delUser($id){
        try {
            $u_info = Users::getInfoById($id);
            if (empty($u_info)) {
                return return_fail(1001);//用户不存在
            }
            $result = Users::softDeleteById($id);
            if (!$result) {
                return return_fail(8003, '删除失败');
            }
            return return_success('删除成功');
        } catch (\Exception $ex) {
            setErrLog('UsersLogic > delUser > '.paramToString($id), $ex);
            return return_fail(8003);
        }
    }
}

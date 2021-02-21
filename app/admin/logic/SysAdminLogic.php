<?php

/**
 * 系统配置业务层
 * User: orginly
 * Date: 2021/2/12
 * Time: 15:54
 */

namespace app\admin\logic;

use app\admin\model\SysAdmin;

class SysAdminLogic
{
    /**
     * Notes:获取系统用户
     * @param int $page
     * @param int $pageSize
     * @param string $username
     * @param int $status
     * @param array $dateran
     * @param string $sortField
     * @param string $sortOrder
     * @return array|null
     */
    public static function getSysUserList(int $page, int $pageSize, string $username, int $status, array $dateran, string $sortField, string $sortOrder){
        try {
            $list = SysAdmin::getSysUserList($page, $pageSize, $username, $status, $dateran, $sortField, $sortOrder);
            foreach ($list['data'] as &$item) {
                format_time($item['create_time']);
            }
            return return_success('ok', $list);
        } catch (\Exception $ex) {
            setErrLog('SysAdminLogic > getUserList > '.paramToString($page, $pageSize, $username, $status, $dateran, $sortField, $sortOrder), $ex);
            return null;
        }
    }


    /**
     * Notes:添加系统用户
     * @param string $username [用户名]
     * @param string $password [密码]
     * @param string $nickname [昵称]
     * @param string $image [头像]
     * @return array
     */
    public static function addSysUser(string $username, string $password, string $nickname = '', string $image = ''){
        try {
            $s_info = SysAdmin::getSysUserInfoByUsername($username);
            if (!empty($s_info)) {
                return return_fail(1007);//用户名已存在
            }
            $data   = [
                'username' => $username,
                'nickname' => $nickname,
                'image'    => $image,
                'password' => sp_password($password)
            ];
            $result = SysAdmin::create($data);
            if (!$result) {
                return return_fail(1119);// 注册失败
            }
            return return_success('添加成功');
        } catch (\Exception $ex) {
            setErrLog('SysAdminLogic > addUser > '.paramToString($username, $password, $nickname, $image), $ex);
            return return_fail(8003);
        }
    }

    /**
     * Notes:
     * @param $id
     * @param $nickname [昵称]
     * @param $image [头像]
     * @return array
     */
    public static function editSysUser($id, $nickname, $image){
        try {
            $s_info = SysAdmin::getInfoById($id);
            if (empty($s_info)) {
                return return_fail(1001);//用户不存在
            }
            if ($s_info['nickname'] == $nickname && $s_info['image'] == $image) {
                return return_success('未做更改');
            }
            $data = [
                'nickname'    => $nickname,
                'update_time' => msectime()
            ];
            if (!empty($image)) {
                $data['image'] = $nickname;
            }
            $result = SysAdmin::where('id', $id)->save($data);
            if (!$result) {
                return return_fail(8003, '编辑失败');
            }
            return return_success('编辑成功');
        } catch (\Exception $ex) {
            setErrLog('SysAdminLogic > editSysUser > '.paramToString($id, $nickname, $image, $image), $ex);
            return return_fail(8002);
        }
    }

    /**
     * Notes:删除系统用户
     * @param int $id
     * @return array
     */
    public static function delSysUser(int $id){
        try {
            $s_info = SysAdmin::getInfoById($id);
            if (empty($s_info)) {
                return return_fail(1001);//用户不存在
            }
            if ($s_info['id'] == 1) {
                return return_fail(8002, '不能删除admin用户');
            }
            $result = SysAdmin::softDeleteById($id);
            if (!$result) {
                return return_fail(8003, '删除失败');
            }
            return return_success('删除成功');
        } catch (\Exception $ex) {
            setErrLog('SysAdminLogic > delSysUser > '.paramToString($id), $ex);
            return return_fail(8002);
        }
    }

}

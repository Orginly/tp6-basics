<?php

/**
 * 系统配置
 * User: orginly
 * Date: 2021/2/12
 * Time: 15:49
 */

namespace app\admin\controller;

use app\admin\logic\SysAdminLogic;
use think\facade\Request;
use think\response\Json;

class SysAdmin extends Base
{
    /**
     * Notes:获取系统用户列表
     * @return Json
     */
    public function sysUserList(){
        $page      = Request::param('pageNo', 1, 'intval');
        $pageSize  = Request::param('pageSize', env_custom('pageSize'), 'intval');
        $username  = Request::param('username', '', 'trim');
        $status    = Request::param('status', -1);
        $dateran   = Request::param('dateran', []);
        $sortField = Request::param('sortField', '');
        $sortOrder = Request::param('sortOrder', '');
        $result    = SysAdminLogic:: getSysUserList($page, $pageSize, $username, $status, $dateran, $sortField, $sortOrder);
        return json($result);
    }

    /**
     * Notes:添加用户
     * @return Json
     */
    public function addSysUser(){
        $username         = Request::post('username', '', 'trim');
        $nickname         = Request::post('nickname', '', 'trim');
        $image            = Request::post('image', '', 'trim');
        $password         = Request::post('password', '', 'trim');
        $password_confirm = Request::post('password_confirm', '', 'trim');
        $data             = [
            'username'         => $username,
            'password'         => $password,
            'password_confirm' => $password_confirm
        ];
        //验证器
        $this->aValidate($data, 'Users', 'add_sys_user');
        $result = SysAdminLogic::addSysUser($username, $password, $nickname, $image);
        return json($result);
    }

    /**
     * Notes: 编辑用户信息
     * @return Json
     */
    public function editSysUser(){
        $id       = Request::post('id', 0, 'intval');
        $nickname = Request::post('nickname', '', 'trim');
        $image    = Request::post('image', '', 'trim');
        $result   = SysAdminLogic::editSysUser($id, $nickname, $image);
        return json($result);
    }

    /**
     * Notes:软删除用户
     * @return Json
     */
    public function delSysUser(){
        $id     = Request::post('id', 0, 'intval');
        $result = SysAdminLogic::delSysUser($id);
        return json($result);
    }
}

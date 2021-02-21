<?php

/**
 * 用户管理控制器
 * User: orginly
 * Date: 2021/2/10
 * Time: 13:46
 */

namespace app\admin\controller;

use app\admin\logic\UsersLogic;
use think\facade\Request;
use think\response\Json;

class Users extends Base
{
    /**
     * Notes:获取用户列表
     * @return Json
     */
    public function userList(){
        $page      = Request::param('pageNo', 1, 'intval');
        $pageSize  = Request::param('pageSize', env_custom('pageSize'), 'intval');
        $keyword   = Request::param('keyword', '', 'trim');
        $status    = Request::param('status', -1);
        $dateran   = Request::param('dateran', []);
        $sortField = Request::param('sortField', '');
        $sortOrder = Request::param('sortOrder', '');
        $result    = UsersLogic:: getUserList($page, $pageSize, $keyword, $status, $dateran, $sortField, $sortOrder);
        return json($result);
    }

    /**
     * Notes:添加用户
     * @return Json
     */
    public function addUser(){
        $mobile_area      = Request::post('mobile_area', '+86', 'trim');
        $mobile           = Request::post('mobile', '', 'trim');
        $nickname         = Request::post('nickname', '', 'trim');
        $password         = Request::post('password', '', 'trim');
        $password_confirm = Request::post('password_confirm', '', 'trim');
        $data             = [
            'mobile'           => $mobile,
            'password'         => $password,
            'password_confirm' => $password_confirm
        ];
        //验证器
        $this->aValidate($data, 'Users', 'add_user');
        $result = UsersLogic::addUser($mobile, $mobile_area, $password, $nickname);
        return json($result);
    }

    /**
     * Notes: 编辑用户信息
     * @return Json
     */
    public function editUser(){
        $id = Request::post('id', 0, 'intval');
        $mobile = Request::post('mobile', '', 'trim');
        $nickname = Request::post('nickname', '', 'trim');
        $result = UsersLogic::editUser($id, $mobile, $nickname);
        return json($result);
    }

    /**
     * Notes:软删除用户
     * @return Json
     */
    public function delUser(){
        $id = Request::post('id', 0, 'intval');
        $result = UsersLogic::delUser($id);
        return json($result);
    }
}

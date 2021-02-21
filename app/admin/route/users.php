<?php

/**
 * 用户管理路由文件
 * User: orginly
 * Date: 2021/2/10
 * Time: 13:47
 */

use think\facade\Route;

Route::rule('users/list','admin/Users/userList','POST');//用户列表
Route::rule('users/add','admin/Users/addUser','POST');//添加用户
Route::rule('users/edit','admin/Users/editUser','POST');//编辑用户
Route::rule('users/del','admin/Users/delUser','POST');//删除用户

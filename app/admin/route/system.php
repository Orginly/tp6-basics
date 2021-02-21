<?php

/**
 * 系统配置路由文件
 * User: orginly
 * Date: 2021/2/12
 * Time: 18:48
 */

use think\facade\Route;

Route::rule('sys/admin/list', 'admin/SysAdmin/sysUserList', 'POST');//用户列表
Route::rule('sys/admin/add', 'admin/SysAdmin/addSysUser', 'POST');  //添加用户
Route::rule('sys/admin/edit', 'admin/SysAdmin/editSysUser', 'POST');//编辑用户
Route::rule('sys/admin/del', 'admin/SysAdmin/delSysUser', 'POST');//删除用户

<?php
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Users extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'mobile'   => 'require|mobile',
        'username' => 'require|min:5',
        'password' => 'require|min:6|max:18|confirm',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'mobile.require'   => [1000],//手机号未输入
        'username.require' => [1100],//用户名未输入
        'username.min'     => [1006],//用户名不得小于6位
        'mobile.mobile'    => [1101],//手机号格式不正确
        'password.require' => [1112],//密码未输入
        'password.min'     => [1120],//密码长度最小6位
        'password.max'     => [1121],//密码长度最大18位
        'password.confirm' => [1114],//两次输入的登录密码不一致
    ];

    protected $scene = [
        'add_user' => ['mobile', 'password'],
        'add_sys_user' => ['username', 'password'],
    ];
}

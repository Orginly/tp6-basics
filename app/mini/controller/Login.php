<?php

/**
 * Created by PhpStorm.
 * User: orginly
 * Date: 2021/2/21
 * Time: 15:26
 */

namespace app\mini\controller;

use app\mini\logic\LoginLogic;
use think\facade\Request;

class Login extends MiniBase
{

    /**
     * Notes:微信用户登录(用户存在时)
     * @return \think\response\Json
     */
    public function login(){
        $code   = Request::post('code', '');//临时凭证
        $result = LoginLogic::userLogin($code);
        return json($result);
    }

    /**
     * Notes:用户不存在时授权手机号注册并登录
     * @return \think\response\Json
     */
    public function userAuth(){
        $openid        = Request::post('openid', '');       //开放id 通过login()登录后换取
        $encryptedData = Request::post('encryptedData', '');//加密数据
        $iv            = Request::post('iv', '');           //加密算法的初始向量
        $result        = LoginLogic::authSaveUserInfo($openid, $encryptedData, $iv);
        return json($result);
    }
}

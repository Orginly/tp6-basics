<?php

/**
 * 用户公共逻辑层
 * User: orginly
 * Date: 2021/2/10
 * Time: 18:32
 */

namespace app\common\logic;

use app\common\model\CUsers;

class CUsersLogic
{
    /**
     * Notes:统一注册账户
     * @param string $mobile [手机号]
     * @param string $mobile_area [手机区域]
     * @param string $password [密码]
     * @param string $nickname [昵称]
     * @param string $openid [openid可为空]
     * @return bool|null [true成功、false失败]
     */
    public static function registerUser(string $mobile, string $mobile_area, string $password, string $nickname = '', string $openid = ''){
        try {
            $orange_key = sp_random_string(); //生成随机邀请码
            $data       = [
                'open_id'     => $openid,
                'mobile'      => $mobile,
                'mobile_area' => $mobile_area,
                'username'    => sp_random_code('T'),//生成随机用户名
                'password'    => sp_password($password, $orange_key),
                'orange_key'  => sp_random_string(), //生成随机邀请码,
                'nickname'    => $nickname,
                'status'      => 1,
            ];
            $result     = \app\common\model\CUsers::create($data);
            if (!$result) {
                setErrLog('统一注册用户失败'.json_encode($data));
                return false;
            }
            return true;
        } catch (\Exception $ex) {
            setErrLog('CUsers > registerUser > '.paramToString(), $ex);
            return null;
        }
    }

    /**
     * Notes:验证手机号是否可用
     * @param string $mobile
     * @return int|null [返回错误状态码 ,0为可用]
     */
    public static function checkMobile(string $mobile){
        try {
            if (empty($mobile)) {
                return 1100;//手机号未输入
            }
            if (!is_mobile($mobile)) {
                return 1101;//手机号格式不正确
            }
            $u_info = CUsers::getInfoByMobile($mobile);
            if (!empty($u_info)) {
                return 1102;//手机号已被占用
            }
            return 0;
        } catch (\Exception $ex) {
            setErrLog('CUsersLogic > checkMobile > '.paramToString(), $ex);
            return null;
        }
    }
}

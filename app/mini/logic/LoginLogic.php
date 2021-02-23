<?php

/**
 * 微信小程序业务层
 * User: orginly
 * Date: 2021/2/21
 * Time: 15:26
 */

namespace app\mini\logic;

use app\common\logic\CUsersLogic;
use app\common\logic\CWxMiniLogic;
use app\common\model\CRedis;
use app\common\model\CUserLoginLog;
use app\common\model\CUsers;
use app\mini\model\Users;
use think\facade\Request;

class LoginLogic
{
    /**
     * Notes:微信用户登录(已存在用户时)
     * @param $code [微信临时Code]
     * @return array|bool|null
     */
    public static function userLogin($code){
        try {
            if (empty($code)) {
                return return_fail(8005);// 凭证不得为空
            }
            // 通过临时凭证换取 用户唯一标识 OpenID 和 会话密钥 session_key
            $result = CWxMiniLogic::code2Session($code);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return return_fail(8003, '授权异常');
            }
            // 用户登录
            return self::openidLogin($result['openid']);
        } catch (\Exception $ex) {
            setErrLog('LoginLogic > userLogin > '.paramToString($code), $ex);
            return return_fail(8003);
        }
    }

    /**
     * Notes:通过openid登录
     * @param string $openid
     * @return array|null
     */
    public static function openidLogin(string $openid){
        try {
            $u_info = Users::getUserInfoByOpenId($openid);
            //如果用户不存在
            if (empty($u_info)) {
                setErrLog('openid对应用户不存在:'.$openid);
                return return_fail(1001, '', ['openid' => $openid]);
            }
            //强退在线用户
            CUserLoginLog::outLogin($u_info['id'], 2, '新设备登录');
            if ($u_info['status'] == 0) {
                return return_fail(8013);// 您的账号已被冻结
            }
            //写入登录记录
            $log_info = CUserLoginLog::createLoginLog($u_info['id'], $u_info['username'], $u_info['login_time'], $u_info['login_ip']);
            if (!$log_info) {
                return return_fail(8003); //操作异常
            }
            //更新用户登录时间和ip
            $result = CUsers::SaveLoginInfo($u_info['id'], msectime(), Request::ip());
            if (!$result) {
                return_fail(8003, '保存用户信息失败');
            }
            //返回数据
            $returnData = [
                'uid'    => $u_info['id'],
                'mobile' => $u_info['mobile'],
                'openid' => $openid,
                'token'  => $log_info['token'],
                'expire' => $log_info['expire'],
            ];
            $redis      = new CRedis();
            $redis->shSet(env('api_login_redis'), $u_info['id'], $returnData);
            return return_success('登录成功', $returnData);
        } catch (\Exception $ex) {
            setErrLog('LoginLogic > openidLogin > '.paramToString($openid), $ex);
            return return_fail(8003);
        }
    }

    /**
     * Notes:授权手机号保存用户信息并登录
     * @param string $openid
     * @param string $encryptedData
     * @param string $iv
     * @return array|null
     */
    public static function authSaveUserInfo(string $openid, string $encryptedData, string $iv){
        $error = 'LoginLogic > saveUserInfo > '.paramToString($openid, $encryptedData, $iv).' > ';
        try {
            if (empty($openid)) {
                return return_fail(8005, 'openid不能为空');
            }
            // 通过传入的openid进行登录,如果登录成功则结束
            $result = self::openidLogin($openid);
            if ($result['code'] == 0) {
                return $result;
            }
            //解密数据获取手机号码
            $data = CWxMiniLogic::decodeEncrypt($openid, $encryptedData, $iv);
            if (!$data) {
                return return_fail(8003, '解密手机号失败');
            }
            $mobile = $data['phoneNumber'];
            //查询用户表中是否存在用户 不存在则创建   存在则更新openid
            $u_info = Users::getInfoByMobile($mobile);
            if (empty($u_info)) {
                $result = CUsersLogic::registerUser($mobile, '+86', '123456', '', $openid);
                if (!$result) {
                    setErrLog($error.'注册用户失败openid:'.$openid.' uid:'.$u_info['id']);
                    return return_fail(1119);//注册失败
                }
            } else {
                $result = Users::updateOpenid($u_info['id'], $openid);
                if (!$result) {
                    setErrLog($error.'更新用户openid失败 uid:'.$u_info['id'].',openid:'.$openid);
                    return return_fail(8003, '更新用户openid失败');
                }
            }
            //执行登录
            return self::openidLogin($openid);
        } catch (\Exception $ex) {
            setErrLog($error, $ex);
            return return_fail(8003);// 操作异常
        }
    }
}

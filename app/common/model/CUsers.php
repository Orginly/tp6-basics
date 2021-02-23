<?php

/**
 * 用户表公共模型
 * User: orginly
 * Date: 2021/2/10
 * Time: 13:39
 */

namespace app\common\model;

class CUsers extends CModel
{
    protected $table = 'users';

    /**
     * Notes: 通过手机号获取用户信息
     * @param string $mobile [手机号]
     * @return array|null
     */
    public static function getInfoByMobile(string $mobile){
        try {
            $where  = [
                ['mobile', '=', $mobile],
            ];
            $result = self::where($where)->find();
            return toArray($result);
        } catch (\Exception $ex) {
            setErrLog('CUsers > getInfoByMobile > '.paramToString($mobile), $ex);
            return null;
        }
    }

    /**
     * Notes:通过openid获取用户信息
     * @param string $openid
     * @return array
     */
    public static function getUserInfoByOpenId(string $openid){
        try {
            $where = [
                ['openid', '=', $openid],
                ['del_time', '=', 0],
            ];
            $info  = self::where($where)->find();
            return toArray($info);
        } catch (\Exception $ex) {
            setErrLog('UserLoginLog > getUserInfoByOpenId > '.paramToString($openid), $ex);
            return [];
        }
    }

    /**
     * Notes:保存登录信息
     * @param $uid [用户id]
     * @param $login_time [登录时间]
     * @param $login_ip [登陆id]
     * @return bool
     */
    public static function SaveLoginInfo(int $uid, int $login_time, string $login_ip){
        try {
            $where = [
                ['uid', '=', $uid]
            ];
            $data  = [
                'login_time'  => $login_time,
                'login_ip'    => $login_ip,
                'update_time' => msectime(),
            ];
            // 更新数据 返回的是模型对象
            $u_info = self::update($data, $where);
            if ($u_info) {
                $u_info = toArray($u_info);
                $redis  = CRedis::getInstance();
                // 保存用户数据到redis中
                $redis->shSet(env_custom('user_redis'), $uid, $u_info);
                return true;
            }
            return false;
        } catch (\Exception $ex) {
            setErrLog('CUsers > SaveLoginInfo > '.paramToString($uid, $login_time, $login_ip), $ex);
            return false;
        }
    }
}

<?php

/**
 * 用户登录记录公共模型
 * User: orginly
 * Date: 2021/2/21
 * Time: 20:55
 */

declare(strict_types = 1);

namespace app\common\model;

use think\facade\Request;

class CUserLoginLog extends CModel
{
    protected $table = 'user_login_log';

    /**
     * Notes: 获取用户所有在线的记录
     * @param int $uid
     * @return array
     */
    public static function getOnlineUserList(int $uid): array{
        try {
            $where = [
                ['active', '=', 0],
            ];
            $list  = self::where($where)->select();
            return toArray($list);
        } catch (\Exception $ex) {
            setErrLog('CUserLoginLog > getOnlineUserList > '.paramToString($uid), $ex);
            return [];
        }
    }

    /**
     * Notes:强退在线用户
     * @param int $uid [用户id]
     * @param int $active [0已登录、1正常退出、2非正常退出、3系统冻结强制退出]
     * @param string $about [备注]
     * @return true
     */
    public static function outLogin(int $uid, int $active, string $about): bool{
        try {
            $where = [
                ['uid', '=', $uid]
            ];
            $data  = [
                'active'          => $active,
                'about'           => $about,
                'quit_login_time' => msectime()
            ];
            self::where($where)->update($data);
            //删除过期凭证
            $redis = new CRedis();
            $redis->hDel('api_token', $uid);
            return true;
        } catch (\Exception $ex) {
            setErrLog('CUserLoginLog > outLogin > '.paramToString($uid, $active, $about), $ex);
            return false;
        }
    }

    /**
     * Notes:新增登录记录
     * @param int $uid [用户id]
     * @param string $username [用户名]
     * @param string $last_login_ip [上次登录ip]
     * @param int $last_login_time [上次登录时间]
     * @return array
     */
    public static function createLoginLog(int $uid, string $username, string $last_login_ip, int $last_login_time): array{
        try {
            $data   = [
                'uid'             => $uid,
                'username'        => $username,
                'last_login_time' => $last_login_time,
                'last_login_ip'   => $last_login_ip,
                'active'          => 0,
                'expire'          => time() + 36000,
                'token'           => sha256($username.time().$uid),
                'login_ip'        => Request::ip()
            ];
            $result = self::create($data);
            return toArray($result);
        } catch (\Exception $ex) {
            setErrLog('CUserLoginLog > createLoginLog > '.paramToString($uid, $username, $last_login_ip, $last_login_time), $ex);
            return [];
        }
    }
}

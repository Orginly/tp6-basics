<?php

/**
 * Created by PhpStorm.
 * User: orginly
 * Date: 2021/2/21
 * Time: 15:27
 */

namespace app\mini\model;

use app\common\model\CUsers;

class Users extends CUsers
{
    /**
     * Notes:通过id更新用户openid
     * @param int $id
     * @param string $openid
     * @return bool
     */
    public static function updateOpenid(int $id, string $openid){
        $error = 'Users > updateOpenid > '.paramToString($id, $openid).' > ';
        try {
            $where = [
                ['id', '=', $id]
            ];
            $data  = [
                'openid'      => $openid,
                'update_time' => msectime()
            ];
            return self::where($where)->save($data);
        } catch (\Exception $ex) {
            setErrLog($error, $ex);
            return false;
        }
    }
}

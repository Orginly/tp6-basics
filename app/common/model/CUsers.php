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
}

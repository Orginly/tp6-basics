<?php

/**
 * Created by PhpStorm.
 * User: orginly
 * Date: 2021/2/21
 * Time: 18:56
 */

namespace app\common\logic;

use app\common\model\CRedis;
use wx\WXBizDataCrypt;

class CWxMiniLogic
{
    /**
     * Notes:通过临时Code凭证向微信服务器换取openId和session_key
     * @param string $code [临时凭证 wx.login()]
     * @return bool|mixed
     */
    public static function code2Session(string $code){
        try {
            $wxConfig = env_custom('mini'); //获取app_id和app_secret
            $url      = "https://api.weixin.qq.com/sns/jscode2session?appid={$wxConfig ['app_id']}&secret={$wxConfig ['app_secret']}&js_code={$code}&grant_type=authorization_code";
            $result   = curlRequest($url, [], 'GET'); // Curl GET请求微信服务器获取会话秘钥
            $result   = json_decode($result, true);
            // 设置redis
            if (isset($result['openid']) && isset($result['session_key'])) {
                self::getOrSetSession($result['openid'], $result['session_key']);
            }
            return $result;
        } catch (\Exception $ex) {
            setErrLog('WxMiniLogic > code2Session > '.paramToString($code), $ex);
            return false;
        }
    }

    /**
     * Notes:查询或设置 Redis会话密钥
     * @param $openid [开放id]
     * @param string $session_key [会话密钥 为空则获取]
     * @return int|string|null
     */
    public static function getOrSetSession($openid, $session_key = ''){
        try {
            $redis = new CRedis();
            if ($session_key) {
                $redis->hSet(env_custom('session_key'), $openid, $session_key);
            }
            return $redis->hGet(env_custom('session_key'), $openid);
        } catch (\Exception $ex) {
            setErrLog('CWxMiniLogic > getOrSetSession > '.paramToString($openid, $session_key), $ex);
            return null;
        }
    }

    /**
     * Notes:对微信小程序用户加密数据的解密
     * @param string $openid [用户开放id]
     * @param string $encryptedData [需要解密的数据]
     * @param string $iv [解密初值]
     * @return bool|mixed
     */
    public static function decodeEncrypt(string $openid, string $encryptedData, string $iv){
        $error = 'CWxMiniLogic > decodeEncrypt > '.paramToString($openid, $encryptedData, $iv).' > ';
        try {
            $redis = new CRedis();
            //获取微信会话session_key
            $session_key = $redis->hGet(env_custom('session_key'), $openid);
            //创建对象 传入AppID!!!
            $pc = new WXBizDataCrypt(env_custom('mini.app_id'), $session_key);
            //对数据进行解密 返回错误码
            $errorCode = $pc->decryptData($encryptedData, $iv, $data);
            if ($errorCode != 0) {
                setErrLog($error.'解密失败错误码:'.$errorCode);
                return false;
            }
            return json_decode($data, true);
        } catch (\Exception $ex) {
            setErrLog($error, $ex);
            return false;
        }
    }
}

<?php
// 应用公共文件

# region 统一写入或返回方法

/**
 * 统一日志写入
 * @param String $title [功能或操作名称]
 * @param Exception $ex [异常信息(可为null)]
 */
function setErrLog(string $title, Exception $ex = null){
    if ($ex != null) {
        //记录日志
        trace($title.'异常', 'error');
        trace('行数'.$ex->getLine(), 'error');
        trace($ex->getMessage(), 'error');
    } else {
        trace($title, 'error');
    }
}

/**
 * 统一失败返回编码
 * @param int $code [状态码]
 * @param string $msg [文字提示，为空时读语言包]
 * @param array $data [返回的信息]
 * @return array
 */
function return_fail(int $code, string $msg = '', array $data = []){
    $arr         = [];
    $arr['code'] = $code;
    $arr['msg']  = $msg;
    if (empty($msg)) {
        //获取语言包中的编码
        $arr['msg'] = \think\facade\Lang::get($code);
    }
    $arr['time'] = time();
    $arr['data'] = $data;
    return $arr;
}

/**
 * 统一成功返回
 * @param string $msg
 * @param array $data
 * @return array
 */
function return_success(string $msg = '', array $data = []){
    $arr         = array();
    $arr['code'] = 0;
    $arr['msg']  = $msg;
    $arr['time'] = time();
    $arr['data'] = $data;
    return $arr;
}

#endregion

# region 字符串方法
/**
 * 获取随机的用户编号
 * @param string $first [首字母]
 * @param int $len [生成的字符串长度]
 * @return string
 */
function sp_random_code(string $first = '', int $len = 6){
    $chars    = [
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9
    ];
    $char_len = count($chars) - 1;
    //将数组打乱
    shuffle($chars);
    $res = strtoupper($first);
    for ($i = 0; $i < $len; $i++) {
        $res .= $chars[mt_rand(1, $char_len)];
    }
    return $res;
}

/**
 * 随机字符串生成推荐码
 * @param int $len 生成的字符串长度
 * @return string
 */
function sp_random_string(int $len = 6){
    $chars    = array(
        "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 将传入的变量值拼接为字符串
 * @param mixed ...$args
 * @return string
 */
function paramToString(...$args){
    return json_encode($args);
}

/**
 * 生成uuid
 * @return string
 */
function uuid(){
    mt_srand(( double )microtime() * 10000);
    $char_id = md5(uniqid(rand(), true));
    $hyphen  = chr(45); // "-"
    $uuid    = ''.substr($char_id, 0, 8).$hyphen.substr($char_id, 8, 4).$hyphen.substr($char_id, 12, 4).
        $hyphen.substr($char_id, 16, 4).$hyphen.substr($char_id, 20, 12);
    return $uuid;

}

/**
 * 通过时间生成唯一订单号(除了前缀长度是18位)
 * @param $first
 * @return string
 */
function createOrderSn($first = ''){
    list($t1, $t2) = explode(' ', microtime());
    return $first.date('ymdHis', $t2).substr($t1, 2, 6);
}

/**
 * 将一个字符串超过n个字符的中间部分字符用*替代隐藏
 * @param $strKey
 * @param $n /限制长度值
 * @return mixed
 */
function hideStr($strKey, $n = 8){
    $len = strlen($strKey);
    if ($len > $n) {
        $length = $len - $n;
        return substr_replace($strKey, '****', 4, $length);
    } else {
        return $strKey;
    }
}

# endregion

#region 计算函数
/**
 * 两数相加
 * @param $left /第一位数
 * @param $right /第二位数
 * @param int $scale /小数位
 * @return string
 */
function add($left, $right, $scale = 4){
    return bcadd($left, $right, $scale);
}

/**
 * 两数相减
 * @param $left /被减数
 * @param $right /减数
 * @param int $scale /小数位
 * @return string
 */
function sub($left, $right, $scale = 4){
    return bcsub($left, $right, $scale);
}

/**
 * 两数相乘
 * @param $left /被乘数
 * @param $right /乘数
 * @param int $scale /小数位
 * @return string
 */
function mul($left, $right, $scale = 4){
    return bcmul($left, $right, $scale);
}

/**
 * 两数相除
 * @param $left /被除数
 * @param $right /除数
 * @param int $scale /小数位
 * @return int|string|null
 */
function div($left, $right, $scale = 4){
    try {
        return bcdiv($left, $right, $scale);
    } catch (Exception $ex) {
        setErrLog('两数相除', $ex);
        return 0;
    }
}

/**
 * 两数相比较
 * @param $left /左边数
 * @param $right /右边数
 * @param int $scale /小数位
 * @return int [0相等，-1小于，1大于]
 */
function comp($left, $right, $scale = 4){
    return bccomp($left, $right, $scale);
}

#endregion

#region 转换函数
/**
 * 结果数值转换统一保留小数
 * @param $value /金额数值
 * @param $dec /小数位(默认4位)
 * @param $isRound /是否四舍五入(1是、0否)
 */
function valueDec(&$value, $dec = 4, $isRound = 0){
    if ($isRound) {
        $value = round($value, $dec);
    }
    $value = bcadd($value, '0', $dec);
}

/**
 * 毫秒时间戳
 * @return int
 */
function msectime(){
    list($t1, $t2) = explode(' ', microtime());
    $msectime = strval($t2).substr($t1, 2, 3);
    return intval($msectime);
}

/**
 * 格式化时间戳
 * @param $timestamp
 */
function format_time(&$timestamp){
    $len = strlen($timestamp);
    if ($len == 13) {
        $timestamp = date('Y-m-d H:i:s', $timestamp / 1000);
    } elseif ($len == 10) {
        $timestamp = date('Y-m-d H:i:s', $timestamp);
    }
}

/**
 * Notes:Model对象转换为数组
 * @param $data
 * @return array
 */
function toArray($data){
    return $data ? $data->toArray() : [];
}

/**
 * Notes: 将模型分页数据转换为ant格式返回
 * @param $data
 * @return array
 */
function paginateToAnt(\think\Paginator $data){
    $data               = $data->toArray();
    $list               = [];
    $list['pageNo']     = $data['current_page'];
    $list['pageSize']   = $data['per_page'];
    $list['totalPage']  = $data['last_page'];
    $list['totalCount'] = $data['total'];
    $list['data']       = $data['data'];
    return $list;
}

/**
 * Notes: 截取字符串出现位置之前的字符
 * @param string $str [需要查找字符串]
 * @param string $strpos [指定字符串]
 * @return false|string
 */
function substrAssign(string $str, string $strpos){
    return substr($str, 0, strpos($str, $strpos));
}

/**
 * Notes:转换UTC时间为时间戳数组
 * @param array $dateran
 * @return array
 */
function get_dateran(array $dateran){
    $dateran[0] = strtotime(date('Y-m-d 00:00:00', strtotime($dateran[0]))) * 1000;
    $dateran[1] = strtotime(date('Y-m-d 23:59:59', strtotime($dateran[1]))) * 1000;
    return $dateran;
}

#endregion

#region 加密方法

/**
 * sha256加密函数
 * @param $key
 * @return string
 */
function sha256($key){
    return hash('sha256', $key);
}

/**
 * 加密函数
 * @param string $password
 * @param string $orange_key
 * @return string
 */
function sp_password(string $password, string $orange_key = ''){
    return 'va'.md5($orange_key.sha256($orange_key.$password));

}

/**
 * 加密比较函数
 * @param string $password 要比较的密码
 * @param string $password_in_db 数据库保存的已经加密过的密码
 * @param $auth_code
 * @return boolean 密码相同，返回true
 */
function sp_compare_password($password, $password_in_db, $auth_code = ''){
    if (strpos($password_in_db, "va") === 0) {
        return sp_password($password, $auth_code) == $password_in_db;
    } else {
        return false;
    }
}

#endregion

#region 验证方法
/**
 * 验证输入的邮件地址是否合法
 * @param $user_email [邮箱]
 * @return bool
 */
function is_email($user_email){
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false) {
        if (preg_match($chars, $user_email)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 验证输入的手机号码是否合法
 * @param $mobile_phone [手机号]
 * @return bool
 */
function is_mobile($mobile_phone){
    $chars = "/^1[3-9]\d{9}$/";
    if (preg_match($chars, $mobile_phone)) {
        return true;
    }
    return false;
}

#endregion

#region 获取配置信息

/**
 * 获取公共自定义参数
 * @param string $name
 * @return mixed
 */
function env_custom(string $name){
    if (env('DEVELOP')) {//测试环境
        return config('custom_test.'.$name);
    } else {
        return config('custom.'.$name);
    }
}

#endregion

/**
 * curl的post请求
 * @param $url
 * @param null $data [数组/json字符串]
 * @param bool $json [是否json发送，true为是]
 * @return array|mixed
 */
function curlPost($url, $data = NULL, $json = false){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        if ($json && is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        if ($json) { //发送JSON数据
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length:'.strlen($data))
            );
        }
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $res     = curl_exec($curl);
    $errorno = curl_errno($curl);
    if ($errorno) {
        return array('code' => -1, 'msg' => $errorno);
    }
    curl_close($curl);
    return json_decode($res, true);
}

/**
 * Notes:Curl请求
 * @param string $url [url链接]
 * @param array $data [参数]
 * @param string $method [默认POST  GET|POST]
 * @param bool $json
 * @return int
 */
function curlRequest(string $url, array $data = [], string $method = 'POST', bool $json = false){
    $ch = curl_init();                                //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, $url);              //设置请求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //跳过https验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if ($method == 'POST' && !empty($data)) {
        if ($json && is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($json) { //发送JSON数据
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length:'.strlen($data))
            );
        }
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: $method"));//设置HTTP头信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                   //设置提交的字符串
    }
    $result  = curl_exec($ch);//执行预定义的CURL
    $errorno = curl_errno($ch);
    if ($errorno) {
        return $errorno;
    }
    curl_close($ch);
    return $result;
}

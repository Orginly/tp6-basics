<?php

/**
 * 基础控制器
 * User: orginly
 * Date: 2021/2/10
 * Time: 17:33
 */
declare(strict_types = 1);

namespace app\admin\controller;

use app\BaseController;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Lang;
use think\Response;

class Base extends BaseController
{

    /**
     * Notes:成功返回封装后的API数据到客户端
     * @param mixed $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param array $header 发送的Header信息
     * @return Response
     */
    protected function success($msg = '', $data = [], array $header = []): Response{
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $type     = 'json';
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * Notes:失败返回封装后的API数据到客户端
     * @param int $code [code]
     * @param string $msg [错误信息]
     * @param array $data [返回的数据]
     * @param array $header [发送的Header信息]
     * @return Response
     */
    public function fail(int $code, string $msg = '', array $data = [], array $header = []): Response{
        if (empty($msg)) {
            $msg = Lang::get((string)$code);
        }
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data
        ];
        //创建response对象
        $response = Response::create($result, 'json')->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * Notes:验证器
     * @param array $data
     * @param $validate
     * @param string $scene
     * @return bool
     */
    public function aValidate(array $data, $validate, $scene = ''){
        try {
            if ($scene) {
                //解析到当前应用的 validate层类名
                validate($this->app->parseClass('validate', $validate))
                    ->scene($scene)
                    ->check($data);
            } else {
                validate($this->app->parseClass('validate', $validate))
                    ->check($data);
            }
        } catch (ValidateException $ex) {
            $this->fail((int)$ex->getMessage());
        }
        return true;
    }
}

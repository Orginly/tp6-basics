<?php

/**
 * 公共的数据库模型类
 * User: orginly
 * Date: 2021/2/10
 * Time: 13:54
 */

namespace app\common\model;

use think\Model;

class CModel extends Model
{
    /**
     * Notice:获取详情信息
     * @param int $id
     * @return array|null
     */
    public static function getInfoById(int $id){
        try {
            $where  = [
                ['id', '=', $id],
            ];
            $result = self::where($where)->find();
            return $result ? $result->toArray() : [];
        } catch (\Exception $ex) {
            setErrLog('CModel > getInfoById > '.paramToString($id), $ex);
            return null;
        }
    }

    /**
     * Notes:重写create函数增加创建时间、更新时间
     * @param array $data
     * @param array $allowField
     * @param bool $replace
     * @param string $suffix
     * @return Model
     */
    public static function create(array $data, array $allowField = [], bool $replace = false, string $suffix = ''): Model{
        $data['create_time'] = isset($data['create_time']) ? : msectime();
        return parent::create($data, $allowField, $replace, $suffix);
    }

    /**
     * Notes:获取列表
     * @param int $page
     * @param int $limit
     * @return array|null
     */
    public static function getList(int $page, int $limit){
        try {
            $list = self::where('del_time', 0)
                ->order('create_time', 'desc')
                ->page($page, $limit)
                ->select();
            return $list ? $list->toArray() : [];
        } catch (\Exception $ex) {
            setErrLog('CModel > getList > '.paramToString(), $ex);
            return null;
        }
    }


    /**
     * Notes:通过id软删除
     * @param $id
     * @return CModel|bool
     */
    public static function softDeleteById($id){
        try {
            return self::where('id', $id)
                ->update(['del_time' => msectime()]);
        } catch (\Exception $ex) {
            setErrLog('CModel > softDelete > '.paramToString(), $ex);
            return false;
        }
    }
}

<?php
/**
 * common model file Created by PhpStorm.
 * User: wumengmeng
 * Date: 2019/08/20
 * Time: 06:01
 */

namespace HashyooEntrust\LaravelService;

use HashyooEntrust\LaravelModel\EntrustUserRole;

class EntrustUserRoleService extends BaseService
{

    /**
     * 用户角色设置
     *
     * @param int   $n_id
     * @param array $arr_ids
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function insert($n_id = 0, $arr_ids = [])
    {
        if ($n_id <= 0) {
            return yoo_hello_fail('数据id不能为空');
        }
        //        if(count($arr_ids) <= 0){
        //            return yoo_hello_fail('请选择权限');
        //        }

        //删除之前的数据
        $option = ['where' => ['user_id' => $n_id]];
        $result = EntrustUserRole::lara_delete($option);
        if (!($result >= 0)) {
            return yoo_hello_fail('操作失败', '删除失败');
        }

        //重新添加新的数据
        $arr_data = [];
        foreach ($arr_ids as $value) {
            $arr_data[] = ['user_id' => $n_id, 'role_id' => $value];
        }
        $result = EntrustUserRole::lara_insert($arr_data);
        if (!$result) {
            return yoo_hello_fail('设置失败');
        }
        return yoo_hello_success('设置成功');
    }

    /**
     * 用户角色
     *
     * @param int $uid
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function user_roles($uid = 0)
    {
        $arr_option['where']['user_id'] = $uid;
        $result = EntrustUserRole::lara_all($arr_option)->toarray();
        return yoo_hello_success('获取成功',$result);
    }


}
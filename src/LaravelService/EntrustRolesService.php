<?php
/**
 * common model file Created by PhpStorm.
 * User: wumengmeng
 * Date: 2019/08/20
 * Time: 06:01
 */
namespace HashyooEntrust\LaravelService;

use HashyooEntrust\LaravelModel\EntrustRolePermission;
use HashyooEntrust\LaravelModel\EntrustRoles;

class EntrustRolesService extends BaseService
{

    /**
     * 所有角色（包含已禁用的）
     *
     * @param array $arr_field
     *
     * @return mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function all_get($arr_option = []){
        $arr_option['order'] = [
          'id'=>'asc',
        ];
        $result = EntrustRoles::en_get($arr_option)->toArray();
        return yoo_hello_success('查询成功',$result);
    }

    /**
     * 所有角色（只含未被禁用的）
     *
     * @param array $arr_field
     *
     * @return mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function normal_get($arr_option = []){
        $arr_option['where'][] = ['status'=>1];
        $arr_option['order'] = [
          'id'=>'asc',
        ];
        $result = EntrustRoles::en_get($arr_option)->toArray();
        return yoo_hello_success('查询成功',$result);
    }

    /**
     * 查询一条角色
     *
     * @param int   $n_id
     * @param array $arr_field
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function find($n_id = 0,$arr_field = ['*']){
        if($n_id <= 0){
            return yoo_hello_fail('数据id不能为空');
        }

        $arr_option['field'] = $arr_field;
        $result = EntrustRoles::en_find($n_id,$arr_option);
        if(is_null($result)){
            return yoo_hello_fail('数据不存在');
        }
        $result = $result->toArray();
        return yoo_hello_success('查询成功',$result);


    }

    /**
     * 添加角色
     *
     * @param array $arr_input
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function add($arr_input = []){
        $result = EntrustRoles::form_add($arr_input);
        if($result['state'] != 200){
            return $result;
        }

        //角色名称不能重复
        $arr_option = ['where'=>yoo_array_remain($arr_input,['name'])];
        $result = EntrustRoles::en_count($arr_option);
        if($result > 0){
            return yoo_hello_fail('角色名称重复');
        }
        
        //添加角色
        $result = EntrustRoles::en_add($arr_input)->toarray();
        if(empty($result)){
            return yoo_hello_fail('添加失败');
        }
        else{
            return yoo_hello_success('添加成功');
        }

    }

    /**
     * 修改角色
     *
     * @param array $arr_input
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function edit($arr_input = []){
        $n_id = intval($arr_input['id']);
        if($n_id <= 0){
            return yoo_hello_fail('数据id不能为空');
        }

        //角色名称不能重复
        if(isset($arr_input['name'])){
            $arr_where['name'] = $arr_input['name'];
        }

        if(count($arr_where) > 0){
            $arr_where[] = ['id','!=',$n_id];
            $arr_option = [
              'where'=>$arr_where
            ];
            $result = EntrustRoles::en_count($arr_option);
            if($result > 0){
                return yoo_hello_fail('角色名称重复');
            }
        }

        //修改角色
        $result = EntrustRoles::en_edit($arr_input);
        if(!$result){
            return yoo_hello_fail('修改失败');
        }
        else{
            return yoo_hello_success('修改成功');
        }
      
    }

    /**
     * 删除角色
     *
     * @param int $n_id
     *
     * @return mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function del($n_id = 0){
        if($n_id <= 0){
            return yoo_hello_fail('数据id不能为空');
        }

        //查询是否有子级 若有子级不能删除
        $arr_option = ['where'=>['pid'=>$n_id]];
        $result = EntrustRoles::en_count($arr_option);
        if($result > 0){
            return yoo_hello_fail('当前数据有子数据，不能删除');
        }

        $result = EntrustRoles::en_delete($n_id);
        if(!$result){
            return yoo_hello_fail('删除失败','角色删除失败');
        }

        //删除角色权限信息
        $arr_option = ['where'=>['role_id'=>$n_id]];
        $result = EntrustRolePermission::en_del($arr_option);
        if(!($result >= 0)){
            return yoo_hello_fail('删除失败','角色权限信息删除失败');
        }
        return  yoo_hello_success('删除成功');
    }

    /**
     * 删除权限-若有子级递归删除
     *
     * @param int $n_id
     *
     * @return mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function del_recur($n_id = 0){
        if($n_id <= 0){
            return yoo_hello_fail('数据id不能为空');
        }

        $arr_option['field'] = ['id','pid'];
        $result = EntrustRoles::en_get($arr_option)->toArray();
        yoo_tree_child_ids($result,$arr_ids,$n_id,'id','pid');
        $arr_ids[] = $n_id;

        //删除角色权限信息
        $arr_option = ['whereIn'=>['role_id'=>$arr_ids]];
        $result = EntrustRolePermission::en_del($arr_option);
        if(!($result >= 0)){
            return yoo_hello_fail('删除失败','角色权限信息删除失败');
        }

        //递归删除
        $result = EntrustRoles::en_del_recur($n_id);
        if(!$result){
            return yoo_hello_fail('删除失败','角色删除失败');
        }
        
        return  yoo_hello_success('删除成功');
    }

    /**
     * 开启角色
     *
     * @param int $n_id
     *
     * @return mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function open($n_id = 0){
        if($n_id <= 0){
            return yoo_hello_fail('数据id不能为空');
        }
        $arr_input = ['id'=>$n_id,'status'=>1];
        $result = EntrustRoles::en_edit($arr_input);
        if(!$result){
            return yoo_hello_fail('开启失败');
        }
        return  yoo_hello_success('开启成功');
    }

    /**
     * 关闭角色
     *
     * @param int $n_id
     *
     * @return mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function close($n_id = 0){
        if($n_id <= 0){
            return yoo_hello_fail('数据id不能为空');
        }
        $arr_input = ['id'=>$n_id,'status'=>2];
        $result = EntrustRoles::en_edit($arr_input);
        if(!$result){
            return yoo_hello_fail('关闭失败');
        }
        return  yoo_hello_success('关闭成功');
    }

}
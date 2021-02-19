<?php

namespace Tq\${app_name}\Model;

class Clazz extends Base
{

    public function getList($schId, $grade = 0)
    {
        $sql = "select `id`, `school_id`, `title`, `grade`, `wx_dept_id`, `zh_dept_id`, `join_year`, `add_time`, `up_time` from class where `school_id` = {$schId}";
        if ($grade) {
            $sql .= " and grade = {$grade} ";
        }
        return $this->getDb()->fetchRows($sql);
    }

    /**
     * 添加单个 class
     * @param $class_info
     * @return int
     */
    public function addClass($class_info)
    {
        return $this->getDb()->insert('${class_table_name}', $class_info, true) ?: 0;
    }

    /**
     * 查看班级是否存在
     * @param $school_id
     * @param $wxDepId
     * @return array|false
     */
    public function getByWxDepId($school_id, $wxDepId)
    {
        $sql = "select `id` from `${class_table_name}` where `school_id` = {$school_id} and `wx_dept_id` = '{$wxDepId}'";
        return $this->getDb()->fetchOne($sql);
    }


    /**
     * 以智慧校园 id 查询是否存在该班级（部门）
     * @param $school_id
     * @param $depart_id
     * @return int
     */
    public function getByCampus($school_id, $depart_id)
    {
        $sql = "select `id` from `class` where `school_id` = {$school_id} and `zh_dept_id` = '{$depart_id}' limit 1";
        return $this->getDb()->fetchOne($sql) ?: 0;
    }

    /**
     * 更新
     * @param $clazz_info
     * @param $id
     */
    public function updateById($clazz_info, $id)
    {
        $this->getDb()->updateRow("class", $clazz_info, "`id` = '{$id}'");
    }

    /**
     * 根据 school_id wx_departid 获取班级
     * @param $school_id
     * @param $depart_id
     * @return array
     */
    public function getByWxDeptId($school_id, $depart_id)
    {
        $sql = "select * from `class` where `school_id` = {$school_id} and `wx_dept_id` = '{$depart_id}' limit 1";
        return $this->getDb()->fetchRows($sql) ?: [];
    }

}
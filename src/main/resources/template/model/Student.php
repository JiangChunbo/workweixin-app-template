<?php


namespace Tq\ShortVideoContest\Model;


class Student extends Base
{
    public function listByIds($id_list)
    {
		if(!$id_list) {
			return [];
		}
        $limit = sizeof($id_list);
        $ids = join("','", $id_list);
        $sql = "select * from `${student_table_name}` where `id` in ('{$ids}') limit {$limit}";
        return $this->getDb()->fetchRows($sql) ?: [];
    }

    /**
     * 添加学生
     * @param $student_info
     * @return int
     */
    public function addStudent($student_info)
    {
	return $this->getDb()->insert("${student_table_name}", $student_info, true) ?: 0;
    }

    /**
     * @param $student_info
     * @param $id
     */
    public function updateById($student_info, $id)
    {
        $this->getDb()->updateRow("${student_table_name}", $student_info, "`id` = '{$id}'");
    }

    public function getByBranchIdAndUserid($branch_id, $child_userid)
    {
        $sql = "select * from `${student_table_name}` where `branch_id` = '{$branch_id}' and `wx_userid` = '{$child_userid}' limit 1";
        return $this->getDb()->fetchRow($sql);
    }

    /**
     * 根据 ID 获取
     * @param $role_id
     * @return array
     */
    public function getById($role_id)
    {
        $sql = "select * from `${student_table_name}` where `id` = '{$role_id}' limit 1";
        return $this->getDb()->fetchRow($sql) ?: [];
    }

}
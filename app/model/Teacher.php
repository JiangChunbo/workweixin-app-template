<?php

namespace Tq\ShortVideoContest\Model;

class Teacher extends Base
{

    public function getByBranchIdAndWxUserid($branch_id, $wx_userid)
    {
        $sql = "select * from `" . TEACHER_TABLE_NAME . "` where `branch_id` = '{$branch_id}' and `wx_userid` = '{$wx_userid}' limit 1";
        echo $sql;
        return $this->getDb()->fetchRow($sql) ?: [];
    }

    /**
     * 更新老师信息
     * @param $data
     * @param $teacher_id
     * @return bool
     */
    public function updateTeacher($data, $teacher_id)
    {
        return $this->getDb()->update(TEACHER_TABLE_NAME, $data, "`id` = '{$teacher_id}'");
    }

    /**
     * 添加老师
     * @param $data
     * @return mixed
     */
    public function addTeacher($data)
    {
        return $this->getDb()->insert(TEACHER_TABLE_NAME, $data, true);
    }

    public function getById($teacher_id)
    {
        $sql = "select * from `vd_pltfrm_teacher` where `id` = '{$teacher_id}' limit 1";
        return $this->getDb()->fetchRow($sql) ?: [];
    }
}
<?php

namespace Tq\ShortVideoContest\Model;

class Subject extends Base
{
    public function listByStage($stage)
    {
        $sql = "select * from `vd_pltfrm_subject` where `stage` = '{$stage}' limit 20";
        $subject_list = $this->getDb()->fetchRows($sql) ?: [];
        foreach ($subject_list as $i => $subject) {
            $subject_list[$i] = [
                "subject_id" => $subject["id"],
                "subject_title" => $subject["title"]
            ];
        }
        return $subject_list;
    }
}
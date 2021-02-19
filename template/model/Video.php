<?php

namespace Tq\ShortVideoContest\Model;

class Video extends Base
{
    public function addVideo($video, $video_detail)
    {
        $video_id = $this->getDb()->insert("vd_pltfrm_video", $video, true);
        if ($video_id) {
            $video_detail["video_id"] = intval($video_id);
            $detail_id = $this->getDb()->insert("vd_pltfrm_video_dtl", $video_detail, true);
            return true;
        }
        return false;
    }


    public function listVideo($uploader_type, $uploader_id, $offset, $limit)
    {
        $where = "where `uploader_type` = '{$uploader_type}' and `uploader_id` = '{$uploader_id}' and `is_del` = 0";
        /* 查找未删除的 */
        $sql = "select * from `vd_pltfrm_video` {$where} order by `id` desc limit $offset, $limit";
        $video_list = $this->getDb()->fetchRows($sql) ?: [];

        $sql = "select count(1) from `vd_pltfrm_video` {$where}";
        $total = $this->getDb()->fetchOne($sql) ?: 0;

        $video_id_list = array_column($video_list, "id");
        $video_ids = join("','", $video_id_list);

        $sql = "select * from `vd_pltfrm_video_dtl` where `video_id` in ('{$video_ids}') limit $limit";
        $detail_list = $this->getDb()->fetchRows($sql);
        $detail_map = array_column($detail_list, null, "video_id");


        $subject_id_list = array_column($video_list, "subject_id");
        $subject_id_list = array_keys(array_flip($subject_id_list));
        $subject_ids = join("','", $subject_id_list);
        $sql = "select * from `vd_pltfrm_subject` where `id` in ('{$subject_ids}')";
        $subject_list = $this->getDb()->fetchRows($sql);
        $subject_map = array_column($subject_list, null, "id");

        foreach ($video_list as $i => $video) {
            $video_id = $video["id"];
            if ($video["approval_status"] == 2) { // 审核通过，赋值标签
                $label = $GLOBALS["g_label_map"][$video["label"]] ?: $GLOBALS["g_approval_status_map"][$video["approval_status"]];
            } elseif ($video["approval_status"] == 1 || $video["approval_status"] == 3) { // 审核中或者不通过
                $label = $GLOBALS["g_approval_status_map"][$video["approval_status"]];
            } else {
                $label = "";
            }
            $video_list[$i] = [
                "video_id" => $video_id,
                "file_id" => $detail_map[$video_id]["file_id"] ?: "",
                "cover" => $detail_map[$video_id]["cover"] ?: "",
                "subject_title" => $subject_map[$video["subject_id"]]["title"] ?: "",
                "video_title" => $video["title"],
                "label" => $label,
                "add_time" => $video["add_time"]
            ];
        }

        return [
            "list" => $video_list,
            "total" => $total
        ];

    }

    public function updateVideo($data, $id)
    {
        return $this->getDb()->updateRow("vd_pltfrm_video", $data, "`id` = '{$id}'");
    }

    public function updateVideoDetail($data, $video_id)
    {
        return $this->getDb()->updateRow("vd_pltfrm_video_dtl", $data, "`video_id` = '{$video_id}'");
    }

    public function deleteVideo($id)
    {
        $this->getDb()->updateRow("vd_pltfrm_video", [
            "is_del" => 1
        ], "`id` = '{$id}'");
    }

    public function getById($video_id)
    {
        $sql = "select * from `vd_pltfrm_video` where `id` = '{$video_id}' limit 1";
        return $this->getDb()->fetchRow($sql);
    }

    public function getDetailByVideoId($video_id)
    {
        $sql = "select * from `vd_pltfrm_video_dtl` where `video_id` = '{$video_id}' limit 1";
        return $this->getDb()->fetchRow($sql) ?: [];
    }
}

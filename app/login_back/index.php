<?php

use Tq\ShortVideoContest\Model\Branch;
use Tq\ShortVideoContest\Model\Student;
use Tq\ShortVideoContest\Model\Teacher;
use Tq\ShortVideoContest\Lib\AuthUtil;
use function Tq\Com\redirect;

require_once __DIR__ . '/../inc/global.php';


$branch_id = trim($_POST["branch_id"]);
$code = trim($_POST["code"]);

$branch_model = new Branch();
$teacher_model = new Teacher();
$student_model = new Student();

if (!$branch_id) {
	exit("缺少 branch_id 参数");
}

$branch = $branch_model->getById($branch_id);
if (!$branch) {
	exit("请联系管理员开通应用");
}

/* 当前用户默认进入的角色，如果是家长进入，优先是学生 */
$role = 0;

$access_token = $branch_model->getAccessToken($branch_id);


$userid_response = $branch_model->getUserid($access_token, $code);
$wx_userid = $userid_response["UserId"];
$parent_userid = $userid_response["parent_userid"];

if ($wx_userid) {
    $user_info = $branch_model->getUserDetail($access_token, $wx_userid);
    $teacher = $teacher_model->getByBranchIdAndWxUserid($branch_id, $wx_userid);
    if ($teacher) {
        $teacher_id = $teacher["id"];
        $teacher_model->updateTeacher([
            "title" => addslashes($user_info["name"]),
            "mobile" => addslashes($user_info["mobile"]),
        ], $teacher_id);
    } else {
        $teacher_id = $teacher_model->addTeacher([
            "branch_id" => intval($branch_id),
            "wx_userid" => addslashes($wx_userid),
            "title" => addslashes($user_info["name"]),
            "mobile" => addslashes($user_info["mobile"]),
            "school_approve" => 0,
            "area_approve" => 0,
            "add_time" => date("Y-m-d H:i:s"),
        ]);
    }
    $role_id = $teacher_id;
}


$student_id_list = [];
if ($parent_userid) {
    $role = 2; /* 覆盖教师的角色 */
    $response = $branch_model->getSchoolUserDetail($access_token, $parent_userid);
    $children = $response["parent"]["children"];
    foreach ($children as $i => $child) {

        $child_userid = $child["student_userid"];
        $response = $branch_model->getSchoolUserDetail($access_token, $child_userid);
        $student = $student_model->getByBranchIdAndUserid($branch_id, $child_userid);
        if ($student) {
            $student_id = $student["student_id"];
            $student_info = [
                "title" => addslashes($response["student"]["name"]),
            ];
            $student_model->updateById($student_info, $student_id);
        } else {
            $student_info = [
                "branch_id" => intval($branch_id),
                "wx_userid" => addslashes($child_userid),
                "title" => addslashes($response["student"]["name"]),
                "add_time" => date("Y-m-d H:i:s")
            ];
            $student_id = $student_model->addStudent($student_info);
        }

        /* 默认进入第一个学生 */
        if ($i == 0) {
            $role_id = $student_id;
        }
        $student_id_list[] = $student_id;
    }
}


$token_content = [

];

$api_token = AuthUtil::authCode(json_encode($token_content), "ENCODE", TOKEN_KEY);
redirect(HOME_PAGE_URL . "?api_token={$api_token}");

<?php

use Tq\${app_name}\Model\Branch;
use Tq\${app_name}\Model\Student;
use Tq\${app_name}\Model\Teacher;
use Tq\${app_name}\Lib\AuthUtil;
use function Tq\Com\redirect;

require_once __DIR__ . '/../inc/global.php';


$branch_id = trim($_GET["branch_id"]);
$code = trim($_GET["code"]);

$branch_model = new Branch();
$teacher_model = new Teacher();
$student_model = new Student();

if (!$branch_id) {
	exit("缺少 branch_id 参数");
}

$access_token = $branch_model->getAccessToken($branch_id);

$response = $branch_model->getUserid($access_token, $code);
$wx_userid = $response["UserId"];
$parent_userid = $response["parent_userid"];

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
        ]);
    }
    $role_id = $teacher_id;
}


if ($parent_userid) {
    $response = $branch_model->getSchoolUserDetail($access_token, $parent_userid);
	
	<#if parent_table_name??>
	$parent = $parent_model->getBySchoolAndCampus($school_id, $parent_userid);
	if ($parent) {
        $parent_id = $parent["id"];
        $parent_model->updateParent([
            
        ], $parent["id"]);
    } else {
        $parent_id = $parent_model->insertParent([
            
        ]);
    }
	</#if>
	
    $children = $response["parent"]["children"];
    foreach ($children as $i => $child) {

        $child_userid = $child["student_userid"];
        $response = $branch_model->getSchoolUserDetail($access_token, $child_userid);
		
		<#if clazz_table_name??>
		if ($clazz) {
            $clazz_id = $clazz["id"];
            $clazz_info = [
                
            ];
            $clazz_model->updateById($clazz_info, $clazz_id);
        } else {
            $clazz_info = [
                
            ];
            $clazz_id = $clazz_model->addClass($clazz_info);
        }
		</#if>

        $student = $student_model->getByBranchIdAndUserid($branch_id, $child_userid);
        if ($student) {
            $student_id = $student["student_id"];
            $student_info = [
                <#if clazz_table_name??>"class_id" => $class_id</#if>
            ];
            $student_model->updateById($student_info, $student_id);
        } else {
            $student_info = [
                <#if clazz_table_name??>"class_id" => $class_id</#if>
            ];
            $student_id = $student_model->addStudent($student_info);
        }
		
    }
}


$token_content = [

];

$api_token = AuthUtil::authCode(json_encode($token_content), "ENCODE", TOKEN_KEY);
redirect(HOME_PAGE_URL . "?api_token={$api_token}");

<?php

use Tq\ShortVideoContest\Model\Branch;
use function Tq\Com\redirect;

require_once __DIR__ . '/inc/global.php';

$corpid = trim($_GET["corp_id"]);

$branch_model = new Branch();

if (!$corpid) {
    header("Content-Type: text/html");
    exit("<h1>缺少 corp_id 参数</h1>");
}

$branch = $branch_model->getByCorpid($corpid);
if (!$branch) {
    header("Content-Type: text/html");
    exit("<h1>请联系管理员开通应用</h1>");
}
$branch_id = $branch["id"];

$query_string = http_build_query([
    "appid" => $branch["corpid"],
    "redirect_uri" => LOGIN_BACK_URL . "?branch_id={$branch_id}",
    "response_type" => "code",
    "scope" => "snsapi_base",
    "state" => "state"
]);
redirect("https://open.weixin.qq.com/connect/oauth2/authorize?{$query_string}#wechat_redirect");

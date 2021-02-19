<?php

namespace Tq\ShortVideoContest\Model;


class Branch extends Base
{

    /**
     * 通过 ID 获取学校
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        $sql = "select * from `${branch_table_name}` where `id` = '{$id}' limit 1";
        return $this->getDb()->fetchRow($sql) ?: [];
    }

    /**
     * 获取 token，如果 token 失效，则更新
     * 文档: https://work.weixin.qq.com/api/doc/90000/90135/91039
     * @param $branch_id
     * @return mixed|string
     */
    public function getAccessToken($branch_id)
    {
        /* 查询 branch 表信息 */
        $sql = "select * from `${branch_table_name}` where `id` = '{$branch_id}' limit 1";
        $school = $this->getDb()->fetchRow($sql) ?: [];
        if (!$school) {
            exit("请联系管理员开通应用");
        }

        /* 判断 access_token 是否存在，且不为空 */
        if ($school['access_token'] && strtotime($school['token_expire']) - time() > 0) {
            return $school['access_token'];
        }

        /* 从企微端获取最新 token */
        $query_string = http_build_query([
            "corpid" => $this->corpid,
            "corpsecret" => $this->secret
        ]);
        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?{$query_string}";
        $response = json_decode(file_get_contents($url), true);
        if ($response["errcode"]) {
            exit("请求失败，url: {$url}");
        }

        /* 更新 token */
        $this->getDb()->updateRow(${branch_table_name}, [
            'access_token' => $response["access_token"],
            'token_expire' => date('Y-m-d H:i:s', $response["expires_in"] + time()),//时间格式转换
        ], "`id` = '{$branch_id}'");

        return $response["access_token"];
    }


    /**
     * 获取企微通讯录 userid
     * @param $access_token
     * @param $code
     * @return array|mixed
     */
    public function getUserid($access_token, $code)
    {
        if (!$access_token || !$code) {
            return "";
        }
        $query_string = http_build_query([
            "access_token" => $access_token,
            "code" => $code
        ]);
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?{$query_string}";
        $response = json_decode(file_get_contents($url), true);
        return $response["errcode"] == 0 ? $response : "";
    }


    /**
     * 获取用户详细信息
     * 信息格式见文档： https://open.work.weixin.qq.com/api/doc/90000/90135/90196
     * @param $access_token
     * @param $wx_userid
     * @return array|bool|mixed|string
     */
    public function getUserDetail($access_token, $wx_userid)
    {
        if (!$access_token || !$wx_userid) {
            return [];
        }
        $query_string = http_build_query([
            "access_token" => $access_token,
            "userid" => $wx_userid
        ]);
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?{$query_string}";
        $response = json_decode(file_get_contents($url), true);
        return $response["errcode"] == 0 ? $response : [];
    }


    /**
     * 返回家校版的详细信息
     * @param $access_token
     * @param $wx_user_id
     * @return array|mixed
     */
    public function getSchoolUserDetail($access_token, $wx_user_id)
    {
        $params = [
            "access_token" => $access_token,
            "userid" => $wx_user_id,
        ];
        $query_string = http_build_query($params);
        $url = "https://qyapi.weixin.qq.com/cgi-bin/school/user/get?{$query_string}";
        $json = json_decode(file_get_contents($url), true);
        return $json['errcode'] ? [] : $json;
    }

}
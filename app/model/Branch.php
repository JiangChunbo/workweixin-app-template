<?php

namespace Tq\ShortVideoContest\Model;


class Branch extends Base
{

    /**
     * ͨ�� ID ��ȡѧУ
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        $sql = "select * from `vd_pltfrm_branch` where `id` = '{$id}' limit 1";
        return $this->getDb()->fetchRow($sql) ?: [];
    }

    /**
     * ��ȡ token����� token ʧЧ�������
     * �ĵ�: https://work.weixin.qq.com/api/doc/90000/90135/91039
     * @param $branch_id
     * @return mixed|string
     */
    public function getAccessToken($branch_id)
    {
        /* ��ѯ branch ����Ϣ */
        $sql = "select * from `vd_pltfrm_branch` where `id` = '{$branch_id}' limit 1";
        $school = $this->getDb()->fetchRow($sql) ?: [];
        if (!$school) {
            return "";
        }

        /* �ж� access_token �Ƿ���ڣ��Ҳ�Ϊ�� */
        if ($school['access_token'] && strtotime($school['token_expire']) - time() > 0) {
            return $school['access_token'];
        }

        /* ����΢�˻�ȡ���� token */
        $query_string = http_build_query([
            "corpid" => $this->corpid,
            "corpsecret" => $this->secret
        ]);
        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?{$query_string}";
        $response = json_decode(file_get_contents($url), true);
        if ($response["errcode"]) {
            return "";
        }

        /* ���� token */
        $this->getDb()->updateRow(vd_pltfrm_branch, [
            'access_token' => $response["access_token"],
            'token_expire' => date('Y-m-d H:i:s', $response["expires_in"] + time()),//ʱ���ʽת��
        ], "`id` = '{$branch_id}'");

        return $response["access_token"];
    }


    /**
     * ��ȡ��΢ͨѶ¼ userid
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
     * ��ȡ�û���ϸ��Ϣ
     * ��Ϣ��ʽ���ĵ��� https://open.work.weixin.qq.com/api/doc/90000/90135/90196
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
     * ���ؼ�У�����ϸ��Ϣ
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
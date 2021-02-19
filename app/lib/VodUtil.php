<?php

namespace Tq\ShortVideoContest\Lib;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Vod\V20180717\Models\DescribeMediaInfosRequest;
use TencentCloud\Vod\V20180717\Models\SearchMediaRequest;
use TencentCloud\Vod\V20180717\VodClient;

class VodUtil
{
    public static function getVodUploadSign()
    {
        // ȷ�� App ���� API ��Կ
        $secret_id = SECRET_ID;
        $secret_key = SECRET_KEY;

        // ȷ��ǩ���ĵ�ǰʱ���ʧЧʱ��
        $current = time();
        $expired = $current + 3600;  // ǩ����Ч�ڣ�1��

        // ������б��������
        $arg_list = [
            'secretId' => $secret_id,
            'currentTimeStamp' => $current,
            'expireTime' => $expired,
            'random' => rand(),
            "classId" => CLASS_ID,
            "vodSubAppId" => SUB_APP_ID,
            'procedure' => 'short_video',
        ];

        // ����ǩ��
        $original = http_build_query($arg_list);
        return base64_encode(hash_hmac('SHA1', $original, $secret_key, true) . $original);
    }


    /**
     * ע��
     * @param $file_id
     * @return \TencentCloud\Vod\V20180717\Models\DescribeMediaInfosResponse
     */
    public static function describeMediaInfos($file_id)
    {
        try {

            $cred = new Credential(SECRET_ID, SECRET_KEY);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);

            $req = new DescribeMediaInfosRequest();

            $params = array(
                "FileIds" => array($file_id),
                "Filters" => array("basicInfo"),
                "SubAppId" => SUB_APP_ID
            );
            $req->fromJsonString(json_encode($params));

            return $client->DescribeMediaInfos($req);
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }

    /**
     * ����ý�壬��ʱû��
     * @return \TencentCloud\Vod\V20180717\Models\SearchMediaResponse
     */
    public static function searchMedia()
    {
        try {

            $cred = new Credential(SECRET_ID, SECRET_KEY);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("vod.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);

            $req = new SearchMediaRequest();

            $params = array(
                "ClassIds" => array(CLASS_ID),
                "SubAppId" => SUB_APP_ID
            );
            $req->fromJsonString(json_encode($params));

            return $client->SearchMedia($req);
        } catch (TencentCloudSDKException $e) {
            echo $e;
        }
    }
}
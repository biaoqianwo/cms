<?php

class Sdy
{
    private $Url; //速递易接口地址
    public $PartnerId; //合作商家号
    public $PartnerKey; //合作商家密匙
    private $Now;
    private $ContentType;

    public function __construct()
    {
        $this->Url = 'http://open.sudiyi.cn';
        $this->PartnerId = '12995';
        $this->PartnerKey = 'jrmqgorl0lxxd6yt';
        $this->Now = date('D,d M Y H:i:s', time()) . ' GMT';
        $this->ContentType = 'application/json;charset=UTF-8';
    }

    //获取行政区域
    public function getArea()
    {
        $url = $this->Url . '/v1/area';
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("get", $contentMD5, $this->ContentType, $this->Now, "/v1/area");
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header);
    }

    //获取网点和设备信息
    public function getLattice($area_id)
    {
        $url = $this->Url . '/v1/lattice?area=' . $area_id;
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("get", $contentMD5, $this->ContentType, $this->Now, "/v1/lattice");
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header);
    }

    //获取箱格状态,type为device,按照设备id获取，type为lattice,按照网点id获取
    public function getBoxStatus($id, $type = 'device')
    {
        if ($type == 'device') {
            $url = $this->Url . '/v1/boxStatus?device=' . $id;
        } else if ($type == 'lattice') {
            $url = $this->Url . '/v1/boxStatus?lattice=' . $id;
        } else {
            return "type in('device','lattice')";
        }
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("get", $contentMD5, $this->ContentType, $this->Now, "/v1/boxStatus");
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header);
    }

    //预约箱格
    public function resv($data)
    {
        $url = $this->Url . '/v1/resv';
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("post", $contentMD5, $this->ContentType, $this->Now, "/v1/resv");
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header, 'post', json_encode($data));
    }

    public function cancelYuyue($resv_order_no)
    {
        $url = $this->Url . '/v1/resv/' . $resv_order_no;
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("post", $contentMD5, $this->ContentType, $this->Now, "/v1/resv");
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header, 'delete');
    }

    //查看预约信息
    public function  getResvStatus($resv_order_no)
    {
        $url = $this->Url . '/v1/resv/' . $resv_order_no;
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("get", $contentMD5, $this->ContentType, $this->Now, "/v1/resv/" . $resv_order_no);
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header);
    }

    //查询订单当前的详细状态
    public function  getResv($no, $type = 'resv_order_no')
    {
        if ($type == 'order_no') {
            $url = $this->Url . '/v1/resv?order_no=' . $no;
        } else if ($type == 'resv_order_no') {
            $url = $this->Url . '/v1/resv?resv_order_no=' . $no;
        } else {
            return "type in('order_no','resv_order_no')";
        }
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("get", $contentMD5, $this->ContentType, $this->Now, "/v1/resv");
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header);
    }

    //查看预约信息
    public function  closest($data = array('lat' => '30.5452', 'lnt' => '104.065'))
    {
        $url = $this->Url . '/v1/closest?lat=' . $data['lat'] . '&lng=' . $data['lnt'];
        $contentMD5 = $this->generateContentMD5("");
        $infoToSign = $this->generateInfoToSign("get", $contentMD5, $this->ContentType, $this->Now, "/v1/closest");
        $Authorization = $this->generateAuthorization($this->PartnerId, $this->PartnerKey, $infoToSign);
        $header = array(
            'Content-Type:' . $this->ContentType,
            'Date:' . $this->Now,
            'Content-MD5:' . $contentMD5,
            'Authorization:' . $Authorization,
        );
        return $this->curl($url, $header);
    }

    //cur方法，设置头部信息
    //http://blog.csdn.net/lengxue789/article/details/8254667
    public function curl($url, $header, $type = 'get', $params = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } elseif ($type == 'put') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } elseif ($type == 'delete') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;

    }

    //生成ContentMD5
    private function generateContentMD5($str)
    {
        return base64_encode(md5($str));
    }

    //生成InfoToSign
    private function generateInfoToSign($method, $contentMD5, $contentType, $date, $url)
    {
        $n = "\n";
        return strtoupper($method) . $n . $contentMD5 . $n . $contentType . $n . $date . $n . $url;
    }

    //生成Authorization
    private function generateAuthorization($partner_id, $partner_key, $infoToSign)
    {
        return "SDY " . $partner_id . ":" . base64_encode(hash_hmac("sha1", $infoToSign, $partner_key, true));
    }
}
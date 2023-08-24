<?php

// 云市场分配的密钥Id
$secretId = 'AKIDcaEnG2B2rkq49v3LWYCc97oTMsfkDpRb0Xg4';
// 云市场分配的密钥Key
$secretKey = '4enJiCNQIdI216mzROJiHHofRri7Irhu6IIq4mym';
$source = 'market';

// 签名
$datetime = gmdate('D, d M Y H:i:s T');
$signStr = sprintf("x-date: %s\nx-source: %s", $datetime, $source);
$sign = base64_encode(hash_hmac('sha1', $signStr, $secretKey, true));
$auth = sprintf('hmac id="%s", algorithm="hmac-sha1", headers="x-date x-source", signature="%s"', $secretId, $sign);

// 请求方法
$method = 'POST';
// 请求头
$headers = array(
    'X-Source' => $source,
    'X-Date' => $datetime,
    'Authorization' => $auth,
    
);
// 查询参数
$queryParams = array (

);
// body参数（POST方法下）
$bodyParams = array (
    'cardNo' => '500228200206203375',
    'realName' => '向佳俊',
);
// url参数拼接
$url = 'https://service-18c38npd-1300755093.ap-beijing.apigateway.myqcloud.com/release/idcard/VerifyIdcardv2';
if (count($queryParams) > 0) {
    $url .= '?' . http_build_query($queryParams);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//ssl证书不验证
curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function ($v, $k) {
    return $k . ': ' . $v;
}, array_values($headers), array_keys($headers)));
if (in_array($method, array('POST', 'PUT', 'PATCH'), true)) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
}

$data = curl_exec($ch);
if (curl_errno($ch)) {
    echo "Error: " . curl_error($ch);
} else {
    print_r($data);
}
curl_close($ch);
?>  
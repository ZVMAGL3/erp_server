<?php
    // 设置响应头，允许跨域请求
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS"); // 添加 OPTIONS 请求方法
    header("Access-Control-Allow-Headers: Content-Type"); // 允许的请求头，确保包含 Content-Type
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json; charset=UTF-8");
    include('./config/db.php');
    include('./config/db_vip.php');
    include('./config/conn.php');

    // 创建数据库实例
    $db = new Database($dbconfig);
    // 创建数据库实例
    $db_vip = new Database($db_vipconfig);



    // 获取 POST 请求的 JSON 数据
    $requestData = json_decode(file_get_contents('php://input'), true);

    // 查询语句和参数
    $query = "SELECT * FROM msc_user_reg WHERE SYS_ShouJi = ? AND SYS_PassWord = ?";
    $params = array($requestData['identification'],$requestData['identityHash']);
    // 执行查询
    $queryResult = $db->query($query, $params);

    if ($queryResult['error'] == null) {
        $response = array(
            "isLonggedIn" => true,
            "numRows" => $db -> numRows($queryResult['result']),
        );
    } else {
        $response = array(
            "isLonggedIn" => false,
            'error' => $queryResult['error']
        );
    }

    // 返回 JSON 格式的响应
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
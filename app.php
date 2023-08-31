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
    require './vendor/AltoRouter-master/AltoRouter.php';
    $router = new AltoRouter();

    // 创建数据库实例
    $db = new Database($dbconfig);
    // 创建数据库实例
    $db_vip = new Database($db_vipconfig);

    $router->map('POST', '/app.php/login', function() use ($db){

        // 获取 POST 请求的 JSON 数据
        $requestData = json_decode(file_get_contents('php://input'), true);

        // $selectedColumns = array(
        //     "id",
        //     "SYS_GongHao",
        //     "SYS_UserName",
        //     "SYS_XingBie",
        //     "SYS_DiZhi",
        //     "SYS_ShouJi",
        //     "SYS_Email",
        //     "SYS_qianmin",
        //     "SYS_Company_id",
        //     "SYS_reg_num",
        //     "SYS_QuanXian",
        //     "sys_web_shenpi",
        //     "SYS_ZD_ZaiZhiZhuangTai",
        //     "YinXingKaHao",
        // );
        
        // 将列名数组转换为逗号分隔的字符串
        // $columnsString = implode(", ", $selectedColumns);

        // 查询语句和参数
        $query = "SELECT * FROM msc_user_reg WHERE SYS_ShouJi = ? AND SYS_PassWord = ?";
        $params = array($requestData['mobile'],$requestData['identityHash']);
        // 执行查询
        $queryResult = $db->query($query, $params);

        if ($queryResult['error'] == null) {
            if ($db->numRows($queryResult['result']) > 0) {
                // $userinfo = array();
                // while ($row = mysqli_fetch_assoc($queryResult['result'])) {
                //     $userinfo[] = $row;
                // }
                $response = array(
                    "isLonggedIn" => true,
                    'userinfo' =>  mysqli_fetch_assoc($queryResult['result']),
                    "error" => null,
                );
            } else {
                $response = array(
                    "isLonggedIn" => false,
                    "error" => "账号或密码错误",
                );
            }
        } else {
            $response = array(
                "isLonggedIn" => false,
                'error' => $queryResult['error']
            );
        }

        // 返回 JSON 格式的响应
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    });

    $router->map('POST', '/app.php/register', function() use ($db){

        // 获取 POST 请求的 JSON 数据
        $requestData = json_decode(file_get_contents('php://input'), true);

        $isValidData = true;
        $pattern = [
            '/^(?![0-9])(?!.*[?!@&|\/])[^\s]{1,16}$/', //不为以数字开头
            '/^(\+\d{2,3}\-)?\d{11}$/', //手机号
            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&~])[A-Za-z\d@$!%*?&~]{8,}$/',   //password
        ];

        $Prompt = [
            '请输入用户名,第一个不能为数字',
            '请输入正确的手机号',
            '8-12位,需包含数字、大写字母、小写字母和特殊符号(@$!%*?&~)',
            '确认密码必须和设置的密码一致'
        ];

        //在这里验证数据符不符合
        for($i = 0; $i < 3; $i++){
            if(!preg_match($pattern[$i], $requestData[$i])){
                $isValidData = false;
                echo $Prompt[$i];
                break;
            }
        }
        if($isValidData){
            if($requestData[2] !== $requestData[3]){
                $isValidData = false;
                echo $Prompt[3];
            }
        }
        if($isValidData){
            // 查询语句和参数
            $query = "";
            $params = "";
            // 执行查询
            $queryResult = $db->query($query, $params);

            if ($queryResult['error'] == null) {
                if ($db->numRows($queryResult['result']) > 0) {
                    $response = array(
                        "isLonggedIn" => true,
                        'userinfo' =>  mysqli_fetch_assoc($queryResult['result']),
                        "error" => null,
                    );
                } else {
                    $response = array(
                        "isLonggedIn" => false,
                        "error" => "账号或密码错误",
                    );
                }
            } else {
                $response = array(
                    "isLonggedIn" => false,
                    'error' => $queryResult['error']
                );
            }

            // 返回 JSON 格式的响应
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    });

    $match = $router->match();
    
    if ($match) {
        $match['target']();
    } else {
        var_dump($router);
    }
 
?>
<?php
	class Database {
		private $host;
		private $username;
		private $password;
		private $database;
		private $connection;

		public function __construct($dbconfig) {
			$this->host = $dbconfig['host'];
			$this->username = $dbconfig['username'];
			$this->password = $dbconfig['password'];
			$this->database = $dbconfig['database'];

			// 建立数据库连接
			$this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database);
			if (!$this->connection) {
				die('Could not connect: ' . mysqli_connect_error());
			}
		}
		
		public function query($sql, $params = array(), $types = "") {
			$stmt = mysqli_prepare($this->connection, $sql);
			if ($stmt) {
				if (!empty($params)) {
					$paramCount = count($params);
					if ($types === "") {
						$types = str_repeat('s', $paramCount); // 默认为字符串类型
					}
		
					// 准备绑定参数的数组，第一个元素为预处理语句，第二个元素为类型字符串
					$bindParams = array($stmt, $types);
		
					// 将参数的引用添加到绑定参数数组中
					foreach ($params as $key => $_) {
						$bindParams[] = &$params[$key];
					}
		
					// 使用 call_user_func_array 来动态调用 mysqli_stmt_bind_param
					call_user_func_array("mysqli_stmt_bind_param", $bindParams);
				}
		
				// 执行预处理语句
				$result = mysqli_stmt_execute($stmt);
				if ($result) {
					// 获取结果集对象
					$result_set = mysqli_stmt_get_result($stmt);
					
					// 关闭预处理语句
					mysqli_stmt_close($stmt);
					
					return array(
						'result' => $result_set,
						'error' => null
					);
				} else {
					// 关闭预处理语句
					mysqli_stmt_close($stmt);
					
					return array(
						'result' => null,
						'error' => 'Query failed: ' . mysqli_error($this->connection)
					);
				}
			} else {
				die('Preparation failed: ' . mysqli_error($this->connection));
			}
		}

		public function fetchArray($result) {
			// 获取查询结果的数组形式
			return mysqli_fetch_array($result);
		}

		public function numRows($result) {
			// 获取查询结果的行数
			return mysqli_num_rows($result);
		}

		public function escapeString($str) {
			// 转义字符串中的特殊字符
			return mysqli_real_escape_string($this->connection, $str);
		}

		public function __destruct() {
			// 关闭数据库连接
			mysqli_close($this->connection);
		}
	}
?>
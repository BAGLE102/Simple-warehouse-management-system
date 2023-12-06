<?php

// 設定資料庫連接參數
$servername = "localhost"; // MySQL 伺服器名稱
$username = "root"; // MySQL 使用者名稱
$password = ""; // MySQL 使用者密碼
$dbname = "warehouse_management_system"; // 資料庫名稱

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

//設定萬國碼
$conn ->set_charset('utf8');

// 檢查連接狀態
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


?>

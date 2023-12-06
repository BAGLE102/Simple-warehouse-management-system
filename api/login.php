<?php

header('Content-Type: application/json; charset=utf-8');

include_once "connectDB.inc.php";

// 檢查請求方法是否為POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 從POST請求主體中獲取用戶名和密碼
    $username = htmlspecialchars(trim($_POST['account']));
    $password = htmlspecialchars(trim($_POST['password']));

    // 執行SQL查詢以獲取指定用戶名的用戶
    $sql = "SELECT * FROM user WHERE account='$username'";
    $result = $conn->query($sql);

    // 檢查是否找到任何用戶
    if ($result->num_rows > 0) {

      // 從結果集中獲取第一行
      $row = $result->fetch_assoc();

      // 檢查密碼是否正確
      if ($row['password'] === $password) {
        // 密碼正確，回傳 JSON 格式的回應
        $response = array(
          'status' => 'success',
          'message' => 'sign in suceesfully',
          'permissions'=>'guests'
        );
        if($row['permissions']!=null){
          $response = array(
            'status' => 'success',
            'message' => 'sign in suceesfully',
            'permissions'=>'admin'
          );
        }
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($response);

      } else {
        // 密碼不正確，回傳 JSON 格式的回應
        $response = array(
          'status' => 'error',
          'message' => 'wrong password'
        );
        header("HTTP/1.1 401 Unauthorized");
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($response);
      }

    } else {
      header("HTTP/1.1 404 Not Found"); // 設定響應的狀態碼為 404 Not Found
      header("Content-Type: application/json; charset=UTF-8");
      // 找不到用戶，回傳 JSON 格式的回應
      $response = array(
        'status' => 'error',
        'message' => 'user not found',
        'permissions'=>'null'
      );
      echo json_encode($response);
    }
}
?>

<?php

  include_once "connectDB.inc.php";

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

      // 執行 SQL 查詢以取得所有用戶
      $sql = "SELECT count, product, ticketNo,manufacturingDate, status, user,lastModified, note, quantity FROM product_data"; // 查詢語句
      $result = $conn->query($sql); // 執行查詢
    
      // 檢查是否有找到任何用戶
      if ($result->num_rows > 0) {
      
        // 建立陣列以存儲資料
        $product_data = array();
    
        // 迴圈處理結果集中的每一行
        while($row = $result->fetch_assoc()) {
          
          // 將各項資料加到陣列中
          $product_data[] = array(
            'count' => $row['count'], // count 欄位
            'product' => $row['product'], // product 欄位
            'ticketNo' => $row['ticketNo'], // ticketNo 欄位
            'manufacturingDate' => $row['manufacturingDate'], // Manufacturing Date 欄位
            'status' => $row['status'], // status 欄位
            'user' => $row['user'], // user 欄位
            'lastModified' => $row['lastModified'], // Last Modified 欄位
            'note' => $row['note'], // note 欄位
            'quantity'=>$row['quantity']//quantity欄位
          );
        }
        
        // 輸出用戶的 JSON 數組
        header('Content-Type: application/json'); // 設定響應的 Content-Type 為 JSON
        echo json_encode($product_data); // 將零件表的 JSON 數組輸出到頁面
    
      } else {
        header("HTTP/1.1 404 Not Found"); // 設定響應的狀態碼為 404 Not Found
        die(); // 結束程式執行
      }
    }
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {    

      // 取得表單提交的資料
      $product = $_POST['product'];
      $ticketNo = $_POST['ticketNo'];
      $manufacturingDate = $_POST['manufacturingDate'];
      $status = $_POST['status'];
      $note = $_POST['note'];
      $user = $_POST['user'];
      $lastModified = $_POST['lastModified'];
      $quantity = $_POST['quantity'];

      // 建立SQL指令
      $sql = "INSERT INTO product_data (product, ticketNo,manufacturingDate, status, user, lastModified, note,quantity) 
      VALUES ('$product', '$ticketNo', '$manufacturingDate', '$status', '$user', '$lastModified', '$note','$quantity')";

      // 執行SQL指令
      if ($conn->query($sql) === TRUE) {
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json; charset=UTF-8");
        $data = array(
          "product" => $product,
          "ticketNo" => $ticketNo,
          "manufacturingDate" => $manufacturingDate,
          "status" => $status,
          "note" => $note,
          "user" => $user,
          "quantity" => $quantity,
          "lastModified" => $lastModified
      );
        $response = array(
          'status' => 'success',
          'message' => 'Data added successfully',
          'data' => $data
        );
        echo json_encode($response);
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        header("Content-Type: application/json; charset=UTF-8");
        $response = array(
          'status' => 'error',
          'message' => 'Writing to database failed'
        );
        echo json_encode($response);
    }
  }
  if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  
    // 取得請求的資料
    $data = json_decode(file_get_contents("php://input"), true);
    
    // 取得修改的資料
    $id = $data['id'];
    $product = $data['product'];
    $ticketNo = $data['ticketNo'];
    $manufacturingDate = $data['manufacturingDate'];
    $status = $data['status'];
    $note = $data['note'];
    $user = $data['user'];
    $quantity = $data['quantity'];
    $lastModified = $data['lastModified'];
  
    // 建立 SQL 指令
    $sql = "UPDATE product_data SET 
      product = '$product',
      ticketNo = '$ticketNo',
      manufacturingDate = '$manufacturingDate',
      status = '$status',
      user = '$user',
      lastModified = '$lastModified',
      note = '$note',
      quantity = '$quantity'
    WHERE count = '$id'"; // 修改 id 的資料
  
    // 執行 SQL 指令
    if ($conn->query($sql) === TRUE) {
      header("HTTP/1.1 200 OK");
      header("Content-Type: application/json; charset=UTF-8");
      $response = array(
        'status' => 'success',
        'message' => 'Data modified successfully',
        'data' => $data
      );
      echo json_encode($response);
    } else {
      header("HTTP/1.1 500 Internal Server Error");
      header("Content-Type: application/json; charset=UTF-8");
      $response = array(
        'status' => 'error',
        'message' => 'Writing to database failed',
        'dbmessage' => $conn->error
      );
      echo json_encode($response);
    }
  }
  
  // 關閉資料庫連接
  $conn->close();

?>
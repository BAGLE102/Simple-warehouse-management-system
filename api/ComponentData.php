<?php

  header('Content-Type: text/html; charset=UTF-8');

  include_once "connectDB.inc.php";

// 確認請求方式是否為 GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

  // 執行 SQL 查詢以取得所有零件資料
  $sql = "SELECT count, product, quantity, type, partNo, moq, supplier, user, lastModified, model, note, status, price FROM component_data"; // 查詢語句
  $result = $conn->query($sql); // 執行查詢

  // 檢查是否有找到任何資料
  if ($result->num_rows > 0) {

    // 建立陣列以存儲零件資料
    $component_data = array();

    // 迴圈處理結果集中的每一行
    while($row = $result->fetch_assoc()) {

      // 將各項零件資料加到陣列中
      $component_data[] = array(
        'count' => $row['count'], // 零件編號
        'product' => $row['product'], // 零件名稱
        'quantity' => $row['quantity'], // 數量
        'type' => $row['type'], // 零件類型
        'partNo' => $row['partNo'], // 零件編號
        'moq' => $row['moq'], // 最小訂購量
        'supplier' => $row['supplier'], // 供應商
        'user' => $row['user'], // 最後修改者
        'price'=>$row['price'], // 價格
        'status'=>$row['status'], // 零件狀態
        'model'=>$row['model'],
        'lastModified' => $row['lastModified'], // 最後修改時間
        'note' => $row['note'] // 備註
      );
    }

    // 設定響應的 Content-Type 為 JSON
    header('Content-Type: application/json');

    // 將零件資料的 JSON 數組輸出到頁面
    echo json_encode($component_data);
  } else {
    // 若沒有找到任何資料，設定響應的狀態碼為 404 Not Found
    header("HTTP/1.1 404 Not Found");

    // 結束程式執行
    die();
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    

  // 取得表單提交的資料
  $product = $_POST['product'];
  $quantity = $_POST['quantity'];
  $type = $_POST['type'];
  $partNo = $_POST['partNo'];
  $moq = $_POST['moq'];
  $supplier = $_POST['supplier'];
  $note = $_POST['note'];
  $user = $_POST['user'];
  $lastModified = $_POST['lastModified'];
  $status = $_POST['status'];
  $price = $_POST['price'];
  $model = $_POST['model'];

//----------------------------------------------------------------------------------------------------------------
  $sql = "SELECT * FROM component_num WHERE product = '$product' AND supplier = '$supplier' AND partNo = '$partNo'";
  $result = $conn->query($sql);
  if($result -> num_rows > 0){
    
    $row = $result->fetch_assoc();
    
    if($status == "出貨"){
      $remainingQuantity = $quantity*(-1) + $row['remainingQuantity']; 
    }else{
      $remainingQuantity = $quantity + $row['remainingQuantity']; 
    }

    // 建立 SQL 指令
    $sql = "UPDATE component_num SET remainingQuantity = '$remainingQuantity'
    WHERE  product = '$product' AND supplier = '$supplier' AND partNo = '$partNo'";

    $conn->query($sql);

  }else{
    if($status == "出貨"){
      $remainingQuantity = $quantity*(-1) ; 
    }else{
      $remainingQuantity = $quantity; 
    }

    // 建立 SQL 指令
    $sql ="INSERT INTO component_num(product,remainingQuantity,supplier,partNo)
    VALUES('$product','$remainingQuantity','$supplier','$partNo')";

    $conn->query($sql);

  }
//----------------------------------------------------------------------------------------------------------------    
  // 建立SQL指令
  $sql = "INSERT INTO component_data (product, quantity, type, partNo, moq, supplier, user, lastModified, note,status,price,model) 
  VALUES ('$product', '$quantity', '$type', '$partNo', '$moq', '$supplier', '$user', '$lastModified', '$note','$status','$price','$model')";

  // 執行SQL指令
  if ($conn->query($sql) === TRUE) {
    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json; charset=UTF-8");
    $data = array(
      "product" => $product,
      "quantity" => $quantity,
      "type" => $type,
      "partNo" => $partNo,
      "moq" => $moq,
      "supplier" => $supplier,
      "user" => $user,
      "lastModified" => $lastModified,
      "note" => $note,
      "status"=>$status,
      "price"=>$price,
      "model"=>$model
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
      'message' => 'Writing to database failed',
      'ads'=> $con->$error
    );
    echo json_encode($response);

  }

}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

  // 取得請求的資料
  $data = json_decode(file_get_contents("php://input"), true);
  

  // 取得表單提交的資料
  $id = $data['id'];
  $product = $data['product'];
  $quantity = $data['quantity'];
  $type = $data['type'];
  $partNo = $data['partNo'];
  $moq = $data['moq'];
  $supplier = $data['supplier'];
  $note = $data['note'];
  $user = $data['user'];
  $lastModified = $data['lastModified'];
  $status = $data['status'];
  $price = $data['price'];
  $model = $data['model'];
//----------------------------------------------------------------------------------------------------------------
  $sql = "SELECT * FROM component_data WHERE  count = '$id'";
  $result = $conn->query($sql);

  if(true){
    
    $row_con = $result->fetch_assoc();

    $product_con = $row_con['product'];
    $supplier_con = $row_con['supplier'];
    $partNo_con = $row_con['partNo'];

    $sql = "SELECT * FROM component_num WHERE product = '$product_con' AND supplier = '$supplier_con' AND partNo = '$partNo_con'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if($row_con['status'] == "出貨"){
      $remainingQuantity = $row_con['quantity'] + $row['remainingQuantity']; 
    }else{
      $remainingQuantity = ($row_con['quantity']*(-1)) + $row['remainingQuantity']; 
    }

    // 建立 SQL 指令
    $sql = "UPDATE component_num SET remainingQuantity = '$remainingQuantity'
    WHERE product = '$product_con' AND supplier = '$supplier_con' AND partNo = '$partNo_con'";
    $conn->query($sql);
  
  }


  $sql = "SELECT * FROM component_num WHERE product = '$product' AND supplier = '$supplier' AND partNo = '$partNo'";
  $result = $conn->query($sql);

  if($result -> num_rows > 0){
    
    $row = $result->fetch_assoc();
    
    if($status == "出貨"){
      $remainingQuantity = $quantity*(-1) + $row['remainingQuantity']; 
    }else{
      $remainingQuantity = $quantity + $row['remainingQuantity']; 
    }

    // 建立 SQL 指令
    $sql = "UPDATE component_num SET remainingQuantity = '$remainingQuantity'
    WHERE  product = '$product' AND supplier = '$supplier' AND partNo = '$partNo'";
    $conn->query($sql);

  }else{
    if($status == "出貨"){
      $remainingQuantity = $quantity*(-1) + $row['remainingQuantity']; 
    }else{
      $remainingQuantity = $quantity + $row['remainingQuantity']; 
    }

    // 建立 SQL 指令
    $sql ="INSERT INTO component_num(product,remainingQuantity,supplier,partNo)
    VALUES('$product','$remainingQuantity','$supplier','$partNo')";
    $conn->query($sql);

  }
//----------------------------------------------------------------------------------------------------------------

  // 建立 SQL 指令
  $sql = "UPDATE component_data SET 
    product = '$product',
    quantity = '$quantity',
    type = '$type',
    partNo = '$partNo',
    moq = '$moq',
    supplier = '$supplier',
    lastModified = '$lastModified',
    user = '$user',
    note = '$note',
    status = '$status',
    price = '$price',
    model = '$model'
  WHERE count = '$id'"; // 修改 id 的資料

  // 執行 SQL 指令
  if ($conn->query($sql) === TRUE) {
    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json; charset=UTF-8");
    $response = array(
      'status' => 'success',
      'message' => 'Data added successfully',
    );
    echo json_encode($response);
  } else {
    header("HTTP/1.1 500 Internal Server Error");
    header("Content-Type: application/json; charset=UTF-8");
    $response = array(
      'status' => 'error',
      'message' => 'Writing to database failed',
      'dbmessage' => $conn->$error
    );
    echo json_encode($response);
    }
  
}
// 關閉資料庫連接
$conn->close();

?>
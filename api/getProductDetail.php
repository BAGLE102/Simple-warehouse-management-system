<?php

include_once "connectDB.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $product = $_POST['product'];

    // 執行 SQL 查詢以取得所有零件資料
    $sql = "SELECT * FROM component_data WHERE product ='$product'"; // 查詢語句
    $result = $conn->query($sql); // 執行查詢

    // 檢查是否有找到任何用戶
    if ($result->num_rows > 0) {

        // 建立陣列以存儲資料
        $product_data = array();

        // 迴圈處理結果集中的每一行
        while($row = $result->fetch_assoc()) {
            
            // 將各項資料加到陣列中
            $product_data[] = array(
               'product' => $row['product'], // product 欄位
               'partNo' => $row['partNo'], // 零件編號
               'supplier' => $row['supplier'] // 供應商
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

?>
<?php

header('Content-Type: application/json; charset=utf-8');

include_once "connectDB.inc.php";

// 檢查請求方法是否為POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 從POST請求主體中獲取日期
    $manufacturingDate = $_POST['manufacturingDate'];

    // 執行SQL查詢以獲取指定日期的工單編號
    $sql = "SELECT * FROM workoder_data WHERE manufacturingDate='$manufacturingDate'";
    $result = $conn->query($sql);

    // 檢查是否有查詢到資料
    if ($result->num_rows > 0) {
        // 建立陣列以存儲工單編號
        $ticketNos = array();
        while($row = $result->fetch_assoc()) {
            if($row['productStatus']=="完成")
                // 將工單編號加到陣列中
                $ticketNos[] = $row['ticketNo'];
        }

        // 回傳工單編號的 JSON 數組
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json; charset=UTF-8");
        $response = array(
            'status' => 'success',
            'ticketNos' => $ticketNos
        );
        echo json_encode($response);
    } else {
        header("HTTP/1.1 404 Not Found");
        header("Content-Type: application/json; charset=UTF-8");
        $response = array(
            'status' => 'error',
            'message' => 'No tickets found for the specified date'
        );
        echo json_encode($response);
    }
}
?>
<?php

    include_once "connectDB.inc.php";

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // 執行 SQL 查詢以取得所有用戶
        $sql = "SELECT count, ticketNo,productName, productModel,productQuantity,productStatus,manufacturingDate, user,lastModified, note FROM workoder_data"; // 查詢語句
        $result = $conn->query($sql); // 執行查詢

        // 檢查是否有找到任何用戶
        if ($result->num_rows > 0) {

        // 建立陣列以存儲資料
        $workoder_data = array();

        // 迴圈處理結果集中的每一行
        while($row = $result->fetch_assoc()) {

            // 將各項資料加到陣列中
            $workoder_data[] = array(
                'count' => $row['count'], // count 欄位
                'ticketNo' => $row['ticketNo'], // ticketNo 欄位
                'productName' => $row['productName'], // productName 欄位
                'productModel' => $row['productModel'], // productModel 欄位
                'productQuantity' => $row['productQuantity'], // productQuantity  欄位
                'productStatus' => $row['productStatus'], // productStatus 欄位
                'manufacturingDate' => $row['manufacturingDate'], // Manufacturing Date 欄位
                'user' => $row['user'], // user 欄位
                'lastModified' => $row['lastModified'], // Last Modified 欄位
                'note' => $row['note'] // note 欄位
            );
        }

        // 輸出用戶的 JSON 數組
        header('Content-Type: application/json'); // 設定響應的 Content-Type 為 JSON
        echo json_encode($workoder_data); // 將零件表的 JSON 數組輸出到頁面

        } else {
            header("HTTP/1.1 404 Not Found"); // 設定響應的狀態碼為 404 Not Found
            die(); // 結束程式執行
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {    

        // 取得表單提交的資料
        $ticketNo = $_POST['ticketNo'];
        $productName = $_POST['productName'];
        $productModel = $_POST['productModel'];
        $productQuantity = $_POST['productQuantity'];
        $productStatus = $_POST['productStatus'];
        $manufacturingDate = $_POST['manufacturingDate'];
        $user = $_POST['user'];
        $lastModified = $_POST['lastModified'];
        $note = $_POST['note'];

        

        // 建立SQL指令
        $sql = "INSERT INTO workoder_data (ticketNo,productName,productModel,productQuantity, productStatus, manufacturingDate,user, lastModified, note) 
        VALUES ('$ticketNo','$productName',  '$productModel', '$productQuantity','$productStatus','$manufacturingDate' ,'$user', '$lastModified', '$note')";

        // 執行SQL指令
        if ($conn->query($sql) === TRUE) {
            header("HTTP/1.1 200 OK");
            header("Content-Type: application/json; charset=UTF-8");
            $data = array(
                "ticketNo" => $ticketNo,
                "productName" => $productName,
                "productModel" => $productModel,
                "productQuantity" => $productQuantity,
                "productStatus" => $productStatus,
                "manufacturingDate" => $manufacturingDate,
                "user" => $user,
                "lastModified" => $lastModified,
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
            'dbmessage'=> $conn ->error
            );
            echo json_encode($response);
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

        // 取得請求的資料
        $data = json_decode(file_get_contents("php://input"), true);

        // 取得修改的資料
        $id = $data['id'];
        $ticketNo = $data['ticketNo'];
        $productName = $data['productName'];
        $productModel = $data['productModel'];
        $productQuantity = $data['productQuantity'];
        $productStatus = $data['productStatus'];
        $manufacturingDate = $data['manufacturingDate'];
        $user = $data['user'];
        $lastModified = $data['lastModified'];
        $note = $data['note'];

        // 建立 SQL 指令
        $sql = "UPDATE workoder_data SET 
            ticketNo = '$ticketNo',
            productName = '$productName',
            productModel = '$productModel',
            productQuantity = '$productQuantity',
            productStatus = '$productStatus',
            manufacturingDate = '$manufacturingDate',
            user = '$user',
            lastModified = '$lastModified',
            note = '$note'
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
            'message' => 'Writing to database failed'
            );
            echo json_encode($response);
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    
        // 取得要刪除的資料 count
        $id = $_GET['id'];
    
        // 建立 SQL 指令
        $sql_delete_detail = "DELETE FROM WorkOder_detail WHERE WorkOderNo = '$id'";
        $sql_delete_data = "DELETE FROM WorkOder_data WHERE count = '$id'";
    
        // 執行 SQL 指令
        if ($conn->query($sql_delete_detail) === TRUE && $conn->query($sql_delete_data) === TRUE) {
            header("HTTP/1.1 200 OK");
            header("Content-Type: application/json; charset=UTF-8");
            $response = array(
                'status' => 'success',
                'message' => 'Data deleted successfully'
            );
            echo json_encode($response);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            header("Content-Type: application/json; charset=UTF-8");
            $response = array(
                'status' => 'error',
                'message' => 'Deleting data failed',
                'dbmessage' => $conn->error
            );
            echo json_encode($response);
        }
    }
    
    

    // 關閉資料庫連接
    $conn->close();

?>
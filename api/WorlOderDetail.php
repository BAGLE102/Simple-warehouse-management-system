<?php

include_once "connectDB.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {


        $workOderNo =trim($_GET['workOderNo']);
        // 執行 SQL 查詢以取得所有用戶

        $sql = "SELECT * FROM workoder_detail WHERE workOderNo = '$workOderNo'"; // 查詢語句
        $result = $conn->query($sql); // 執行查詢

        // 檢查是否有找到任何用戶
        if ($result->num_rows >= 0) {

            // 建立陣列以存儲資料
            $workoder_data = array();
            //$workoder_data[] =array('workOderNo' => $workOderNo);
            // 迴圈處理結果集中的每一行
            while($row = $result->fetch_assoc()) {

                // 將各項資料加到陣列中
                $workoder_data[] = array(
                    'count' => $row['count'], // count 欄位
                    'product' => $row['product'], // product 欄位
                    'partNo' => $row['partNo'], // partNo 欄位
                    'consumptionQuantity' => $row['consumptionQuantity'], // consumptionQuantity  欄位
                    'supplier' => $row['supplier'], // Product_Status 欄位
                    'remainingQuantity' => $row['remainingQuantity'], // remainingQuantity 欄位
                    'lastModified' => $row['lastModified'], // Last Modified 欄位
                    'note' => $row['note'] // note 欄位
                );
            }

            // 輸出用戶的 JSON 數組
            header('Content-Type: application/json'); // 設定響應的 Content-Type 為 JSON
            echo json_encode($workoder_data); // 將零件表的 JSON 數組輸出到頁面

        } else {
            header("HTTP/1.1 404 Not Found"); // 設定響應的狀態碼為 404 Not Found
            $response = array(
                'status' => 'error',
                'message' => 'Can not fount the data',
                'workOderNo' => $workOderNo,
                'dbmessage' => $conn -> error
                );
             echo json_encode($response);
            die(); // 結束程式執行
        }
    }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {    

    // 取得表單提交的資料
    $workOderNo = trim($_POST['workOderNo']);
    $product = trim($_POST['product']);
    $partNo = trim($_POST['partNo']);
    $consumptionQuantity = trim($_POST['consumptionQuantity']);
    $supplier = trim($_POST['supplier']);
    $lastModified = trim($_POST['lastModified']);
    $note = trim($_POST['note']);
//----------------------------------------------------------------------------------------------------------------
    $sql = "SELECT remainingQuantity FROM component_num 
    WHERE product = '$product' AND supplier = '$supplier' AND partNo = '$partNo'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $remainingQuantity = $row['remainingQuantity'] - $consumptionQuantity;
    // 建立 SQL 指令
    $sql = "UPDATE component_num SET remainingQuantity = '$remainingQuantity'
    WHERE  product = '$product' AND supplier = '$supplier' AND partNo = '$partNo'";

//----------------------------------------------------------------------------------------------------------------

    // 建立SQL指令
     $sqla = "INSERT INTO workoder_detail (workOderNo,product, partNo,consumptionQuantity, supplier, remainingQuantity, lastModified, note) 
    VALUES ('$workOderNo',  '$product', '$partNo','$consumptionQuantity', '$supplier','$remainingQuantity' ,'$lastModified', '$note')";


    // 執行SQL指令
    if ($conn->query($sqla) === TRUE) {
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json; charset=UTF-8");
        $data = array(
            "workOderNo" => $workOderNo,
            "product" => $product,
            "partNo" => $partNo,
            "consumptionQuantity" => $consumptionQuantity,
            "supplier" => $supplier,
            "remainingQuantity" => $remainingQuantity,
            "lastModified" => $lastModified,
            "note" => $note
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
        'dbmessage' => $conn -> error
        );
        echo json_encode($response);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

        // 取得請求的資料
        $data = json_decode(file_get_contents("php://input"), true);

        // 取得修改的資料
        $id = $data['id'];
        $workOderNo = $data['workOderNo'];
        $product = $data['product'];
        $partNo = $data['partNo'];
        $consumptionQuantity = $data['consumptionQuantity'];
        $supplier = $data['supplier'];
        $remainingQuantity = $data['remainingQuantity'];
        $lastModified = $data['lastModified'];
        $note = $data['note'];
//------------------------------------------------------------------------------
        // 建立 SQL 查詢指令
        $sql = "SELECT * FROM componentdata WHERE partNo = '$partNo'";

        // 執行 SQL 查詢指令
        $result = $conn->query($sql);

        // 檢查是否有查詢到資料
        if ($result->num_rows > 0) {
        // 取得第一筆查詢到的資料
        $row = $result->fetch_assoc();
        $quantity = $row['quantity'];
        $remainingQuantity = $quantity - $consumptionQuantity;
        } else {
        header("HTTP/1.1 404 Not Found"); // 設定響應的狀態碼為 404 Not Found
        // 回傳查詢失敗訊息
        $response = array(
            'status' => 'error',
            'message' => 'Data not found'
        );
        echo json_encode($response);
        die();
        }
//------------------------------------------------------------------------------
        // 建立 SQL 指令
        $sql = "UPDATE workoder_detail SET 
            workOderNo = '$workOderNo',
            product = '$product',
            partNo = '$partNo',
            consumptionQuantity = '$consumptionQuantity',
            supplier = '$supplier',
            remainingQuantity = '$remainingQuantity',
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
            'message' => 'Writing to database failed',
            'dbmessage' => $conn -> error
            );
            echo json_encode($response);
        }
    }
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

        // 取得要刪除的資料 count
        $id = $_GET['id'];
//---------------------------------------------------------------------------------
        // 建立 SQL 查詢指令
        $sql = "SELECT * FROM workoder_detail WHERE count = '$id'";

        // 執行 SQL 查詢指令
        $result = $conn->query($sql);

        // 檢查是否有查詢到資料
        if ($result->num_rows > 0) {
        // 取得第一筆查詢到的資料
        $row = $result->fetch_assoc();
        $quantity = $row['quantity'];
        $partNo = $row['partNo']; // Product_Model 欄位
        $remainingQuantity = $quantity + $consumptionQuantity;
      //  $sql = "UPDATE componentdata SET quantity = $remainingQuantity WHERE partNo = '$partNo'";
      //  $result = $conn->query($sql);

        } else {
        header("HTTP/1.1 404 Not Found"); // 設定響應的狀態碼為 404 Not Found
        // 回傳查詢失敗訊息
        $response = array(
            'status' => 'error',
            'message' => 'Data not found'
        );
        echo json_encode($response);
        die();
        }

    //----------------------------------------------------------------------------
        // 建立 SQL 指令
        $sql = "DELETE FROM workoder_detail WHERE count = '$id'";
    
        // 執行 SQL 指令
        if ($conn->query($sql) === TRUE) {
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
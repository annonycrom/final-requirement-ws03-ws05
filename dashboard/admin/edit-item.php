<?php
    require('../../db-connect.php');

    $data = json_decode(file_get_contents('php://input'), true);

    if($data){
        $id = base64_decode(urldecode($data['id']));
        $sql = "UPDATE items SET ITEM_NAME = ?, ITEM_DESCRIPTION = ?, ITEM_PRICE = ? WHERE ITEM_ID =?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $data['name'],$data['desc'],$data['price'],$id);
        $stmt->execute();
    }
?>
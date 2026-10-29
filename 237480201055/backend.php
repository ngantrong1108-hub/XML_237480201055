<?php
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "danhmuc_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Kết nối thất bại"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

if ($action == 'add') {
    $name = $data['name'];
    $phone = $data['phone'];
    $email = $data['email'];
    $group = $data['group'];

    $sql = "INSERT INTO contacts (name, phone, email, group_name) VALUES ('$name', '$phone', '$email', '$group')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Đã lưu vào CSDL thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} 
else if ($action == 'update') {
    $id = $data['id'];
    $name = $data['name'];
    $phone = $data['phone'];
    $email = $data['email'];
    $group = $data['group'];

    $sql = "UPDATE contacts SET name='$name', phone='$phone', email='$email', group_name='$group' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Đã cập nhật CSDL thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} 
else if ($action == 'delete') {
    $id = $data['id'];
    $sql = "DELETE FROM contacts WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Đã xoá khỏi CSDL!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

$conn->close();
?>
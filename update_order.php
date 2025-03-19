<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

$orderId = $data['order_id'];
$orderType = $data['order_type'];
$customizationInstruction = $data['customization_instruction'];
$orderStatus = $data['order_status'];
$customerName = $data['customer_name'];
$cellno = $data['cellno'];

$stmt = $pdo->prepare("UPDATE pos_order SET order_type = :order_type, customization_instruction = :customization_instruction, order_status = :order_status, customer_name = :customer_name, cellno = :cellno WHERE order_id = :order_id");
$stmt->execute([
    'order_type' => $orderType,
    'customization_instruction' => $customizationInstruction,
    'order_status' => $orderStatus,
    'customer_name' => $customerName,
    'cellno' => $cellno,
    'order_id' => $orderId
]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No changes made or order not found']);
}
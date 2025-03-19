<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'];

$stmt = $pdo->prepare("SELECT * FROM pos_order WHERE order_id = :order_id");
$stmt->execute(['order_id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    echo json_encode($order);
} else {
    echo json_encode(['error' => 'Order not found']);
}
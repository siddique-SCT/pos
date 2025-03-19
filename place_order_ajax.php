<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['cart_items'])) {
        // Insert order into the database
        $stmt = $pdo->prepare("INSERT INTO orders (cart_items, customer_name, customer_cellNo, user_id, order_status, order_total) VALUES (:cart_items, :customer_name, :customer_cellNo, :user_id, :order_status, :order_total)");
        $stmt->execute([
            ':cart_items' => $input['cart_items'],
            ':customer_name' => $input['customer_name'],
            ':customer_cellNo' => $input['customer_cellNo'],
            ':user_id' => $input['user_id'],
            ':order_status' => $input['order_status'],
            ':order_total' => $input['order_total']
        ]);

        $order_id = $pdo->lastInsertId();

        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
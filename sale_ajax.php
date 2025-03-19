<?php
require_once 'db_connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get':
        // Fetch orders for DataTable
        $query = "SELECT o.*, u.user_name 
                  FROM pos_order o 
                  LEFT JOIN users u ON o.order_created_by = u.user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $orders]);
        break;

    case 'update_order':
        // Update order details
        $orderId = $_POST['order_id'];
        $orderType = $_POST['order_type'];
        $customizationInstruction = $_POST['customization_instruction'];
        $orderStatus = $_POST['order_status'];
        $customerName = $_POST['customer_name'];
        $cellno = $_POST['cellno'];

        $stmt = $pdo->prepare("UPDATE pos_order 
                               SET order_type = ?, customization_instruction = ?, order_status = ?, customer_name = ?, cellno = ? 
                               WHERE order_id = ?");
        $stmt->execute([$orderType, $customizationInstruction, $orderStatus, $customerName, $cellno, $orderId]);
        echo "Order updated successfully!";
        break;

    case 'delete_order':
        // Delete an order
        $orderId = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM pos_order WHERE order_id = ?");
        $stmt->execute([$orderId]);
        echo "Order deleted successfully!";
        break;

    default:
        echo "Invalid action!";
        break;
}
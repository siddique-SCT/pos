<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

header('Content-Type: application/json');

// Handle GET requests (e.g., for DataTables)
if (isset($_GET['action'])) {
    $columns = [
        0 => 'order_number',
        1 => 'order_total',
        2 => 'user_name',
        3 => 'order_datetime',
        4 => null
    ];

    $limit = $_GET['length'];
    $start = $_GET['start'];
    $order = $columns[$_GET['order'][0]['column']];
    $dir = $_GET['order'][0]['dir'];

    $searchValue = $_GET['search']['value'];

    // Get total records
    $totalRecordsStmt = $pdo->query("SELECT COUNT(*) FROM pos_order");
    $totalRecords = $totalRecordsStmt->fetchColumn();

    // Get total filtered records
    $filterQuery = "SELECT COUNT(*) FROM pos_order INNER JOIN pos_user ON pos_order.order_created_by = pos_user.user_id WHERE 1=1";
    if (!empty($searchValue)) {
        $filterQuery .= " AND (order_number LIKE '%$searchValue%' OR user_name LIKE '%$searchValue%' OR order_total LIKE '%$searchValue%')";
    }
    $totalFilteredRecordsStmt = $pdo->query($filterQuery);
    $totalFilteredRecords = $totalFilteredRecordsStmt->fetchColumn();

    // Fetch data
    $dataQuery = "SELECT * FROM pos_order INNER JOIN pos_user ON pos_order.order_created_by = pos_user.user_id WHERE 1=1";
    if (!empty($searchValue)) {
        $dataQuery .= " AND (order_number LIKE '%$searchValue%' OR user_name LIKE '%$searchValue%' OR order_total LIKE '%$searchValue%')";
    }
    $dataQuery .= " ORDER BY $order $dir LIMIT $start, $limit";
    $dataStmt = $pdo->query($dataQuery);
    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        "draw"              => intval($_GET['draw']),
        "recordsTotal"      => intval($totalRecords),
        "recordsFiltered"   => intval($totalFilteredRecords),
        "data"              => $data
    ];

    echo json_encode($response);
    exit;
}

// Handle POST requests (e.g., for category, product_id, and order creation)
$input = json_decode(file_get_contents('php://input'), true);

// Fetch products by category, search query, and sorting
if (isset($input['category_id']) || isset($input['search']) || isset($input['sort_by'])) {
    $categoryId = $input['category_id'] ?? 0;
    $searchQuery = $input['search'] ?? '';
    $sortBy = $input['sort_by'] ?? 'name'; // Default sorting by name

    // Base query
    $query = "SELECT * FROM pos_product WHERE 1=1";

    // Add category filter
    if ($categoryId > 0) {
        $query .= " AND category_id = :category_id";
    }

    // Add search filter
    if (!empty($searchQuery)) {
        $query .= " AND (product_name LIKE :search OR product_id = :product_id)";
    }

    // Add sorting
    if ($sortBy === 'name') {
        $query .= " ORDER BY product_name ASC"; // Sort by name alphabetically
    } elseif ($sortBy === 'id') {
        $query .= " ORDER BY product_id ASC"; // Sort by ID in ascending order
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);

    if ($categoryId > 0) {
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    }

    if (!empty($searchQuery)) {
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':product_id', $searchQuery, PDO::PARAM_STR);
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
    exit;
}

// Fetch product by ID (for shortcut functionality)
if (isset($input['product_id'])) {
    $productId = $input['product_id'];
    $stmt = $pdo->prepare("SELECT * FROM pos_product WHERE product_id = :product_id AND product_status = 'Available'");
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found or unavailable']);
    }
    exit;
}

// Create a new order
if (isset($input['order_number'])) {
    $stmt = $pdo->prepare("INSERT INTO pos_order (order_number, order_total, order_created_by) VALUES (?, ?, ?)");
    $stmt->execute([
        $input['order_number'],
        $input['order_total'],
        $input['order_created_by']
    ]);

    $order_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO pos_order_item (order_id, product_name, product_qty, product_price) VALUES (?, ?, ?, ?)");
    foreach ($input['items'] as $item) {
        $stmt->execute([
            $order_id,
            $item['product_name'],
            $item['product_qty'],
            $item['product_price']
        ]);
    }

    echo json_encode(['success' => true, 'order_id' => $order_id]);
    exit;
}

// Delete an order
if (isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM pos_order_item WHERE order_id = ?");
    $stmt->execute([$_POST['id']]);

    $stmt = $pdo->prepare("DELETE FROM pos_order WHERE order_id = ?");
    $stmt->execute([$_POST['id']]);

    echo json_encode(['success' => true]);
    exit;
}

// Handle Update Order Status
if (isset($_POST['update_order_status'])) {
    $orderId = $_POST['order_id'];
    $orderStatus = $_POST['order_status'];

    $stmt = $pdo->prepare("UPDATE pos_order SET order_status = :order_status WHERE order_id = :order_id");
    $stmt->execute([
        'order_status' => $orderStatus,
        'order_id' => $orderId
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or order not found']);
    }
    exit;
}

// Default response for invalid requests
echo json_encode(['error' => 'Invalid request']);
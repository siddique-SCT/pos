<?php
declare(strict_types=1);

session_start();

function checkAdminLogin(): void {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
        header('Location: index.php');
        exit;
    } 
}

function redirectIfLoggedIn(): void {
    if(isset($_SESSION['user_logged_in'])){
        header('Location: dashboard.php');
    }
}

function checkAdminOrUserLogin(): void {
    if (!isset($_SESSION['user_logged_in'])) {
        header('Location: index.php');
        exit;
    }
}

function getConfigData(PDO $pdo): array {
    $stmt = $pdo->query('SELECT * FROM pos_configuration');
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

function getCategoryName(PDO $pdo, string $category_id): string {
    $stmt = $pdo->prepare('SELECT category_name FROM pos_category WHERE category_id = ?');
    $stmt->execute([$category_id]);
    return $stmt->fetchColumn() ?: '';
}


?>
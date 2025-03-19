<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

// Ensure the user is logged in as an admin
checkAdminLogin();

$message = '';

// Check if the product ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: product.php");
    exit;
}

$product_id = $_GET['id'];

// Fetch product details from the database
$stmt = $pdo->prepare("SELECT * FROM pos_product WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if the product does not exist
if (!$product) {
    header("Location: product.php");
    exit;
}

// Fetch active categories for the dropdown
$categories = $pdo->query("SELECT category_id, category_name FROM pos_category WHERE category_status = 'Active'")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];

    // Retrieve form data
    $category_id = $_POST['category_id'];
    $product_name = trim($_POST['product_name']);
    $product_type = $_POST['product_type'];
    $product_description = trim($_POST['product_description']);
    $product_image = $_FILES['product_image'];
    $product_price = trim($_POST['product_price']);
    $product_status = $_POST['product_status'];
    $tax_percent = trim($_POST['tax_percent']);
    $discount_percent = trim($_POST['discount_percent']);
    $cost = trim($_POST['cost']);
    $laborcost = trim($_POST['laborcost']);
    $processingcost = trim($_POST['processingcost']);
    $othercost = trim($_POST['othercost']);
    $destPath = $product['product_image']; // Keep existing image if no new image is uploaded

    // Validate required fields
    if (empty($category_id)) {
        $errors[] = 'Category is required.';
    }
    if (empty($product_name)) {
        $errors[] = 'Product Name is required.';
    }
    if (empty($product_price)) {
        $errors[] = 'Product Price is required.';
    }

    // Handle image upload if a new file is provided
    if ($product_image['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileTmpPath = $product_image['tmp_name'];
        $fileName = $product_image['name'];
        $fileType = $product_image['type'];

        // Validate file type
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'uploads/';

            // Create the upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate a unique file name
            $uniqueFileName = uniqid('', true) . '-' . basename($fileName);
            $destPath = $uploadDir . $uniqueFileName;

            // Move the uploaded file to the destination directory
            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                $errors[] = "Failed to move uploaded file.";
            }
        } else {
            $errors[] = "Invalid file type. Only JPG and PNG files are allowed.";
        }
    }

    // If no errors, update the product in the database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE pos_product SET category_id = ?, product_name = ?, product_type = ?, product_description = ?, product_image = ?, product_price = ?, product_status = ?, tax_percent = ?, discount_percent = ?, cost = ?, laborcost = ?, processingcost = ?, othercost = ? WHERE product_id = ?");
            $stmt->execute([$category_id, $product_name, $product_type, $product_description, $destPath, $product_price, $product_status, $tax_percent, $discount_percent, $cost, $laborcost, $processingcost, $othercost, $product_id]);

            // Redirect to the product list page after successful update
            header("Location: product.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        $message = '<ul class="list-unstyled">';
        foreach ($errors as $error) {
            $message .= '<li>' . $error . '</li>';
        }
        $message .= '</ul>';
    }
}

include('header.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        /* Background Styling */
        body {
            background-image: url('asset/img/1.jpg'); /* Replace with actual image */
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Form Container */
        .form-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Form Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        /* Labels & Inputs */
        .form-group label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Shortcut Styling */
        .shortcut {
            font-size: 12px;
            color: #666;
            margin-left: 5px;
            font-weight: normal;
        }

        /* Submit Button */
        .full-width {
            grid-column: 1 / -1;
            text-align: center;
        }

        button {
            background: maroon;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            max-width: 200px;
        }

        button:hover {
            background: darkred;
        }

        /* Product Image Preview */
        .product-image-preview {
            display: block;
            margin-top: 10px;
            max-width: 100px;
            border-radius: 5px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        // JavaScript to handle keyboard shortcuts
        document.addEventListener('keydown', function (event) {
            if (event.altKey) {
                switch (event.key) {
                    case '1':
                        document.getElementById('category_id').focus();
                        break;
                    case '2':
                        document.getElementById('product_name').focus();
                        break;
                    case '3':
                        document.getElementById('product_type').focus();
                        break;
                    case '4':
                        document.getElementById('product_description').focus();
                        break;
                    case '5':
                        document.getElementById('product_price').focus();
                        break;
                    case '6':
                        document.getElementById('tax_percent').focus();
                        break;
                    case '7':
                        document.getElementById('discount_percent').focus();
                        break;
                    case '8':
                        document.getElementById('cost').focus();
                        break;
                    case '9':
                        document.getElementById('laborcost').focus();
                        break;
                    case '0':
                        document.getElementById('processingcost').focus();
                        break;
                    case 'q':
                        document.getElementById('othercost').focus();
                        break;
                    case 'w':
                        document.getElementById('product_status').focus();
                        break;
                    case 'e':
                        document.querySelector('form').submit();
                        break;
                }
            }
        });
    </script>
</head>
<body>
    <h1 class="mt-4">Edit Product</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="product.php">Product Management</a></li>
        <li class="breadcrumb-item active">Edit Product</li>
    </ol>

    <?php
    // Display error messages if any
    if ($message !== '') {
        echo '<div class="alert alert-danger">' . $message . '</div>';
    }
    ?>

    <div class="form-container">
        <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="category_id">Category <span class="shortcut">(Alt+1)</span></label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>" 
                                <?php echo ($category['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                <?php echo $category['category_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_name">Product Name <span class="shortcut">(Alt+2)</span></label>
                    <input type="text" name="product_name" id="product_name" class="form-control" 
                           value="<?php echo $product['product_name']; ?>">
                </div>
                <div class="form-group">
                    <label for="product_type">Product Type <span class="shortcut">(Alt+3)</span></label>
                    <select name="product_type" id="product_type" class="form-control">
                        <option value="Regular" <?php echo ($product['product_type'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                        <option value="Deal" <?php echo ($product['product_type'] == 'Deal') ? 'selected' : ''; ?>>Deal</option>
                        <option value="Customized" <?php echo ($product['product_type'] == 'Customized') ? 'selected' : ''; ?>>Customized</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_description">Product Description <span class="shortcut">(Alt+4)</span></label>
                    <textarea name="product_description" id="product_description" class="form-control"><?php echo $product['product_description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="product_price">Product Price (PKR) <span class="shortcut">(Alt+5)</span></label>
                    <input type="number" name="product_price" id="product_price" class="form-control" step="0.01" 
                           value="<?php echo $product['product_price']; ?>">
                </div>
                <div class="form-group">
                    <label for="tax_percent">Tax Percentage <span class="shortcut">(Alt+6)</span></label>
                    <input type="number" name="tax_percent" id="tax_percent" class="form-control" step="0.01" 
                           value="<?php echo $product['tax_percent']; ?>">
                </div>
                <div class="form-group">
                    <label for="discount_percent">Discount Percentage <span class="shortcut">(Alt+7)</span></label>
                    <input type="number" name="discount_percent" id="discount_percent" class="form-control" step="0.01" 
                           value="<?php echo $product['discount_percent']; ?>">
                </div>
                <div class="form-group">
                    <label for="cost">Cost (PKR) <span class="shortcut">(Alt+8)</span></label>
                    <input type="number" name="cost" id="cost" class="form-control" step="0.01" 
                           value="<?php echo $product['cost']; ?>">
                </div>
                <div class="form-group">
                    <label for="laborcost">Labor Cost (PKR) <span class="shortcut">(Alt+9)</span></label>
                    <input type="number" name="laborcost" id="laborcost" class="form-control" step="0.01" 
                           value="<?php echo $product['laborcost']; ?>">
                </div>
                <div class="form-group">
                    <label for="processingcost">Processing Cost (PKR) <span class="shortcut">(Alt+0)</span></label>
                    <input type="number" name="processingcost" id="processingcost" class="form-control" step="0.01" 
                           value="<?php echo $product['processingcost']; ?>">
                </div>
                <div class="form-group">
                    <label for="othercost">Other Cost (PKR) <span class="shortcut">(Alt+Q)</span></label>
                    <input type="number" name="othercost" id="othercost" class="form-control" step="0.01" 
                           value="<?php echo $product['othercost']; ?>">
                </div>
                <div class="form-group">
                    <label for="product_status">Status <span class="shortcut">(Alt+W)</span></label>
                    <select name="product_status" id="product_status" class="form-control">
                        <option value="Available" <?php echo ($product['product_status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                        <option value="Out of Stock" <?php echo ($product['product_status'] == 'Out of Stock') ? 'selected' : ''; ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_image">Image</label>
                    <input type="file" name="product_image" accept="image/*" />
                    <?php if ($product['product_image']): ?>
                        <img src="<?php echo $product['product_image']; ?>" alt="Product Image" class="product-image-preview">
                    <?php endif; ?>
                </div>
                <div class="full-width">
                    <button type="submit">Update Product <span>(Alt+E)</span></button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

<?php
include('footer.php');
?>
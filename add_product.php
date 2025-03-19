<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

// Fetch category for the dropdown
$categorys = $pdo->query("SELECT category_id, category_name FROM pos_category WHERE category_status = 'Active'")->fetchAll(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];

    $category_id = $_POST['category_id'];
    $product_name = trim($_POST['product_name']);
    $product_type = $_POST['product_type'];  // New field
    $product_description = trim($_POST['product_description']);  // New field
    $product_image = $_FILES['product_image'];
    $product_price = trim($_POST['product_price']);
    $product_status = $_POST['product_status'];
    $tax_percent = trim($_POST['tax_percent']);
    $discount_percent = trim($_POST['discount_percent']);
    $cost = trim($_POST['cost']);
    $laborcost = trim($_POST['laborcost']);
    $processingcost = trim($_POST['processingcost']);
    $othercost = trim($_POST['othercost']);
    $destPath = '';

    // Validate fields
    if (empty($category_id)) {
        $errors[] = 'Category is required.';
    }
    if (empty($product_name)) {
        $errors[] = 'Product Name is required.';
    }
    if (empty($product_price)) {
        $errors[] = 'Product Price is required.';
    }

    // Check if Product already exists for another user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pos_product WHERE product_name = :product_name");
    $stmt->execute(['product_name' => $product_name]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errors[] = 'Product already exists.';
    } else {
        // Handle image upload
        if ($product_image['error'] === UPLOAD_ERR_OK) {

            // Define the allowed file types
            $allowedTypes = ['image/jpeg', 'image/png'];

            // Get the uploaded file information
            $fileTmpPath = $product_image['tmp_name'];
            $fileName = $product_image['name'];
            $fileSize = $product_image['size'];
            $fileType = $product_image['type'];

            // Validate the file type
            if (in_array($fileType, $allowedTypes)) {
                // Define the upload directory
                $uploadDir = 'uploads/';

                // Create the upload directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate a unique file name to avoid overwriting
                $uniqueFileName = uniqid('', true) . '-' . basename($fileName);

                // Define the destination path
                $destPath = $uploadDir . $uniqueFileName;

                // Move the uploaded file to the destination directory
                if (!move_uploaded_file($fileTmpPath, $destPath)) {
                    $errors[] = "Failed to move uploaded file.";
                }
            } else {
                $errors[] = "Invalid file type. Only JPG and PNG files are allowed.";
            }
        }
    }

    if (empty($errors)) {
        try {
            // Insert the product into the database
            $stmt = $pdo->prepare("INSERT INTO pos_product (category_id, product_name, product_type, product_description, product_image, product_price, product_status, tax_percent, discount_percent, cost, laborcost, processingcost, othercost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category_id, $product_name, $product_type, $product_description, $destPath, $product_price, $product_status, $tax_percent, $discount_percent, $cost, $laborcost, $processingcost, $othercost]);

            header("Location: product.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

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
    <title>Add Product</title>
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
            max-width: 90%;
            margin: 10px auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 5px;
            border-radius: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Form Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
        }

        /* Labels & Inputs */
        .form-group label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-bottom: 3px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Submit Button */
        .full-width {
            grid-column: span 2;
            text-align: center;
        }

        button {
            background: maroon;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
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
            margin-top: 8px;
            max-width: 80px;
            border-radius: 5px;
        }

        /* Responsive Adjustments */
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .full-width {
                grid-column: span 1;
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
    <h1 class="mt-4">Add Product</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="product.php">Product Management</a></li>
        <li class="breadcrumb-item active">Add Product</li>
    </ol>

    <?php
    if ($message !== '') {
        echo '<div class="alert alert-danger">' . $message . '</div>';
    }
    ?>

    <div class="form-container">
        <form method="POST" action="add_product.php" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="category_id">Category <span class="shortcut">(Alt+1)</span></label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php foreach ($categorys as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_name">Product Name <span class="shortcut">(Alt+2)</span></label>
                    <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Product (Alt+2)">
                </div>
                <div class="form-group">
                    <label for="product_type">Product Type <span class="shortcut">(Alt+3)</span></label>
                    <select name="product_type" id="product_type" class="form-control">
                        <option value="Regular">Regular</option>
                        <option value="Deal">Deal</option>
                        <option value="Customized">Customized</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="product_price">Product Price (PKR) <span class="shortcut">(Alt+5)</span></label>
                    <input type="number" name="product_price" id="product_price" class="form-control" step="0.01" placeholder="Price (Alt+5)">
                </div>
                <div class="form-group">
                    <label for="tax_percent">Tax Percentage <span class="shortcut">(Alt+6)</span></label>
                    <input type="number" name="tax_percent" id="tax_percent" class="form-control" step="0.01" placeholder="Tax (Alt+6)">
                </div>
                <div class="form-group">
                    <label for="discount_percent">Discount Percentage <span class="shortcut">(Alt+7)</span></label>
                    <input type="number" name="discount_percent" id="discount_percent" class="form-control" step="0.01" placeholder="Discount (Alt+7)">
                </div>
                <div class="form-group">
                    <label for="cost">Cost (PKR) <span class="shortcut">(Alt+8)</span></label>
                    <input type="number" name="cost" id="cost" class="form-control" step="0.01" placeholder="Cost (Alt+8)">
                </div>
                <div class="form-group">
                    <label for="laborcost">Labor Cost (PKR) <span class="shortcut">(Alt+9)</span></label>
                    <input type="number" name="laborcost" id="laborcost" class="form-control" step="0.01" placeholder="LaborCost (Alt+9)">
                </div>
                <div class="form-group">
                    <label for="processingcost">Processing Cost (PKR) <span class="shortcut">(Alt+10)</span></label>
                    <input type="number" name="processingcost" id="processingcost" class="form-control" step="0.01" placeholder="Processing Cost (Alt+10)">
                </div>
                <div class="form-group">
                    <label for="othercost">Other Cost (PKR) <span class="shortcut">(Alt+11)</span></label>
                    <input type="number" name="othercost" id="othercost" class="form-control" step="0.01" placeholder="Other Cost (Alt+11)">
                </div>
                <div class="form-group">
                    <label for="product_status">Status <span class="shortcut">(Alt+12)</span></label>
                    <select name="product_status" id="product_status" class="form-control">
                        <option value="Available">Available</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>
                
                
                <div class="form-group">
                    <label for="product_description">Product Description <span class="shortcut">(Alt+4)</span></label>
                    <textarea name="product_description" id="product_description" class="form-control" placeholder="Description (Alt+4)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="product_image">Image</label>
                    <input type="file" name="product_image" accept="image/*" />
                </div>
                
                <div class="full-width">
                    <button type="submit">Add Product <span class="shortcut">(Alt+E)</span></button>
                </div>
                
                
                
            </div>
        </form>
    </div>

</body>
</html>

<?php
include('footer.php');
?>

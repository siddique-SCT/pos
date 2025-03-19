<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminOrUserLogin();

// Fetch categories for the dropdown
$categorys = $pdo->query("SELECT category_id, category_name FROM pos_category WHERE category_status = 'Active'")->fetchAll(PDO::FETCH_ASSOC);

// Function to generate unique shortcuts for categories
function generateShortcuts($categories) {
    $shortcuts = [];
    $usedChars = []; // Track used characters

    foreach ($categories as &$category) {
        $name = strtolower($category['category_name']);
        $shortcut = '';

        // Try to assign the first unique letter
        for ($i = 0; $i < strlen($name); $i++) {
            $char = $name[$i];
            if (!in_array($char, $usedChars)) {
                $shortcut = $char;
                $usedChars[] = $char;
                break;
            }
        }

        // If no unique letter is found, fall back to a numeric shortcut
        if (empty($shortcut)) {
            $shortcut = count($usedChars) + 1; // Use a number as a fallback
        }

        $category['shortcut'] = $shortcut;
    }

    return $categories;
}

// Generate shortcuts for categories
$categorys = generateShortcuts($categorys);

$confData = getConfigData($pdo);

include('header.php');
?>

<h1 class="mt-4">Order</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Order</li>
</ol>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><b>Item</b></div>
            <div class="card-body">
                <div class="mb-3">
                    <!-- Search Input -->
                    <input type="text" id="search_product" placeholder="Search product by name (Alt + 0)" class="form-control mb-2" oninput="searchProduct()">
                    <!-- Shortcut Input -->
                    <input type="text" id="product_id_input" placeholder="Enter Product ID and press Enter (Control + 0)" class="form-control mb-2 shortcut-input" onkeypress="handleProductIdInput(event)">
                    <button type="button" class="btn btn-primary mb-2" onclick="load_category_product()">All (Alt + A)</button>
                    <?php foreach ($categorys as $category): ?>
                        <button type="button" class="btn btn-primary mb-2" onclick="load_category_product('<?php echo $category['category_id']; ?>')"><?php echo $category['category_name']; ?> (Alt + <?php echo strtoupper($category['shortcut']); ?>)</button>&nbsp;&nbsp;
                    <?php endforeach; ?>
                </div>
                <div class="row" id="dynamic_item">
                    <!-- Dynamic items will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><b>Order</b></div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success btn-sm float-end" onclick="resetOrder()">New Order (Alt + N)</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="order_item_details">
                            <!-- Order items will be loaded here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><b>Gross Total</b></td>
                                <td class="text-right"><b id="order_gross_total">0.00</b></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3"><b>Discount</b></td>
                                <td class="text-right"><b id="order_discount">0.00</b></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3"><b>Taxes</b></td>
                                <td class="text-right"><b id="order_taxes">0.00</b></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3"><b>Net Total</b></td>
                                <td class="text-right"><b id="order_net_total">0.00</b></td>
                                <td>&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <input type="button" class="btn btn-success" id="order_btn" value="Pay (Alt + X)" onclick="createOrder()" disabled />
                <!-- Display Shortcuts -->
                <div class="mt-2 text-muted">
                    <small>
                        <b>Shortcuts:</b><br>
                        <b>Alt + 0</b> (Search Product), <b>Control + 0</b> (Enter Product ID)<br>
                        <b>Alt + A</b> (All), <b>Alt + N</b> (New Order), <b>Alt + X</b> (Pay)<br>
                        <?php foreach ($categorys as $category): ?>
                            <b>Alt + <?php echo strtoupper($category['shortcut']); ?></b> (<?php echo $category['category_name']; ?>)<br>
                        <?php endforeach; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style for shortcut input field */
    .shortcut-input {
        font-weight: bold;
        border: 2px solid #007bff; /* Blue border */
    }
    .shortcut-input:focus {
        border-color: #0056b3; /* Darker blue on focus */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Glow effect */
    }
</style>

<script>
let cart = [];
let total = 0;
let cur = "<?php echo $confData['currency']; ?>";
let taxPer = parseFloat("<?php echo $confData['tax_rate']; ?>");

// Load products when the page loads
load_category_product();

// Function to handle keyboard shortcuts
document.addEventListener('keydown', function(event) {
    // Check if Alt + 0 is pressed (Search Product)
    if (event.altKey && event.key.toLowerCase() === '0') {
        event.preventDefault(); // Prevent default behavior
        document.getElementById('search_product').focus(); // Focus on the search input
    }

    // Check if Control + 0 is pressed (Enter Product ID)
    if (event.ctrlKey && event.key.toLowerCase() === '0') {
        event.preventDefault(); // Prevent default behavior
        document.getElementById('product_id_input').focus(); // Focus on the product ID input
    }

    // Check if Alt + A is pressed (All)
    if (event.altKey && event.key.toLowerCase() === 'a') {
        event.preventDefault(); // Prevent default behavior
        load_category_product(); // Load all products
    }

    // Check if Alt + N is pressed (New Order)
    if (event.altKey && event.key.toLowerCase() === 'n') {
        event.preventDefault(); // Prevent default behavior
        resetOrder(); // Reset the order
    }

    // Check if Alt + X is pressed (Pay)
    if (event.altKey && event.key.toLowerCase() === 'x') {
        event.preventDefault(); // Prevent default behavior
        createOrder(); // Trigger the Pay functionality
    }

    // Check for category shortcuts (Alt + dynamically assigned shortcut)
    <?php foreach ($categorys as $category): ?>
        if (event.altKey && event.key.toLowerCase() === '<?php echo strtolower($category['shortcut']); ?>') {
            event.preventDefault(); // Prevent default behavior
            load_category_product('<?php echo $category['category_id']; ?>'); // Load products for the category
        }
    <?php endforeach; ?>
});

// Function to load products based on category and search query
function load_category_product(category_id = 0, searchQuery = '') {
    fetch('place_order_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ category_id: category_id, search: searchQuery })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error:', data.error);
        } else {
            let html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    let product_status = (data[i].product_status === 'Available') ? `<span class="badge bg-success">${data[i].product_status}</span>` : `<span class="badge bg-danger">${data[i].product_status}</span>`;
                    let extraCode = (data[i].product_status === 'Available') ? `onclick="addToCart('${data[i].product_id}', '${data[i].product_name}', ${data[i].product_price}, ${data[i].tax_percent}, ${data[i].discount_percent})" style="cursor:pointer"` : '';
                    html += `
                    <div class="col-md-2 text-center mb-3" ${extraCode}>
                        <img src="${data[i].product_image}" class="img-thumbnail img-fluid mb-2">
                        <br />
                        <span id="product_name_${data[i].product_id}">${data[i].product_name}</span><br />
                        <span>ID: ${data[i].product_id}</span><br />
                        ${product_status}
                    </div>
                    `;
                }
            } else {
                html = '<p class="text-center">No Item Found</p>';
            }
            document.getElementById('dynamic_item').innerHTML = html;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}

// Function to handle product ID input (shortcut)
function handleProductIdInput(event) {
    if (event.key === 'Enter') {
        const productId = document.getElementById('product_id_input').value.trim();
        if (productId) {
            fetch('place_order_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error); // Show error if product not found
                } else {
                    // Add the product to the cart
                    addToCart(data.product_id, data.product_name, data.product_price, data.tax_percent, data.discount_percent);
                    document.getElementById('product_id_input').value = ''; // Clear the input field
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }
    }
}

// Function to search products by name
function searchProduct() {
    const searchQuery = document.getElementById('search_product').value.trim();
    load_category_product(0, searchQuery); // Load all products with the search query
}

// Function to add item to cart
function addToCart(productId, itemName, itemPrice, taxPercent, discountPercent) {
    const item = cart.find(cartItem => cartItem.productId === productId);

    if (item) {
        item.quantity += 1; // Increment quantity if item already exists in cart
    } else {
        // Add new item to cart
        cart.push({
            productId: productId,
            name: itemName,
            price: itemPrice,
            quantity: 1,
            taxPercent: taxPercent,
            discountPercent: discountPercent
        });
    }
    updateCart(); // Refresh the cart display
}

// Function to update the cart display
function updateCart() {
    const cartItems = document.getElementById('order_item_details');
    cartItems.innerHTML = '';
    let cardHtml = '';
    let grossTotal = 0;
    let totalDiscount = 0;
    let totalTax = 0;

    cart.forEach(cartItem => {
        let itemTotal = cartItem.price * cartItem.quantity;
        let itemDiscount = itemTotal * (cartItem.discountPercent / 100);
        let itemTax = (itemTotal - itemDiscount) * (cartItem.taxPercent / 100);
        grossTotal += itemTotal;
        totalDiscount += itemDiscount;
        totalTax += itemTax;

        cardHtml += `
        <tr>
            <td width="40%">${cartItem.name}</td>
            <td width="15%"><input type="number" name="product_qty[]" min="1" value="${cartItem.quantity}" oninput="changeQuantity('${cartItem.productId}', this.value)" style="width:50px;"></td>
            <td width="15%">${cur}${parseFloat(cartItem.price).toFixed(2)}</td>
            <td width="20%">${cur}${parseFloat(cartItem.price * cartItem.quantity).toFixed(2)}</td>
            <td width="10%"><button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart('${cartItem.productId}')">x</button></td>
        </tr>
        `;
    });

    let netTotal = grossTotal - totalDiscount + totalTax;

    document.getElementById('order_gross_total').innerText = cur + parseFloat(grossTotal).toFixed(2);
    document.getElementById('order_discount').innerText = cur + parseFloat(totalDiscount).toFixed(2);
    document.getElementById('order_taxes').innerText = cur + parseFloat(totalTax).toFixed(2);
    document.getElementById('order_net_total').innerText = cur + parseFloat(netTotal).toFixed(2);

    cartItems.innerHTML = cardHtml;

    // Enable or disable the Create Order button based on cart contents
    const createOrderBtn = document.getElementById('order_btn');
    createOrderBtn.disabled = cart.length === 0;
}

// Function to change item quantity
function changeQuantity(productId, newQuantity) {
    const item = cart.find(cartItem => cartItem.productId === productId);
    if (item) {
        item.quantity = parseInt(newQuantity, 10);
        if (item.quantity < 1) {
            removeFromCart(productId);
        } else {
            updateCart();
        }
    }
}

// Function to remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(cartItem => cartItem.productId !== productId);
    updateCart();
}

// Function to reset the order
function resetOrder() {
    cart = [];
    updateCart();
}

// Function to create the order
function createOrder() {
    if (cart.length === 0) {
        alert('Cart is empty. Add items to the cart before paying.');
        return;
    }

    const customerName = prompt("Enter customer name:");
    const customerCellNo = prompt("Enter customer cell number:");

    if (!customerName || !customerCellNo) {
        alert("Customer name and cell number are required.");
        return;
    }

    const orderData = {
        cart_items: JSON.stringify(cart),
        customer_name: customerName,
        customer_cellNo: customerCellNo,
        user_id: <?php echo $_SESSION['user_id']; ?>,
        order_status: 'Fresh',
        order_total: parseFloat(document.getElementById('order_net_total').innerText.replace(cur, ''))
    };

    fetch('place_order_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order created successfully! Order ID: ' + data.order_id);
            resetOrder();
        } else {
            alert('Order creation failed: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php
include('footer.php');
?>
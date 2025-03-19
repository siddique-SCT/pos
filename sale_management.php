<?php
require_once 'db_connect.php';
require_once 'auth_function.php';

// Check if the user is logged in as admin or user
checkAdminOrUserLogin();

// Fetch configuration data (e.g., currency)
$confData = getConfigData($pdo);

// Include the header
include('header.php');
?>

<h1 class="mt-4">Sale Management</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Sale Management</li>
</ol>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col col-md-6"><b>Order List</b></div>
            <div class="col col-md-6">
                <a href="add_order.php" class="btn btn-success btn-sm float-end">Add</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="orderTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Order Total</th>
                    <th>Created By</th>
                    <th>Date Time</th>
                    <th>Order Type</th>
                    <th>Customization Instruction</th>
                    <th>Order Status</th>
                    <th>Customer Name</th>
                    <th>Cell No</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<?php
// Include the footer
include('footer.php');
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#orderTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "sale_ajax.php?action=get",
            "type": "GET"
        },
        "columns": [
            { "data": "order_number" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `<?php echo $confData['currency']; ?>${row.order_total}`;
                }
            },
            { "data": "user_name" },
            { "data": "order_datetime" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                    <select class="form-control form-control-sm order-type" data-id="${row.order_id}">
                        <option value="ReadyMade" ${row.order_type === 'ReadyMade' ? 'selected' : ''}>ReadyMade</option>
                        <option value="Customized" ${row.order_type === 'Customized' ? 'selected' : ''}>Customized</option>
                        <option value="Hybrid" ${row.order_type === 'Hybrid' ? 'selected' : ''}>Hybrid</option>
                    </select>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `<textarea class="form-control form-control-sm customization-instruction" data-id="${row.order_id}">${row.customization_instruction || ''}</textarea>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                    <select class="form-control form-control-sm order-status" data-id="${row.order_id}">
                        <option value="Fresh" ${row.order_status === 'Fresh' ? 'selected' : ''}>Fresh</option>
                        <option value="In-Process" ${row.order_status === 'In-Process' ? 'selected' : ''}>In-Process</option>
                        <option value="Completed" ${row.order_status === 'Completed' ? 'selected' : ''}>Completed</option>
                        <option value="Cancelled" ${row.order_status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `<input type="text" class="form-control form-control-sm customer-name" data-id="${row.order_id}" value="${row.customer_name || ''}">`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `<input type="text" class="form-control form-control-sm cellno" data-id="${row.order_id}" value="${row.cellno || ''}">`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                    <div class="text-center">
                        <a href="print_order.php?id=${row.order_id}" class="btn btn-warning btn-sm" target="_blank">PDF</a>
                        <button type="button" class="btn btn-primary btn-sm btn-save-update" data-id="${row.order_id}">Save</button>
                        <button type="button" class="btn btn-danger btn-sm btn_delete" data-id="${row.order_id}">X</button>
                    </div>`;
                }
            }
        ]
    });

    // Handle Save Button Click
    $(document).on('click', '.btn-save-update', function() {
        let orderId = $(this).data('id');
        let orderType = $(`.order-type[data-id="${orderId}"]`).val();
        let customizationInstruction = $(`.customization-instruction[data-id="${orderId}"]`).val();
        let orderStatus = $(`.order-status[data-id="${orderId}"]`).val();
        let customerName = $(`.customer-name[data-id="${orderId}"]`).val();
        let cellno = $(`.cellno[data-id="${orderId}"]`).val();

        $.ajax({
            url: 'sale_ajax.php',
            method: 'POST',
            data: {
                action: 'update_order',
                order_id: orderId,
                order_type: orderType,
                customization_instruction: customizationInstruction,
                order_status: orderStatus,
                customer_name: customerName,
                cellno: cellno
            },
            success: function(response) {
                alert('Order updated successfully!');
                $('#orderTable').DataTable().ajax.reload();
            }
        });
    });

    // Handle Delete Button Click
    $(document).on('click', '.btn_delete', function() {
        if (confirm("Are you sure you want to remove this Order?")) {
            let id = $(this).data('id');
            $.ajax({
                url: 'sale_ajax.php',
                method: 'POST',
                data: { id: id, action: 'delete_order' },
                success: function(data) {
                    $('#orderTable').DataTable().ajax.reload();
                }
            });
        }
    });
});
</script>
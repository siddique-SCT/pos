<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminOrUserLogin();

$confData = getConfigData($pdo);

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
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal for Updating Order Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="order_id" name="order_id">
                    <div class="mb-3">
                        <label for="order_status" class="form-label">Order Status</label>
                        <select class="form-control" id="order_status" name="order_status">
                            <option value="Fresh">Fresh</option>
                            <option value="In-Process">In-Process</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#orderTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "order_ajax.php?action=get",
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
                    return `<span class="badge bg-${getStatusBadgeColor(row.order_status)}">${row.order_status}</span>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                    <div class="text-center">
                        <a href="print_order.php?id=${row.order_id}" class="btn btn-warning btn-sm" target="_blank">PDF</a>
                        <button type="button" class="btn btn-info btn-sm btn_update_status" data-id="${row.order_id}">Update Status</button>
                        <button type="button" class="btn btn-danger btn-sm btn_delete" data-id="${row.order_id}">X</button>
                    </div>`;
                }
            }
        ]
    });

    // Function to get badge color based on order status
    function getStatusBadgeColor(status) {
        switch (status) {
            case 'Fresh':
                return 'primary';
            case 'In-Process':
                return 'warning';
            case 'Completed':
                return 'success';
            case 'Cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    // Handle Delete Order
    $(document).on('click', '.btn_delete', function() {
        if (confirm("Are you sure you want to remove this Order?")) {
            let id = $(this).data('id');
            $.ajax({
                url: 'order_ajax.php',
                method: 'POST',
                data: { id: id },
                success: function(data) {
                    $('#orderTable').DataTable().ajax.reload();
                }
            });
        }
    });

    // Handle Update Status Button Click
    $(document).on('click', '.btn_update_status', function() {
        let orderId = $(this).data('id');
        $('#order_id').val(orderId); // Set order ID in the hidden input
        $('#updateStatusModal').modal('show'); // Show the modal
    });

    // Handle Update Status Form Submission
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        let orderId = $('#order_id').val();
        let orderStatus = $('#order_status').val();

        $.ajax({
            url: 'order_ajax.php',
            method: 'POST',
            data: {
                update_order_status: true,
                order_id: orderId,
                order_status: orderStatus
            },
            success: function(data) {
                $('#updateStatusModal').modal('hide');
                $('#orderTable').DataTable().ajax.reload();
            }
        });
    });
});
</script>
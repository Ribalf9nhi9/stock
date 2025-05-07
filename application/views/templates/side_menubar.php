<aside style="background-color:;" class="main-sidebar">
<style>
        /* Sidebar menu items */
        .sidebar-menu li a {
            display: block;
            padding: 10px 15px;
            border-radius: 10px; /* Add border radius */
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease; /* Smooth hover effect */
        }

        /* Hover effect */
        .sidebar-menu li a:hover {
            background-color: #007bff; /* Change background color on hover */
            color: #fff; /* Change text color on hover */
        }
    
        /* Active menu item */
     

        /* Sidebar icons */
        .sidebar-menu li a i {
            margin-right: 10px; /* Add spacing between icon and text */
        }

        /* Style for the low stock alert items */
        .low-stock-alert-item {
            padding: 5px 10px;
            font-size: 0.9em;
            border-bottom: 1px solid #eee;
        }
        .low-stock-alert-item:last-child {
            border-bottom: none;
        }
        .low-stock-alert-item .product-name {
            font-weight: bold;
        }
        .low-stock-alert-item .qty-details {
            font-size: 0.9em;
            color: #555;
        }
        .low-stock-alert-item input[type=\"number\"] {
            width: 60px;
            margin-right: 5px;
            padding: 2px 5px;
        }
        .low-stock-alert-item .btn-confirm-add {
            padding: 2px 8px;
            font-size: 0.9em;
        }
        #lowStockAlertsListContainer .no-alerts {
            padding: 10px;
            text-align: center;
            color: #777;
        }

    </style>
    <!-- sidebar: style can be found in sidebar.less -->
    <section style="background-color:#1290c1;"class="sidebar">
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        
        <li  id="dashboardMainMenu">
          <a href="<?php echo base_url("dashboard") ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>

        <?php // === START: Low Stock Alerts Menu Item === ?>
        <?php // Updated to check for specific viewSidebarStockAlerts permission
        if(isset($user_permission) && is_array($user_permission) && in_array("viewSidebarStockAlerts", $user_permission)): ?>
            <li class="treeview" id="mainLowStockAlertsNav">
                <a href="#">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <span>Stock Alerts</span>
                    <span class="pull-right-container">
                        <small class="label pull-right bg-yellow" id="lowStockAlertsCount"></small>
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" id="lowStockAlertsListContainer">
                    <!-- Alerts will be loaded here by JavaScript -->
                    <li class="no-alerts">Loading alerts...</li>
                </ul>
            </li>
        <?php endif; ?>
        <?php // === END: Low Stock Alerts Menu Item === ?>


        <?php if($user_permission): ?>
          <?php if(in_array("createUser", $user_permission) || in_array("updateUser", $user_permission) || in_array("viewUser", $user_permission) || in_array("deleteUser", $user_permission)): ?>
            <li class="treeview" id="mainUserNav">
            <a href="#">
              <i class="fa fa-users"></i>
              <span>Users</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if(in_array("createUser", $user_permission)): ?>
              <li id="createUserNav"><a href="<?php echo base_url("users/create") ?>"><i class="fa fa-circle-o"></i> Add User</a></li>
              <?php endif; ?>

              <?php if(in_array("updateUser", $user_permission) || in_array("viewUser", $user_permission) || in_array("deleteUser", $user_permission)): ?>
              <li id="manageUserNav"><a href="<?php echo base_url("users") ?>"><i class="fa fa-circle-o"></i> Manage Users</a></li>
            <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>

          <?php if(in_array("createGroup", $user_permission) || in_array("updateGroup", $user_permission) || in_array("viewGroup", $user_permission) || in_array("deleteGroup", $user_permission)): ?>
            <li class="treeview" id="mainGroupNav">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span>Groups</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <?php if(in_array("createGroup", $user_permission)): ?>
                  <li id="addGroupNav"><a href="<?php echo base_url("groups/create") ?>"><i class="fa fa-circle-o"></i> Add Group</a></li>
                <?php endif; ?>
                <?php if(in_array("updateGroup", $user_permission) || in_array("viewGroup", $user_permission) || in_array("deleteGroup", $user_permission)): ?>
                <li id="manageGroupNav"><a href="<?php echo base_url("groups") ?>"><i class="fa fa-circle-o"></i> Manage Groups</a></li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>


          <?php if(in_array("createBrand", $user_permission) || in_array("updateBrand", $user_permission) || in_array("viewBrand", $user_permission) || in_array("deleteBrand", $user_permission)): ?>
            <li id="brandNav">
              <a href="<?php echo base_url("brands/") ?>">
                <i class="glyphicon glyphicon-tags"></i> <span>Brands</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if(in_array("createCategory", $user_permission) || in_array("updateCategory", $user_permission) || in_array("viewCategory", $user_permission) || in_array("deleteCategory", $user_permission)): ?>
            <li id="categoryNav">
              <a href="<?php echo base_url("category/") ?>">
                <i class="fa fa-files-o"></i> <span>Category</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if(in_array("createStore", $user_permission) || in_array("updateStore", $user_permission) || in_array("viewStore", $user_permission) || in_array("deleteStore", $user_permission)): ?>
            <li id="storeNav">
              <a href="<?php echo base_url("stores/") ?>">
                <i class="fa fa-files-o"></i> <span >Stores</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if(in_array("createAttribute", $user_permission) || in_array("updateAttribute", $user_permission) || in_array("viewAttribute", $user_permission) || in_array("deleteAttribute", $user_permission)): ?>
          <li id="attributeNav">
            <a href="<?php echo base_url("attributes/") ?>">
              <i class="fa fa-files-o"></i> <span>Attributes</span>
            </a>
          </li>
          <?php endif; ?>

          <?php if(in_array("createProduct", $user_permission) || in_array("updateProduct", $user_permission) || in_array("viewProduct", $user_permission) || in_array("deleteProduct", $user_permission)): ?>
            <li class="treeview" id="mainProductNav">
              <a href="#">
                <i class="fa fa-cube"></i>
                <span>Products</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <?php if(in_array("createProduct", $user_permission)): ?>
                  <li id="addProductNav"><a href="<?php echo base_url("products/create") ?>"><i class="fa fa-circle-o"></i> Add Product</a></li>
                <?php endif; ?>
                <?php if(in_array("updateProduct", $user_permission) || in_array("viewProduct", $user_permission) || in_array("deleteProduct", $user_permission)): ?>
                <li id="manageProductNav"><a href="<?php echo base_url("products") ?>"><i class="fa fa-circle-o"></i> Manage Products</a></li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>


          <?php if(in_array("createOrder", $user_permission) || in_array("updateOrder", $user_permission) || in_array("viewOrder", $user_permission) || in_array("deleteOrder", $user_permission)): ?>
            <li class="treeview" id="mainOrdersNav">
              <a href="#">
                <i class="fa fa-dollar"></i>
                <span>Orders</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <?php if(in_array("createOrder", $user_permission)): ?>
                  <li id="addOrderNav"><a href="<?php echo base_url("orders/create") ?>"><i class="fa fa-circle-o"></i> Add Order</a></li>
                <?php endif; ?>
                <?php if(in_array("updateOrder", $user_permission) || in_array("viewOrder", $user_permission) || in_array("deleteOrder", $user_permission)): ?>
                <li id="manageOrdersNav"><a href="<?php echo base_url("orders") ?>"><i class="fa fa-circle-o"></i> Manage Orders</a></li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>

          <?php // === START: Order Requests Menu Item === ?>
          <?php if(in_array("viewOrderRequest", $user_permission)): ?>
            <li id="navOrderRequests">
              <a href="<?php echo base_url("order_requests") ?>">
                <i class="fa fa-list-alt"></i> <span>Order Requests</span>
              </a>
            </li>
          <?php endif; ?>
          <?php // === END: Order Requests Menu Item === ?>

          <?php if(in_array("viewReports", $user_permission)): ?>
            <li id="reportNav">
              <a href="<?php echo base_url("reports/") ?>">
                <i class="glyphicon glyphicon-stats"></i> <span>Reports</span>
              </a>
            </li>
          <?php endif; ?>


          <?php if(in_array("updateCompany", $user_permission)): ?>
            <li id="companyNav"><a href="<?php echo base_url("company/") ?>"><i class="fa fa-files-o"></i> <span>Company</span></a></li>
          <?php endif; ?>

        

        <!-- <li class="header">Settings</li> -->

        <?php if(in_array("viewProfile", $user_permission)): ?>
          <li><a href="<?php echo base_url("users/profile/") ?>"><i class="fa fa-user-o"></i> <span>Profile</span></a></li>
        <?php endif; ?>
        <?php if(in_array("updateSetting", $user_permission)): ?>
          <li><a href="<?php echo base_url("users/setting/") ?>"><i class="fa fa-wrench"></i> <span>Setting</span></a></li>
        <?php endif; ?>

        <?php endif; ?>
        <!-- user permission info -->
        <li><a href="<?php echo base_url("auth/logout") ?>"><i class="glyphicon glyphicon-log-out"></i> <span>Logout</span></a></li>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

<script type="text/javascript">
$(document).ready(function() {
    // Function to load low stock alerts
    function loadLowStockAlerts() {
        $.ajax({
            url: "<?php echo base_url("alerts/fetch_active_low_stock_alerts"); ?>",
            type: "GET",
            dataType: "json",
            success: function(response) {
                var alertsList = $("#lowStockAlertsListContainer");
                alertsList.empty(); // Clear previous alerts
                if (response.success && response.data && response.data.length > 0) {
                    $("#lowStockAlertsCount").text(response.data.length);
                    $.each(response.data, function(index, alert) {
                        // Corrected ID generation for the input field
                        var inputId = "add_qty_" + alert.id;
                        var alertItem = 
                            $("<li class=\"low-stock-alert-item\">").append(
                                $("<div class=\"product-name\">").text(alert.product_name),
                                $("<div class=\"qty-details\">").text("Current: " + alert.quantity_at_alert + ", Reorder at: " + alert.reorder_point_at_alert),
                                $("<div class=\"add-qty-form\">").append(
                                    $("<input type=\"number\" class=\"form-control input-sm\" placeholder=\"Add Qty\" id=\"" + inputId + "\" min=\"1\">"),
                                    $("<button class=\"btn btn-primary btn-xs btn-confirm-add\" data-alertid=\"" + alert.id + "\" data-productid=\"" + alert.product_id + "\">Confirm</button>")
                                )
                            );
                        alertsList.append(alertItem);
                    });
                } else {
                    $("#lowStockAlertsCount").text("");
                    alertsList.append("<li class=\"no-alerts\">No active low stock alerts.</li>");
                }
            },
            error: function() {
                var alertsList = $("#lowStockAlertsListContainer");
                alertsList.empty();
                $("#lowStockAlertsCount").text("");
                alertsList.append("<li class=\"no-alerts\">Error loading alerts.</li>");
            }
        });
    }

    // Initial load of alerts
    // Check if the alerts nav item exists and the user has permission before loading
    if ($("#mainLowStockAlertsNav").length && <?php echo (isset($user_permission) && is_array($user_permission) && in_array("viewSidebarStockAlerts", $user_permission)) ? "true" : "false"; ?>) { 
        loadLowStockAlerts();
        // Optionally, set an interval to refresh alerts periodically
        // setInterval(loadLowStockAlerts, 60000); // Refresh every 60 seconds
    }

    // Handle confirm add stock
    $(document).on("click", ".btn-confirm-add", function() {
        var alertId = $(this).data("alertid");
        var productId = $(this).data("productid");
        // Corrected selector for the input field
        var quantityToAdd = $("#add_qty_" + alertId).val();

        if (!quantityToAdd || parseInt(quantityToAdd) <= 0) {
            alert("Please enter a valid quantity to add.");
            return;
        }

        $.ajax({
            url: "<?php echo base_url("alerts/quick_add_stock"); ?>",
            type: "POST",
            data: {
                alert_id: alertId,
                product_id: productId,
                quantity_to_add: quantityToAdd,
                "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    alert(response.messages); // Or use a more sophisticated notification
                    if (<?php echo (isset($user_permission) && is_array($user_permission) && in_array("viewSidebarStockAlerts", $user_permission)) ? "true" : "false"; ?>) {
                        loadLowStockAlerts(); // Refresh the alerts list only if user has permission
                    }
                    // Potentially trigger a refresh of the main product table if visible
                    if (typeof manageTable !== "undefined" && $("#manageTable").length) {
                        manageTable.ajax.reload(null, false);
                    }
                } else {
                    alert("Error: " + response.messages);
                }
            },
            error: function() {
                alert("An error occurred while updating stock.");
            }
        });
    });

    // Make the mainLowStockAlertsNav active if its child is active or it"s the current page
    // This part might need adjustment based on how your theme handles active states for treeview
    if ($("#mainLowStockAlertsNav").length && $("#mainLowStockAlertsNav").find(".active").length > 0) {
        $("#mainLowStockAlertsNav").addClass("active menu-open");
    }
});
</script>
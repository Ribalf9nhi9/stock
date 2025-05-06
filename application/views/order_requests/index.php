<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Pending Order Requests</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url("dashboard"); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Order Requests</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata("success")): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata("success"); ?>
          </div>
        <?php elseif($this->session->flashdata("error")): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata("error"); ?>
          </div>
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Pending Order Requests</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageRequestsTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Request ID</th>
                <th>Product SKU</th>
                <th>Product Name</th>
                <th>Required Qty</th>
                <th>Request Time</th>
                <!-- Optional: Add Requested By User -->
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
                <?php if (!empty($pending_requests)): ?>
                  <?php foreach ($pending_requests as $request): ?>
                    <tr id="request-row-<?php echo $request["id"]; ?>">
                      <td><?php echo $request["id"]; ?></td>
                      <td><?php echo htmlspecialchars($request["product_sku"], ENT_QUOTES, "UTF-8"); ?></td>
                      <td><?php echo htmlspecialchars($request["product_name"], ENT_QUOTES, "UTF-8"); ?></td>
                      <td><?php echo $request["required_qty"]; ?></td>
                      <td><?php echo date("Y-m-d H:i:s", strtotime($request["request_timestamp"])); ?></td>
                      <td>
                        <?php 
                          // --- Permission Check for Fulfillment Button ---
                          // Replace 'fulfillOrderRequest' with your actual permission name
                          if(in_array("fulfillOrderRequest", $this->permission)): 
                        ?>
                          <button type="button" class="btn btn-success btn-sm fulfill-request-btn" data-request-id="<?php echo $request["id"]; ?>">
                            <i class="fa fa-check"></i> Fulfill Request
                          </button>
                        <?php else: ?>
                          <span class="text-muted">No permission</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center">No pending order requests found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->
  

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {

  // Add active class to sidebar menu item if you create one for Order Requests
  // $("#mainOrderRequestNav").addClass("active"); 

  // Initialize DataTable (optional, for sorting/searching if needed)
  // $("#manageRequestsTable").DataTable();

  // --- Fulfill Request Button Click Handler ---
  $(document).on("click", ".fulfill-request-btn", function() {
    var button = $(this);
    var requestId = button.data("request-id");
    var tableRow = $("#request-row-" + requestId);

    // Optional: Confirmation dialog
    if (!confirm("Are you sure you want to mark request #" + requestId + " as fulfilled? This will update the product stock.")) {
      return; 
    }

    // Clear previous messages
    $("#messages").html(""); 
    
    // Disable button during processing
    button.prop("disabled", true).html("<i class=\"fa fa-spinner fa-spin\"></i> Processing...");
    
    // AJAX call to backend endpoint (order_requests/fulfillRequest)
    $.ajax({
      // IMPORTANT: Include index.php/ if needed based on your server config
      url: base_url + "index.php/order_requests/fulfillRequest", 
      type: "POST",
      data: { 
        request_id: requestId 
      },
      dataType: "json",
      success: function(response) {
        if(response.success) {
          $("#messages").html(`<div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              ${response.messages}
            </div>`);
          // Remove the row from the table on success
          tableRow.fadeOut(500, function() { $(this).remove(); }); 
        } else {
          // Re-enable button on failure
          button.prop("disabled", false).html("<i class=\"fa fa-check\"></i> Fulfill Request");
          // Show error message from backend
          $("#messages").html(`<div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              Error: ${response.messages}
            </div>`);
          console.error("Backend failed to fulfill request: " + requestId + ". Error: " + response.messages);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        // Handle AJAX errors (e.g., network issues, server errors, 404)
        button.prop("disabled", false).html("<i class=\"fa fa-check\"></i> Fulfill Request");
        $("#messages").html(`<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            AJAX Error: Could not fulfill request. ${textStatus} - ${errorThrown}
          </div>`);
        console.error("AJAX error fulfilling request: " + requestId + " - " + textStatus, errorThrown);
      }
    });
  });

});
</script>


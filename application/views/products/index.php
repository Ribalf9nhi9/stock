

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Products</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Products</li>
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

        <?php if(in_array("createProduct", $user_permission)): ?>
          <a href="<?php echo base_url("products/create") ?>" class="btn btn-primary">Add Product</a>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Products</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Image</th>
                <th>SKU</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Store</th>
                <th>Availability</th>
                <?php if(in_array("updateProduct", $user_permission) || in_array("deleteProduct", $user_permission)): ?>
                  <th>Action</th>
                <?php endif; ?>
              </tr>
              </thead>

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

<!-- Proactive Low Stock Alert Modal -->
<div class="modal fade" id="proactiveLowStockModal" tabindex="-1" role="dialog" aria-labelledby="proactiveLowStockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="proactiveLowStockModalLabel">Proactive Low Stock Alert</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>The following products are at or below their reorder point and require attention:</p>
        <div id="proactiveLowStockMessage"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Acknowledge & Close</button>
      </div>
    </div>
  </div>
</div>


<!-- Negative Quantity Alert Modal (Workflow Version) -->
<div class="modal fade" id="negativeQuantityModal" tabindex="-1" role="dialog" aria-labelledby="negativeQuantityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> 
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="negativeQuantityModalLabel">Urgent: Negative Stock Alert</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>The following products have negative stock quantities. Click "Request Reorder" to notify the order group:</p>
        <div id="negativeQuantityMessage"></div> 
        <div id="reorderMessages" class="mt-2"></div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<?php if(in_array("deleteProduct", $user_permission)): ?>
<!-- remove product modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Product</h4>
      </div>

      <form role="form" action="<?php echo base_url("products/remove") ?>" method="post" id="removeForm">
        <div class="modal-body">
          <p>Do you really want to remove?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>



<script type="text/javascript">
var manageTable;
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {

  $("#mainProductNav").addClass("active");

  manageTable = $("#manageTable").DataTable({
    "ajax": base_url + "products/fetchProductData",
    "order": []
  });

  // --- Proactive Low Stock Alert Logic ---
  <?php if (!empty($low_stock_alert_products)): ?>
    let proactiveMessageHtml = `<ul class="list-group">`;
    <?php 
      foreach ($low_stock_alert_products as $product):
        $product_name_escaped = htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8");
        $product_sku_escaped = htmlspecialchars($product["sku"], ENT_QUOTES, "UTF-8");
        $product_qty = (int)$product["qty"];
        $reorder_point_product = isset($product["reorder_point"]) && $product["reorder_point"] !== null ? (int)$product["reorder_point"] : null;
        $reorder_point_category = isset($product["category_reorder_point"]) && $product["category_reorder_point"] !== null ? (int)$product["category_reorder_point"] : null;
        $effective_reorder_point = $reorder_point_product ?? $reorder_point_category ?? "N/A";
    ?>
      proactiveMessageHtml += 
        `<li class="list-group-item">
          <strong>${decodeURIComponent("<?php echo rawurlencode($product_name_escaped); ?>")} (SKU: ${decodeURIComponent("<?php echo rawurlencode($product_sku_escaped); ?>")})</strong><br>
          Current Quantity: <?php echo $product_qty; ?><br>
          Reorder Point: <?php echo $effective_reorder_point; ?>
        </li>`;
    <?php endforeach; ?>
    proactiveMessageHtml += `</ul>`;

    $("#proactiveLowStockMessage").html(proactiveMessageHtml);
    $("#proactiveLowStockModal").modal("show");
  <?php endif; ?>


  // --- Negative Stock Alert Logic (Workflow Version) ---
  <?php if (!empty($negative_products)): ?>
    let messageHtml = `<ul class="list-group">`; 
    <?php 
      foreach ($negative_products as $product): 
        $product_name_escaped = htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8");
        $product_id = $product["id"]; 
        $product_qty = $product["qty"];
    ?>
      messageHtml += 
        `<li class="list-group-item d-flex justify-content-between align-items-center" id="reorder-item-<?php echo $product_id; ?>">
          <span>${decodeURIComponent("<?php echo rawurlencode($product_name_escaped); ?>")} (Current: <?php echo $product_qty; ?>)</span>
          <button class="btn btn-warning btn-sm request-reorder-btn" data-product-id="<?php echo $product_id; ?>" type="button">Request Reorder</button>
        </li>`;
    <?php endforeach; ?>
    messageHtml += `</ul>`;

    $("#negativeQuantityMessage").html(messageHtml);
    $("#negativeQuantityModal").modal("show");
  <?php endif; ?>

  $(document).on("click", ".request-reorder-btn", function() {
    var button = $(this);
    var productId = button.data("product-id");
    var listItem = button.closest("li");

    $("#reorderMessages").html(""); 
    
    button.prop("disabled", true).text("Processing...").removeClass("btn-warning").addClass("btn-secondary");
    
    $.ajax({
      url: base_url + "products/createOrderRequest", // Corrected: Removed index.php if base_url handles it
      type: "POST",
      data: { 
        product_id: productId,
        "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>" // Added CSRF token
      },
      dataType: "json",
      success: function(response) {
        if(response.success) {
          $("#reorderMessages").html(`<div class="alert alert-success alert-sm">Order request created for product ${productId}.</div>`);
          button.text("Reorder Requested").removeClass("btn-secondary").addClass("btn-success"); 
        } else {
          button.prop("disabled", false).text("Request Reorder").removeClass("btn-secondary").addClass("btn-warning");
          $("#reorderMessages").html(`<div class="alert alert-danger alert-sm">Error: ${response.messages}</div>`);
          console.error("Backend failed to create order request for product: " + productId + ". Error: " + response.messages);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        button.prop("disabled", false).text("Request Reorder").removeClass("btn-secondary").addClass("btn-warning");
        $("#reorderMessages").html(`<div class="alert alert-danger alert-sm">AJAX Error: Could not create order request. ${textStatus} - ${errorThrown}</div>`);
        console.error("AJAX error creating order request for product: " + productId + " - " + textStatus, errorThrown);
      }
    });
  });

});

function removeFunc(id)
{
  if(id) {
    $("#removeForm").on("submit", function() {

      var form = $(this);
      $(".text-danger").remove();

      $.ajax({
        url: form.attr("action"), // Corrected: Use the form's action attribute directly
        type: form.attr("method"),
        data: { 
            product_id:id,
            "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>" // Added CSRF token
        }, 
        dataType: "json",
        success:function(response) {

          manageTable.ajax.reload(null, false); 

          if(response.success === true) {
            $("#messages").html("<div class=\"alert alert-success alert-dismissible\" role=\"alert\">"+
              "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
              "<strong> <span class=\"glyphicon glyphicon-ok-sign\"></span> </strong>"+response.messages+
            "</div>");
            $("#removeModal").modal("hide");
          } else {
            $("#messages").html("<div class=\"alert alert-warning alert-dismissible\" role=\"alert\">"+
              "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
              "<strong> <span class=\"glyphicon glyphicon-exclamation-sign\"></span> </strong>"+response.messages+
            "</div>"); 
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
           $("#messages").html("<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">"+
              "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
              "<strong>AJAX Error removing product: </strong>" + textStatus + " - " + errorThrown +
            "</div>"); 
        }
      }); 

      return false;
    });
  }
}

</script>

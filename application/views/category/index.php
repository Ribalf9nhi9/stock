

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Category</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Category</li>
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

        <?php if(in_array("createCategory", $user_permission)): ?>
          <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add Category</button>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Categories</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Category Name</th>
                <th>Default Reorder Point</th>
                <th>Status</th>
                <?php if(in_array("updateCategory", $user_permission) || in_array("deleteCategory", $user_permission)): ?>
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

<?php if(in_array("createCategory", $user_permission)): ?>
<!-- create category modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Category</h4>
      </div>

      <form role="form" action="<?php echo base_url("category/create") ?>" method="post" id="createForm">

        <div class="modal-body">

          <div class="form-group">
            <label for="category_name">Category Name</label>
            <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="default_reorder_point">Default Reorder Point</label>
            <input type="number" class="form-control" id="default_reorder_point" name="default_reorder_point" placeholder="Enter default reorder point (optional)" autocomplete="off" min="0">
            <small class="text-muted">Leave blank or 0 if not applicable. Category-wide threshold for low stock alert.</small>
          </div>

          <div class="form-group">
            <label for="active">Status</label>
            <select class="form-control" id="active" name="active">
              <option value="1">Active</option>
              <option value="2">Inactive</option>
            </select>
          </div>
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

<?php if(in_array("updateCategory", $user_permission)): ?>
<!-- edit category modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Category</h4>
      </div>

      <form role="form" action="<?php echo base_url("category/update") ?>" method="post" id="updateForm">

        <div class="modal-body">
          <div id="messages_edit"></div> <!-- Changed id to avoid conflict -->

          <div class="form-group">
            <label for="edit_category_name">Category Name</label>
            <input type="text" class="form-control" id="edit_category_name" name="edit_category_name" placeholder="Enter category name" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="edit_default_reorder_point">Default Reorder Point</label>
            <input type="number" class="form-control" id="edit_default_reorder_point" name="edit_default_reorder_point" placeholder="Enter default reorder point (optional)" autocomplete="off" min="0">
            <small class="text-muted">Leave blank or 0 if not applicable. Category-wide threshold for low stock alert.</small>
          </div>

          <div class="form-group">
            <label for="edit_active">Status</label>
            <select class="form-control" id="edit_active" name="edit_active">
              <option value="1">Active</option>
              <option value="2">Inactive</option>
            </select>
          </div>
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

<?php if(in_array("deleteCategory", $user_permission)): ?>
<!-- remove category modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Category</h4>
      </div>

      <form role="form" action="<?php echo base_url("category/remove") ?>" method="post" id="removeForm">
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
var base_url = "<?php echo base_url(); ?>"; // Define base_url for JavaScript

$(document).ready(function() {
  $("#categoryNav").addClass("active");
  
  var ajaxUrl = base_url + "category/fetchCategoryData";
  console.log("DataTable AJAX URL: ", ajaxUrl); // Log the URL to the console

  manageTable = $("#manageTable").DataTable({
    "ajax": ajaxUrl, 
    "order": [],
    "columns": [
        { "data": 0 }, // Category Name
        { "data": 1 }, // Default Reorder Point
        { "data": 2 }, // Status
        <?php if(in_array("updateCategory", $user_permission) || in_array("deleteCategory", $user_permission)): ?>
        { "data": 3 }  // Action
        <?php endif; ?>
    ]
  });

  // submit the create form 
  $("#createForm").unbind("submit").on("submit", function(e) { // Added event parameter e
    e.preventDefault(); // Prevent default form submission
    var form = $(this);
    $(".text-danger").remove();

    $.ajax({
      url: form.attr("action"),
      type: form.attr("method"),
      data: form.serialize(),
      dataType: "json",
      success:function(response) {
        manageTable.ajax.reload(null, false); 
        if(response.success === true) {
          $("#messages").html("<div class=\"alert alert-success alert-dismissible\" role=\"alert\">"+
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
            "<strong> <span class=\"glyphicon glyphicon-ok-sign\"></span> </strong>"+response.messages+
          "</div>");
          $("#addModal").modal("hide");
          $("#createForm")[0].reset();
          $("#createForm .form-group").removeClass("has-error").removeClass("has-success");
        } else {
          if(response.messages instanceof Object) {
            $.each(response.messages, function(index, value) {
              var id = $("#"+index);
              id.closest(".form-group")
              .removeClass("has-error")
              .removeClass("has-success")
              .addClass(value.length > 0 ? "has-error" : "has-success");
              id.after(value);
            });
          } else {
            $("#messages").html("<div class=\"alert alert-warning alert-dismissible\" role=\"alert\">"+
              "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
              "<strong> <span class=\"glyphicon glyphicon-exclamation-sign\"></span> </strong>"+ (response.messages ? response.messages : "Unknown error occurred") +
            "</div>");
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) { // Added error handling for AJAX
        $("#messages").html("<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">"+
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
            "<strong>An error occurred: </strong>" + textStatus + " - " + errorThrown +
          "</div>");
      }
    }); 
    return false; // Keep this to prevent default submission, though e.preventDefault() is preferred
  });

  // submit the edit form 
  $("#updateForm").unbind("submit").on("submit", function(e) { // Added event parameter e
    e.preventDefault(); // Prevent default form submission
    var form = $(this);
    $(".text-danger").remove();
    // Extract ID from form action URL for robustness
    var actionUrl = form.attr("action");
    var categoryId = actionUrl.substring(actionUrl.lastIndexOf("/") + 1);

    $.ajax({
      url: form.attr("action"), // URL already contains ID
      type: form.attr("method"),
      data: form.serialize(),
      dataType: "json",
      success:function(response) {
        manageTable.ajax.reload(null, false); 
        if(response.success === true) {
          $("#messages_edit").html("<div class=\"alert alert-success alert-dismissible\" role=\"alert\">"+
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
            "<strong> <span class=\"glyphicon glyphicon-ok-sign\"></span> </strong>"+response.messages+
          "</div>");
          $("#editModal").modal("hide");
          $("#updateForm .form-group").removeClass("has-error").removeClass("has-success");
        } else {
          if(response.messages instanceof Object) {
            $.each(response.messages, function(index, value) {
              var id = $("#"+index);
              id.closest(".form-group")
              .removeClass("has-error")
              .removeClass("has-success")
              .addClass(value.length > 0 ? "has-error" : "has-success");
              id.after(value);
            });
          } else {
            $("#messages_edit").html("<div class=\"alert alert-warning alert-dismissible\" role=\"alert\">"+
              "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
              "<strong> <span class=\"glyphicon glyphicon-exclamation-sign\"></span> </strong>"+ (response.messages ? response.messages : "Unknown error occurred") +
            "</div>");
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) { // Added error handling for AJAX
        $("#messages_edit").html("<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">"+
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
            "<strong>An error occurred: </strong>" + textStatus + " - " + errorThrown +
          "</div>");
      }
    }); 
    return false; // Keep this to prevent default submission, though e.preventDefault() is preferred
  });

});

// edit function
function editFunc(id)
{ 
  $.ajax({
    url: base_url + "category/fetchCategoryDataById/"+id, 
    type: "post",
    dataType: "json",
    success:function(response) {
      $("#edit_category_name").val(response.name);
      $("#edit_default_reorder_point").val(response.default_reorder_point);
      $("#edit_active").val(response.active);

      // Update form action URL with the correct ID
      $("#updateForm").attr("action", base_url + "category/update/" + id);

      // The submit binding for updateForm is now inside $(document).ready(), 
      // so it doesn't need to be re-bound here.
    },
    error: function(jqXHR, textStatus, errorThrown) {
        $("#messages_edit").html("<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">"+
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
            "<strong>Error fetching category data: </strong>" + textStatus + " - " + errorThrown +
          "</div>");
      }
  });
}

// remove functions 
function removeFunc(id)
{
  if(id) {
    $("#removeForm").unbind("submit").on("submit", function(e) { // Added event parameter e
      e.preventDefault(); // Prevent default form submission
      var form = $(this);

      $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: { category_id:id }, 
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
              "<strong> <span class=\"glyphicon glyphicon-exclamation-sign\"></span> </strong>"+ (response.messages ? response.messages : "Unknown error occurred") +
            "</div>"); 
          }
        },
        error: function(jqXHR, textStatus, errorThrown) { // Added error handling for AJAX
        $("#messages").html("<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">"+
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
            "<strong>An error occurred: </strong>" + textStatus + " - " + errorThrown +
          "</div>");
      }
      }); 
      return false; // Keep this to prevent default submission, though e.preventDefault() is preferred
    });
  }
}
</script>

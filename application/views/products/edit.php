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


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Edit Product</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php echo base_url("products/update/" . $product_data["id"]); ?>" method="post" enctype="multipart/form-data">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label>Image Preview: </label>
                  <img src="<?php echo base_url() . $product_data["image"] ?>" width="150" height="150" class="img-circle">
                </div>

                <div class="form-group">
                  <label for="product_image">Update Image</label>
                  <div class="kv-avatar">
                      <div class="file-loading">
                          <input id="product_image" name="product_image" type="file">
                      </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="product_name">Product name</label>
                  <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" value="<?php echo $product_data["name"]; ?>"  autocomplete="off"/>
                </div>

                <div class="form-group">
                  <label for="sku">SKU</label>
                  <input type="text" class="form-control" id="sku" name="sku" placeholder="Enter sku" value="<?php echo $product_data["sku"]; ?>" autocomplete="off" />
                </div>

                <div class="form-group">
                  <label for="price">Price</label>
                  <input type="text" class="form-control" id="price" name="price" placeholder="Enter price" value="<?php echo $product_data["price"]; ?>" autocomplete="off" />
                </div>

                <div class="form-group">
                  <label for="qty">Qty</label>
                  <input type="text" class="form-control" id="qty" name="qty" placeholder="Enter Qty" value="<?php echo $product_data["qty"]; ?>" autocomplete="off" />
                </div>

                <div class="form-group">
                  <label for="reorder_point">Reorder Point</label>
                  <input type="number" class="form-control" id="reorder_point" name="reorder_point" placeholder="Enter Reorder Point (optional)" value="<?php echo isset($product_data["reorder_point"]) ? $product_data["reorder_point"] : 
                  ""; ?>" autocomplete="off" />
                  <small class="text-muted">Leave blank or 0 if not applicable. Product-specific threshold for low stock alert.</small>
                </div>

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea type="text" class="form-control" id="description" name="description" placeholder="Enter description" autocomplete="off"><?php echo $product_data["description"]; ?></textarea>
                </div>

                <?php $attribute_id = json_decode($product_data["attribute_value_id"]); ?>
                <?php if($attributes): ?>
                  <?php foreach ($attributes as $k => $v): ?>
                    <div class="form-group">
                      <label for="groups"><?php echo $v["attribute_data"]["name"] ?></label>
                      <select class="form-control select_group" id="attributes_value_id_<?php echo $v["attribute_data"]["id"]; ?>" name="attributes_value_id[]" multiple="multiple">
                        <?php foreach ($v["attribute_value"] as $k2 => $v2): ?>
                          <option value="<?php echo $v2["id"] ?>" <?php if(is_array($attribute_id) && in_array($v2["id"], $attribute_id)) { echo "selected"; } ?>><?php echo $v2["value"] ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>    
                  <?php endforeach ?>
                <?php endif; ?>

                <div class="form-group">
                  <label for="brands">Brands</label>
                  <?php $brand_data = json_decode($product_data["brand_id"]); ?>
                  <select class="form-control select_group" id="brands" name="brands[]" multiple="multiple">
                    <?php foreach ($brands as $k => $v): ?>
                      <option value="<?php echo $v["id"] ?>" <?php if(is_array($brand_data) && in_array($v["id"], $brand_data)) { echo "selected=\"selected\""; } ?>><?php echo $v["name"] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="category">Category</label>
                  <?php $category_data = json_decode($product_data["category_id"]); ?>
                  <select class="form-control select_group" id="category" name="category[]" multiple="multiple">
                    <?php foreach ($category as $k => $v): ?>
                      <option value="<?php echo $v["id"] ?>" <?php if(is_array($category_data) && in_array($v["id"], $category_data)) { echo "selected=\"selected\""; } ?>><?php echo $v["name"] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="store">Store</label>
                  <select class="form-control select_group" id="store" name="store">
                    <?php foreach ($stores as $k => $v): ?>
                      <option value="<?php echo $v["id"] ?>" <?php if($product_data["store_id"] == $v["id"]) { echo "selected=\"selected\""; } ?> ><?php echo $v["name"] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="store">Availability</label>
                  <select class="form-control" id="availability" name="availability">
                    <option value="1" <?php if($product_data["availability"] == 1) { echo "selected=\"selected\""; } ?>>Yes</option>
                    <option value="2" <?php if($product_data["availability"] != 1) { echo "selected=\"selected\""; } ?>>No</option>
                  </select>
                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?php echo base_url("products/") ?>" class="btn btn-warning">Back</a>
              </div>
            </form>
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
  $(document).ready(function() {
    $(".select_group").select2();
    $("#description").wysihtml5();

    $("#mainProductNav").addClass("active");
    $("#manageProductNav").addClass("active");

    // Note: The fileinput initialization script from create.php might be needed here if image update is complex.
    // For simplicity, assuming basic file input is handled by the browser or a simpler setup for edit.
    // If advanced features like preview on select are needed, the fileinput script from create.php should be adapted and included.
     var btnCust = 
        襖<button type=\"button\" class=\"btn btn-secondary\" title=\"Add picture tags\" onclick=\"alert("Call your custom code here.")\">\n            <i class=\"glyphicon glyphicon-tag\"></i>\n        </button>"; 
    $("#product_image").fileinput({
        overwriteInitial: true,
        maxFileSize: 1500,
        showClose: false,
        showCaption: false,
        browseLabel: 	iny,
        removeLabel: 	iny,
        browseIcon: 	iny<i class=\"glyphicon glyphicon-folder-open\"></i>	iny,
        removeIcon: 	iny<i class=\"glyphicon glyphicon-remove\"></i>	iny,
        removeTitle: "Cancel or reset changes",
        elErrorContainer: "#kv-avatar-errors-1",
        msgErrorClass: "alert alert-block alert-danger",
        // defaultPreviewContent: 	iny<img src=\"/uploads/default_avatar_male.jpg\" alt=\"Your Avatar\">	iny,
        layoutTemplates: {main2: "{preview} " +  btnCust + " {remove} {browse}"},
        allowedFileExtensions: ["jpg", "png", "gif"]
    });

  });
</script>
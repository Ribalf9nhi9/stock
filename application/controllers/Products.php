<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Products extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data["page_title"] = "Products";

		$this->load->model("model_products");
		$this->load->model("model_brands");
		$this->load->model("model_category");
		$this->load->model("model_stores");
		$this->load->model("model_attributes");
        $this->load->model("model_order_requests"); // Load the new model
	}

    /* 
    * It only redirects to the manage product page
    */
    public function index()
    {
        if (!in_array("viewProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }
    
        $this->data["page_title"] = "Manage Products";
    
        // Fetch all products
        $this->data["products"] = $this->model_products->getProductData();
    
        // Fetch products with negative quantities
        $this->data["negative_products"] = $this->model_products->getNegativeQuantityProducts();
    
        $this->render_template("products/index", $this->data);
    }
    /*
    * It Fetches the products data from the product table 
    * this function is called from the datatable ajax function
    */
	public function fetchProductData()
	{
		$result = array("data" => array());

		$data = $this->model_products->getProductData();

		foreach ($data as $key => $value) {

            $store_data = $this->model_stores->getStoresData($value["store_id"]);
			// button
            $buttons = "";
            if(in_array("updateProduct", $this->permission)) {
    			$buttons .= 
                    "<a href=\"".base_url("products/update/".$value["id"])."\" class=\"btn btn-default\"><i class=\"fa fa-pencil\"></i></a>";
            }

            if(in_array("deleteProduct", $this->permission)) { 
    			$buttons .= 
                    " <button type=\"button\" class=\"btn btn-default\" onclick=\"removeFunc(".$value["id"].")\" data-toggle=\"modal\" data-target=\"#removeModal\"><i class=\"fa fa-trash\"></i></button>";
            }
			

			$img = 
                "<img src=\"".base_url($value["image"])."\" alt=\"".$value["name"]."\" class=\"img-circle\" width=\"50\" height=\"50\" />";

            $availability = ($value["availability"] == 1) ? 
                "<span class=\"label label-success\">Active</span>" : 
                "<span class=\"label label-warning\">Inactive</span>";

            $qty_status = "";
            // Adjusted logic to show danger for negative, warning for low (0-10)
            if($value["qty"] < 0) {
                $qty_status = "<span class=\"label label-danger\">Negative!</span>";
            } else if($value["qty"] <= 10) {
                $qty_status = "<span class=\"label label-warning\">Low!</span>";
            } 


			$result["data"][$key] = array(
				$img,
				$value["sku"],
				$value["name"],
				$value["price"],
                $value["qty"] . " " . $qty_status,
                $store_data["name"],
				$availability,
				$buttons
			);
		} // /foreach

		// Set header to application/json
        header("Content-Type: application/json");
		// Encode and echo the result
        echo json_encode($result);
	}	

    /*
    * If the validation is not valid, then it redirects to the create page.
    * If the validation for each input field is valid then it inserts the data into the database 
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function create()
	{
		// Check permission
        if(!in_array("createProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }

        // Set validation rules
		$this->form_validation->set_rules("product_name", "Product name", "trim|required");
		$this->form_validation->set_rules("sku", "SKU", "trim|required");
		$this->form_validation->set_rules("price", "Price", "trim|required");
		$this->form_validation->set_rules("qty", "Qty", "trim|required|integer"); // Ensure qty is integer
        $this->form_validation->set_rules("store", "Store", "trim|required");
		$this->form_validation->set_rules("availability", "Availability", "trim|required");
		
	
        if ($this->form_validation->run() == TRUE) {
            // true case: validation passed
        	$upload_image = $this->upload_image(); // Handle image upload

            // Prepare data for insertion
        	$data = array(
        		"name" => $this->input->post("product_name"),
        		"sku" => $this->input->post("sku"),
        		"price" => $this->input->post("price"),
        		"qty" => $this->input->post("qty"),
        		"image" => $upload_image,
        		"description" => $this->input->post("description"),
        		"attribute_value_id" => json_encode($this->input->post("attributes_value_id")), // Encode arrays as JSON
        		"brand_id" => json_encode($this->input->post("brands")), // Encode arrays as JSON
        		"category_id" => json_encode($this->input->post("category")), // Encode arrays as JSON
                "store_id" => $this->input->post("store"),
        		"availability" => $this->input->post("availability"),
        	);

            // Create product in database
        	$create = $this->model_products->create($data);
        	
            // Handle result
            if($create == true) {
        		$this->session->set_flashdata("success", "Successfully created");
        		redirect("products/", "refresh");
        	}
        	else {
        		$this->session->set_flashdata("errors", "Error occurred!!");
        		redirect("products/create", "refresh");
        	}
        }
        else {
            // false case: validation failed

        	// Prepare data for the create form view
        	$attribute_data = $this->model_attributes->getActiveAttributeData();
        	$attributes_final_data = array();
        	foreach ($attribute_data as $k => $v) {
        		$attributes_final_data[$k]["attribute_data"] = $v;
        		$value = $this->model_attributes->getAttributeValueData($v["id"]);
        		$attributes_final_data[$k]["attribute_value"] = $value;
        	}

        	$this->data["attributes"] = $attributes_final_data;
			$this->data["brands"] = $this->model_brands->getActiveBrands();        	
			$this->data["category"] = $this->model_category->getActiveCategroy();        	
			$this->data["stores"] = $this->model_stores->getActiveStore();        	

            // Render the create view with data and validation errors
            $this->render_template("products/create", $this->data);
        }	
	}

    /*
    * This function is invoked from another function to upload the image into the assets folder
    * and returns the image path
    */
	public function upload_image()
    {
    	// Configuration for image upload
        $config["upload_path"] = "assets/images/product_image";
        $config["file_name"] =  uniqid(); // Generate unique file name
        $config["allowed_types"] = "gif|jpg|png";
        $config["max_size"] = "1000"; // Max size in KB
        // $config["max_width"]  = "1024";
        // $config["max_height"]  = "768";

        $this->load->library("upload", $config);
        
        // Perform upload
        if ( ! $this->upload->do_upload("product_image"))
        {
            // Upload failed
            $error = $this->upload->display_errors();
            // Return error message (consider logging or handling more gracefully)
            return $error; 
        }
        else
        {
            // Upload successful
            $data = array("upload_data" => $this->upload->data());
            $type = explode(".", $_FILES["product_image"]["name"]);
            $type = $type[count($type) - 1]; // Get file extension
            
            // Construct the full path to the uploaded image
            $path = $config["upload_path"]."/".$config["file_name"].".".$type;
            return ($data == true) ? $path : false; // Return path if successful         
        }
    }

    /*
    * If the validation is not valid, then it redirects to the edit product page 
    * If the validation is successfully then it updates the data into the database 
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function update($product_id)
	{      
        // Check permission
        if(!in_array("updateProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }

        // Check if product ID is provided
        if(!$product_id) {
            redirect("dashboard", "refresh");
        }

        // Set validation rules
        $this->form_validation->set_rules("product_name", "Product name", "trim|required");
        $this->form_validation->set_rules("sku", "SKU", "trim|required");
        $this->form_validation->set_rules("price", "Price", "trim|required");
        $this->form_validation->set_rules("qty", "Qty", "trim|required|integer"); // Ensure qty is integer
        $this->form_validation->set_rules("store", "Store", "trim|required");
        $this->form_validation->set_rules("availability", "Availability", "trim|required");

        if ($this->form_validation->run() == TRUE) {
            // true case: validation passed
            
            // Prepare data for update
            $data = array(
                "name" => $this->input->post("product_name"),
                "sku" => $this->input->post("sku"),
                "price" => $this->input->post("price"),
                "qty" => $this->input->post("qty"),
                "description" => $this->input->post("description"),
                "attribute_value_id" => json_encode($this->input->post("attributes_value_id")), // Encode arrays as JSON
                "brand_id" => json_encode($this->input->post("brands")), // Encode arrays as JSON
                "category_id" => json_encode($this->input->post("category")), // Encode arrays as JSON
                "store_id" => $this->input->post("store"),
                "availability" => $this->input->post("availability"),
            );

            // Handle image upload if a new image is provided
            if($_FILES["product_image"]["size"] > 0) {
                $upload_image = $this->upload_image();
                $upload_image_data = array("image" => $upload_image);
                // Update image path in database first
                $this->model_products->update($upload_image_data, $product_id);
            }

            // Update product data in database
            $update = $this->model_products->update($data, $product_id);
            
            // Handle result
            if($update == true) {
                $this->session->set_flashdata("success", "Successfully updated");
                redirect("products/", "refresh");
            }
            else {
                $this->session->set_flashdata("errors", "Error occurred!!");
                redirect("products/update/".$product_id, "refresh");
            }
        }
        else {
            // false case: validation failed
            
            // Prepare data for the edit form view
            $attribute_data = $this->model_attributes->getActiveAttributeData();
            $attributes_final_data = array();
            foreach ($attribute_data as $k => $v) {
                $attributes_final_data[$k]["attribute_data"] = $v;
                $value = $this->model_attributes->getAttributeValueData($v["id"]);
                $attributes_final_data[$k]["attribute_value"] = $value;
            }
            
            $this->data["attributes"] = $attributes_final_data;
            $this->data["brands"] = $this->model_brands->getActiveBrands();         
            $this->data["category"] = $this->model_category->getActiveCategroy();           
            $this->data["stores"] = $this->model_stores->getActiveStore();          

            // Get existing product data
            $product_data = $this->model_products->getProductData($product_id);
            $this->data["product_data"] = $product_data;
            
            // Render the edit view with data and validation errors
            $this->render_template("products/edit", $this->data); 
        }   
	}

    /*
    * It removes the data from the database
    * and it returns the response into the json format
    */
	public function remove()
	{
        // Check permission
        if(!in_array("deleteProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }
        
        // Get product ID from POST request
        $product_id = $this->input->post("product_id");

        $response = array();
        if($product_id) {
            // Attempt to remove product from database
            $delete = $this->model_products->remove($product_id);
            
            // Prepare response based on result
            if($delete == true) {
                $response["success"] = true;
                $response["messages"] = "Successfully removed"; 
            }
            else {
                $response["success"] = false;
                $response["messages"] = "Error in the database while removing the product information";
            }
        }
        else {
            // No product ID provided
            $response["success"] = false;
            $response["messages"] = "Refresh the page again!!";
        }

        // Set header and echo JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
	}

    /*
    * Handles the AJAX request to create an order request for a product with negative stock.
    */
    public function createOrderRequest()
    {
        // Check permission (e.g., require viewProduct or a specific permission)
        // Using viewProduct for now, adjust as needed
        if(!in_array("viewProduct", $this->permission)) {
            $response["success"] = false;
            $response["messages"] = "Permission denied.";
            header("Content-Type: application/json");
            echo json_encode($response);
            return;
        }

        $product_id = $this->input->post("product_id");
        $user_id = $this->session->userdata("id"); // Get current user ID

        $response = array();

        if (!$product_id || !is_numeric($product_id)) {
            $response["success"] = false;
            $response["messages"] = "Invalid Product ID.";
        } else {
            // Get product data to find the negative quantity
            $product_data = $this->model_products->getProductData($product_id);

            if ($product_data && $product_data["qty"] < 0) {
                $required_qty = abs($product_data["qty"]); // Calculate needed quantity

                // Prepare data for the order_requests table
                $request_data = array(
                    "product_id" => $product_id,
                    "required_qty" => $required_qty,
                    "requested_by_user_id" => $user_id,
                    "status" => "pending" // Default status
                    // request_timestamp is handled by DB default
                );

                // Create the order request using the new model
                $create_success = $this->model_order_requests->create($request_data);

                if ($create_success) {
                    $response["success"] = true;
                    $response["messages"] = "Order request created successfully.";
                } else {
                    $response["success"] = false;
                    $response["messages"] = "Database error: Could not create order request.";
                }
            } else {
                $response["success"] = false;
                $response["messages"] = "Product not found or stock is not negative.";
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    // --- Keep other methods like confirmOrderAndUpdateQty and flagForReorder for reference or potential future use ---
    // --- but they are not used in the current notification workflow ---

    /*
    * DEPRECATED - Handles the AJAX request to update product quantity directly.
    */
    public function confirmOrderAndUpdateQty()
    {
       // ... (code remains but is not called by the current frontend setup)
       $response["success"] = false;
       $response["messages"] = "This function is deprecated.";
       header("Content-Type: application/json");
       echo json_encode($response);
    }

    /*
    * DEPRECATED - Handles the AJAX request to flag a product using needs_reorder column.
    */
    public function flagForReorder()
    {
        // ... (code remains but is not called by the current frontend setup)
        $response["success"] = false;
        $response["messages"] = "This function is deprecated.";
        header("Content-Type: application/json");
        echo json_encode($response);
    }

}


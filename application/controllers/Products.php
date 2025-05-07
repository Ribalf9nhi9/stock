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
        $this->load->model("model_order_requests"); 
	}

    public function index()
    {
        if (!in_array("viewProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }
    
        $this->data["page_title"] = "Manage Products";
    
        // Fetch all products (original functionality for the main table)
        // $this->data["products"] = $this->model_products->getProductData(); // This might not be needed if table is ajax based
    
        // Fetch products with negative quantities (original functionality)
        $this->data["negative_products"] = $this->model_products->getNegativeQuantityProducts();

        // Fetch low stock products for the alert modal
        $this->data["low_stock_alert_products"] = $this->model_products->getLowStockProducts();
    
        $this->render_template("products/index", $this->data);
    }

	public function fetchProductData()
	{
		$result = array("data" => array());

		$data = $this->model_products->getProductData(); // This now includes category_default_reorder_point

		foreach ($data as $key => $value) {

            $store_data = $this->model_stores->getStoresData($value["store_id"]);
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
            $effective_reorder_point = null;
            if (isset($value["reorder_point"]) && $value["reorder_point"] !== null && $value["reorder_point"] > 0) {
                $effective_reorder_point = (int)$value["reorder_point"];
            } elseif (isset($value["category_default_reorder_point"]) && $value["category_default_reorder_point"] !== null && $value["category_default_reorder_point"] > 0) {
                $effective_reorder_point = (int)$value["category_default_reorder_point"];
            }

            if($value["qty"] < 0) {
                $qty_status = "<span class=\"label label-danger\">Negative!</span>";
            } else if ($effective_reorder_point !== null && $value["qty"] <= $effective_reorder_point) {
                $qty_status = "<span class=\"label label-warning\">Low Stock!</span>";
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
		} 

        header("Content-Type: application/json");
        echo json_encode($result);
	}	

	public function create()
	{
        if(!in_array("createProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }

		$this->form_validation->set_rules("product_name", "Product name", "trim|required");
		$this->form_validation->set_rules("sku", "SKU", "trim|required");
		$this->form_validation->set_rules("price", "Price", "trim|required");
		$this->form_validation->set_rules("qty", "Qty", "trim|required|integer");
        $this->form_validation->set_rules("reorder_point", "Reorder Point", "trim|integer|greater_than_equal_to[0]"); // Validation for reorder_point
        $this->form_validation->set_rules("store", "Store", "trim|required");
		$this->form_validation->set_rules("availability", "Availability", "trim|required");
		
	
        if ($this->form_validation->run() == TRUE) {
        	$upload_image = $this->upload_image();

        	$data = array(
        		"name" => $this->input->post("product_name"),
        		"sku" => $this->input->post("sku"),
        		"price" => $this->input->post("price"),
        		"qty" => $this->input->post("qty"),
                "reorder_point" => ($this->input->post("reorder_point") === "" || $this->input->post("reorder_point") === null) ? null : (int)$this->input->post("reorder_point"),
        		"image" => $upload_image,
        		"description" => $this->input->post("description"),
        		"attribute_value_id" => json_encode($this->input->post("attributes_value_id")),
        		"brand_id" => json_encode($this->input->post("brands")),
        		"category_id" => json_encode($this->input->post("category")),
                "store_id" => $this->input->post("store"),
        		"availability" => $this->input->post("availability"),
        	);

        	$create = $this->model_products->create($data);
        	
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

            $this->render_template("products/create", $this->data);
        }	
	}

	public function upload_image()
    {
        $config["upload_path"] = "assets/images/product_image";
        $config["file_name"] =  uniqid();
        $config["allowed_types"] = "gif|jpg|png";
        $config["max_size"] = "1000";

        $this->load->library("upload", $config);
        
        if ( ! $this->upload->do_upload("product_image"))
        {
            $error = $this->upload->display_errors();
            return $error; 
        }
        else
        {
            $data = array("upload_data" => $this->upload->data());
            $type = explode(".", $_FILES["product_image"]["name"]);
            $type = $type[count($type) - 1];
            
            $path = $config["upload_path"]."/".$config["file_name"].".".$type;
            return ($data == true) ? $path : false;       
        }
    }

	public function update($product_id)
	{      
        if(!in_array("updateProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }

        if(!$product_id) {
            redirect("dashboard", "refresh");
        }

        $this->form_validation->set_rules("product_name", "Product name", "trim|required");
        $this->form_validation->set_rules("sku", "SKU", "trim|required");
        $this->form_validation->set_rules("price", "Price", "trim|required");
        $this->form_validation->set_rules("qty", "Qty", "trim|required|integer");
        $this->form_validation->set_rules("reorder_point", "Reorder Point", "trim|integer|greater_than_equal_to[0]"); // Validation for reorder_point
        $this->form_validation->set_rules("store", "Store", "trim|required");
        $this->form_validation->set_rules("availability", "Availability", "trim|required");

        if ($this->form_validation->run() == TRUE) {
            
            $data = array(
                "name" => $this->input->post("product_name"),
                "sku" => $this->input->post("sku"),
                "price" => $this->input->post("price"),
                "qty" => $this->input->post("qty"),
                "reorder_point" => ($this->input->post("reorder_point") === "" || $this->input->post("reorder_point") === null) ? null : (int)$this->input->post("reorder_point"),
                "description" => $this->input->post("description"),
                "attribute_value_id" => json_encode($this->input->post("attributes_value_id")),
                "brand_id" => json_encode($this->input->post("brands")),
                "category_id" => json_encode($this->input->post("category")),
                "store_id" => $this->input->post("store"),
                "availability" => $this->input->post("availability"),
            );

            if($_FILES["product_image"]["size"] > 0) {
                $upload_image = $this->upload_image();
                $upload_image_data = array("image" => $upload_image);
                $this->model_products->update($upload_image_data, $product_id);
            }

            $update = $this->model_products->update($data, $product_id);
            
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

            $product_data = $this->model_products->getProductData($product_id);
            $this->data["product_data"] = $product_data;
            
            $this->render_template("products/edit", $this->data); 
        }   
	}

	public function remove()
	{
        if(!in_array("deleteProduct", $this->permission)) {
            redirect("dashboard", "refresh");
        }
        
        $product_id = $this->input->post("product_id");

        $response = array();
        if($product_id) {
            $delete = $this->model_products->remove($product_id);
            
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
            $response["success"] = false;
            $response["messages"] = "Refresh the page again!!";
        }

        header("Content-Type: application/json");
        echo json_encode($response);
	}

    public function createOrderRequest()
    {
        if(!in_array("viewProduct", $this->permission)) {
            $response["success"] = false;
            $response["messages"] = "Permission denied.";
            header("Content-Type: application/json");
            echo json_encode($response);
            return;
        }

        $product_id = $this->input->post("product_id");
        $user_id = $this->session->userdata("id");

        $response = array();

        if (!$product_id || !is_numeric($product_id)) {
            $response["success"] = false;
            $response["messages"] = "Invalid Product ID.";
        } else {
            $product_data = $this->model_products->getProductData($product_id);

            if ($product_data && $product_data["qty"] < 0) {
                $required_qty = abs($product_data["qty"]);

                $request_data = array(
                    "product_id" => $product_id,
                    "required_qty" => $required_qty,
                    "requested_by_user_id" => $user_id,
                    "status" => "pending"
                );

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

}

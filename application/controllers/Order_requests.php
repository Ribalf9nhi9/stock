<?php

defined("BASEPATH") OR exit("No direct script access allowed");

// Note: This controller assumes an "Order Group" user role/permission exists.
// You will need to define appropriate permissions (e.g., "viewOrderRequest", "fulfillOrderRequest")
// and check them similar to how it's done in the Products controller.

class Order_requests extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in(); // Ensure user is logged in

		$this->data["page_title"] = "Order Requests";

		$this->load->model("model_order_requests");
        $this->load->model("model_products"); // Needed for updating product quantity later
	}

    /**
     * Displays the list of pending order requests.
     * Accessible by users with 'viewOrderRequest' permission.
     */
    public function index()
    {
        // --- Permission Check --- 
        // Replace 'viewOrderRequest' with your actual permission name
        if (!in_array("viewOrderRequest", $this->permission)) {
            redirect("dashboard", "refresh");
        }
    
        $this->data["page_title"] = "Pending Order Requests";
    
        // Fetch pending order requests (joining product info)
        $this->data["pending_requests"] = $this->model_order_requests->getOrderRequestData(null, "pending");
    
        // Load the view for the order group UI
        // We will create this view file in a later step (e.g., order_requests/index.php)
        $this->render_template("order_requests/index", $this->data);
    }

    /**
     * Handles the fulfillment of an order request via AJAX.
     * Accessible by users with 'fulfillOrderRequest' permission.
     */
    public function fulfillRequest()
    {
        // --- Permission Check --- 
        // Replace 'fulfillOrderRequest' with your actual permission name
        if (!in_array("fulfillOrderRequest", $this->permission)) {
            $response["success"] = false;
            $response["messages"] = "Permission denied.";
            header("Content-Type: application/json");
            echo json_encode($response);
            return;
        }

        $request_id = $this->input->post("request_id");
        $user_id = $this->session->userdata("id"); // Get current user ID

        $response = array();

        if (!$request_id || !is_numeric($request_id)) {
            $response["success"] = false;
            $response["messages"] = "Invalid Request ID.";
        } else {
            // 1. Get the request details to find the product_id and required_qty
            $request_data = $this->model_order_requests->getOrderRequestData($request_id);

            if ($request_data && $request_data["status"] == "pending") {
                $product_id = $request_data["product_id"];
                $required_qty = $request_data["required_qty"]; // This is the amount needed to reach 0
                
                // In a real scenario, you might have an input for actual received quantity.
                // For now, we assume the 'required_qty' is the amount received/ordered.
                $fulfilled_qty = $required_qty; 

                // 2. Update the product quantity
                $product_data = $this->model_products->getProductData($product_id);
                if ($product_data) {
                    $current_qty = $product_data["qty"];
                    $new_qty = $current_qty + $fulfilled_qty; // Add the fulfilled quantity

                    $product_update_data = array("qty" => $new_qty);
                    $product_update_success = $this->model_products->update($product_update_data, $product_id);

                    if ($product_update_success) {
                        // 3. Update the order request status
                        $request_update_data = array(
                            "status" => "fulfilled",
                            "fulfilled_by_user_id" => $user_id,
                            "fulfillment_timestamp" => date("Y-m-d H:i:s") // Set timestamp explicitly
                        );
                        $request_update_success = $this->model_order_requests->update($request_update_data, $request_id);

                        if ($request_update_success) {
                            $response["success"] = true;
                            $response["messages"] = "Order request fulfilled and product quantity updated.";
                        } else {
                            $response["success"] = false;
                            $response["messages"] = "Product quantity updated, but failed to update order request status.";
                            // Consider logging this inconsistency
                        }
                    } else {
                        $response["success"] = false;
                        $response["messages"] = "Failed to update product quantity.";
                    }
                } else {
                     $response["success"] = false;
                     $response["messages"] = "Product associated with the request not found.";
                }

            } else {
                $response["success"] = false;
                $response["messages"] = "Order request not found or already fulfilled/cancelled.";
            }
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

    // Potential future methods:
    // - viewFulfilledRequests()
    // - cancelRequest() 

}


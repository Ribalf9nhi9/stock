<?php

defined("BASEPATH") OR exit("No direct script access allowed");

class Alerts extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in(); // Ensure user is logged in
        $this->load->model("model_alerts");
        $this->load->model("model_products");
    }

    // Fetch active low stock alerts for sidebar or other UI elements
    public function fetch_active_low_stock_alerts()
    {
        // Check for view permission if you have a specific permission for viewing alerts
        // if(!in_array("viewAlerts", $this->permission)) {
        //    echo json_encode(array("success" => false, "messages" => "Permission denied"));
        //    return;
        // }

        $active_alerts = $this->model_alerts->get_active_alerts();
        echo json_encode(array("success" => true, "data" => $active_alerts));
    }

    // Handle stock update from the sidebar quick add feature
    public function quick_add_stock()
    {
        // Check for update permission if you have a specific permission for this action
        // if(!in_array("updateProductStock", $this->permission)) { // Or a more specific alert update permission
        //    echo json_encode(array("success" => false, "messages" => "Permission denied"));
        //    return;
        // }

        $response = array();
        $product_id = $this->input->post("product_id");
        $quantity_to_add = $this->input->post("quantity_to_add");
        $alert_id = $this->input->post("alert_id"); // The ID of the alert in low_stock_alerts table

        if (empty($product_id) || !is_numeric($product_id)) {
            $response["success"] = false;
            $response["messages"] = "Product ID is required and must be numeric.";
            echo json_encode($response);
            return;
        }

        if (empty($quantity_to_add) || !is_numeric($quantity_to_add) || (int)$quantity_to_add <= 0) {
            $response["success"] = false;
            $response["messages"] = "Quantity to add is required and must be a positive number.";
            echo json_encode($response);
            return;
        }
        
        if (empty($alert_id) || !is_numeric($alert_id)) {
            $response["success"] = false;
            $response["messages"] = "Alert ID is required for resolving the alert.";
            echo json_encode($response);
            return;
        }

        $update_stock = $this->model_products->update_quantity($product_id, (int)$quantity_to_add);

        if ($update_stock) {
            // The update_quantity method in Model_products now also handles resolving the alert if stock is sufficient.
            // We can double-check or rely on that. For explicit control, we can resolve it here too if needed.
            // $resolve_alert = $this->model_alerts->resolve_alert($alert_id);
            // if ($resolve_alert) { ... }
            
            $response["success"] = true;
            $response["messages"] = "Stock updated successfully.";
        } else {
            $response["success"] = false;
            $response["messages"] = "Error updating stock in the database.";
        }

        echo json_encode($response);
    }
}

?>

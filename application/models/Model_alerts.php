<?php

class Model_alerts extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Create a new low stock alert
    public function create_alert($data)
    {
        if (empty($data["product_id"])) {
            return false;
        }

        // Check if an active alert already exists for this product
        $this->db->where("product_id", $data["product_id"]);
        $this->db->where("status", "active");
        $query = $this->db->get("low_stock_alerts");
        if ($query->num_rows() > 0) {
            // Active alert already exists, no need to create a new one
            return true; 
        }

        $insert_data = array(
            "product_id" => $data["product_id"],
            "product_name" => $data["product_name"],
            "quantity_at_alert" => $data["quantity_at_alert"],
            "reorder_point_at_alert" => $data["reorder_point_at_alert"],
            "status" => "active" // Default status
        );
        $insert = $this->db->insert("low_stock_alerts", $insert_data);
        return ($insert) ? $this->db->insert_id() : false;
    }

    // Fetch active low stock alerts
    public function get_active_alerts()
    {
        $this->db->select("id, product_id, product_name, quantity_at_alert, reorder_point_at_alert, alert_created_at");
        $this->db->where("status", "active");
        $this->db->order_by("alert_created_at", "DESC");
        $query = $this->db->get("low_stock_alerts");
        return $query->result_array();
    }

    // Mark an alert as resolved
    public function resolve_alert($alert_id)
    {
        if (!$alert_id) {
            return false;
        }
        $data = array(
            "status" => "resolved",
            "alert_resolved_at" => date("Y-m-d H:i:s")
        );
        $this->db->where("id", $alert_id);
        $update = $this->db->update("low_stock_alerts", $data);
        return ($update) ? true : false;
    }
    
    // Get an active alert by product_id
    public function get_active_alert_by_product_id($product_id)
    {
        $this->db->where("product_id", $product_id);
        $this->db->where("status", "active");
        $query = $this->db->get("low_stock_alerts");
        return $query->row_array();
    }
}

?>

<?php

class Model_products extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("model_alerts");
    }

    public function getProductData($id = null)
    {
        $this->db->select("products.*, categories.name as category_name, categories.default_reorder_point as category_default_reorder_point");
        $this->db->from("products");
        // Assuming products.category_id is an INTEGER and categories.id is an INTEGER
        $this->db->join("categories", "products.category_id = categories.id", "left");

        if ($id) {
            $this->db->where("products.id", $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by("products.id", "DESC");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getActiveProductData()
    {
        // Assuming products.category_id is an INTEGER and categories.id is an INTEGER
        $sql = "SELECT products.*, categories.name as category_name, categories.default_reorder_point as category_default_reorder_point " .
               "FROM products " .
               "LEFT JOIN categories ON products.category_id = categories.id " .
               "WHERE products.availability = ? ORDER BY products.id DESC";
        $query = $this->db->query($sql, array(1));
        return $query->result_array();
    }

    public function create($data)
    {
        if ($data) {
            $insert = $this->db->insert("products", $data);
            if ($insert) {
                $product_id = $this->db->insert_id();
                $this->check_and_create_low_stock_alert($product_id);
                return true;
            }
            return false;
        }
        return false;
    }

    public function update($data, $id)
    {
        if ($data && $id) {
            $this->db->where("id", $id);
            $update = $this->db->update("products", $data);
            if ($update) {
                $this->check_and_create_low_stock_alert($id);
                return true;
            }
            return false;
        }
        return false;
    }

    public function getNegativeQuantityProducts()
    {
        $sql = "SELECT * FROM products WHERE qty < 0 ORDER BY id DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function remove($id)
    {
        if ($id) {
            $this->db->where("id", $id);
            $delete = $this->db->delete("products");
            return ($delete == true) ? true : false;
        }
        return false;
    }

    public function countTotalProducts()
    {
        $sql = "SELECT * FROM products";
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    public function setReorderFlag($id, $flag_value = 1)
    {
        if ($id) {
            $data = array("needs_reorder" => $flag_value);
            $this->db->where("id", $id);
            $update = $this->db->update("products", $data);
            return ($update == true) ? true : false;
        }
        return false;
    }

    public function getLowStockProducts()
    {
        // Assuming products.category_id is an INTEGER and categories.id is an INTEGER
        $sql = "SELECT p.*, c.name as category_name, c.default_reorder_point as category_reorder_point " .
               "FROM products p " .
               "LEFT JOIN categories c ON p.category_id = c.id " .
               "WHERE p.availability = 1 AND ( " .
               "    (p.reorder_point IS NOT NULL AND p.qty <= p.reorder_point AND p.reorder_point > 0) OR " .
               "    (p.reorder_point IS NULL AND c.default_reorder_point IS NOT NULL AND p.qty <= c.default_reorder_point AND c.default_reorder_point > 0) " .
               ") " .
               "ORDER BY p.id DESC";
        $query = $this->db->query($sql);
        $low_stock_products = $query->result_array();

        if (!empty($low_stock_products)) {
            foreach ($low_stock_products as $product) {
                $alert_data = array(
                    "product_id" => $product["id"],
                    "product_name" => $product["name"],
                    "quantity_at_alert" => $product["qty"],
                    "reorder_point_at_alert" => $product["reorder_point"] ? $product["reorder_point"] : $product["category_default_reorder_point"]
                );
                $this->model_alerts->create_alert($alert_data);
            }
        }
        return $low_stock_products;
    }

    public function check_and_create_low_stock_alert($product_id)
    {
        $product = $this->getProductData($product_id);
        if ($product) {
            $is_low_stock = false;
            $effective_reorder_point = null;

            if (isset($product["reorder_point"]) && $product["reorder_point"] !== null && $product["reorder_point"] > 0) {
                $effective_reorder_point = $product["reorder_point"];
            } elseif (isset($product["category_default_reorder_point"]) && $product["category_default_reorder_point"] !== null && $product["category_default_reorder_point"] > 0) {
                $effective_reorder_point = $product["category_default_reorder_point"];
            }

            if ($effective_reorder_point !== null && isset($product["qty"]) && $product["qty"] <= $effective_reorder_point) {
                $is_low_stock = true;
            }

            if ($is_low_stock && $product["availability"] == 1) {
                $alert_data = array(
                    "product_id" => $product["id"],
                    "product_name" => $product["name"],
                    "quantity_at_alert" => $product["qty"],
                    "reorder_point_at_alert" => $effective_reorder_point
                );
                $this->model_alerts->create_alert($alert_data);
            }
        }
    }

    public function update_quantity($product_id, $quantity_to_add)
    {
        if (!$product_id || !is_numeric($quantity_to_add)) {
            return false;
        }

        $product = $this->getProductData($product_id);
        if (!$product) {
            return false;
        }

        $new_quantity = $product["qty"] + (int)$quantity_to_add;
        
        $data = array("qty" => $new_quantity);
        $this->db->where("id", $product_id);
        $update = $this->db->update("products", $data);

        if ($update) {
            $this->check_and_create_low_stock_alert($product_id);
            
            $effective_reorder_point = null;
            if (isset($product["reorder_point"]) && $product["reorder_point"] !== null && $product["reorder_point"] > 0) {
                $effective_reorder_point = $product["reorder_point"];
            } elseif (isset($product["category_default_reorder_point"]) && $product["category_default_reorder_point"] !== null && $product["category_default_reorder_point"] > 0) {
                $effective_reorder_point = $product["category_default_reorder_point"];
            }

            if ($effective_reorder_point !== null && $new_quantity > $effective_reorder_point) {
                $active_alert = $this->model_alerts->get_active_alert_by_product_id($product_id);
                if ($active_alert) {
                    $this->model_alerts->resolve_alert($active_alert["id"]);
                }
            }
            return true;
        }
        return false;
    }

    public function getProductSalesForecast($product_id, $historical_period_days = 90)
    {
        if (!$product_id) {
            return array(
                "avg_daily_sales" => 0,
                "avg_weekly_sales" => 0,
                "avg_monthly_sales" => 0,
                "total_quantity_sold" => 0,
                "distinct_sale_days" => 0,
                "period_days_considered" => $historical_period_days,
                "error" => "Product ID not provided."
            );
        }

        $start_date_timestamp = strtotime("-" . $historical_period_days . " days");

        $this->db->select_sum("oi.qty", "total_quantity_sold");
        $this->db->select("COUNT(DISTINCT DATE(FROM_UNIXTIME(o.date_time))) as distinct_sale_days", FALSE);
        $this->db->select("MIN(o.date_time) as first_sale_timestamp_in_period", FALSE);
        $this->db->select("MAX(o.date_time) as last_sale_timestamp_in_period", FALSE);
        $this->db->from("orders_item oi");
        $this->db->join("orders o", "oi.order_id = o.id", "inner");
        $this->db->where("oi.product_id", $product_id);
        $this->db->where("o.paid_status", 1);
        $this->db->where("o.date_time >=", $start_date_timestamp);
        
        $query = $this->db->get();
        $result = $query->row_array();

        $total_quantity_sold = (float) ($result["total_quantity_sold"] ?? 0);
        $distinct_sale_days = (int) ($result["distinct_sale_days"] ?? 0);
        
        $avg_daily_sales = 0;
        $actual_days_in_data_span = 0;

        if ($total_quantity_sold > 0 && $result["first_sale_timestamp_in_period"] && $result["last_sale_timestamp_in_period"]) {
            $first_sale_date = new DateTime("@" . $result["first_sale_timestamp_in_period"]);
            $last_sale_date = new DateTime("@" . $result["last_sale_timestamp_in_period"]);
            $period_start_date = new DateTime("@" . $start_date_timestamp);
            $today = new DateTime(); 

            $effective_start = max($period_start_date, $first_sale_date);
            $effective_end   = min($today, $last_sale_date); 
            
            $date_interval = $effective_start->diff($effective_end);
            $actual_days_in_data_span = $date_interval->days + 1; 
            
            if ($actual_days_in_data_span > 0) {
                 $avg_daily_sales = $total_quantity_sold / $actual_days_in_data_span;
            } else if ($distinct_sale_days > 0) { 
                 $avg_daily_sales = $total_quantity_sold / $distinct_sale_days;
            } else {
                $avg_daily_sales = $total_quantity_sold / $historical_period_days; 
            }

        } else {
            $actual_days_in_data_span = $historical_period_days; 
        }
        
        $avg_weekly_sales = $avg_daily_sales * 7;
        $avg_monthly_sales = $avg_daily_sales * 30; 

        return array(
            "avg_daily_sales" => round($avg_daily_sales, 2),
            "avg_weekly_sales" => round($avg_weekly_sales, 2),
            "avg_monthly_sales" => round($avg_monthly_sales, 2),
            "total_quantity_sold" => $total_quantity_sold,
            "distinct_sale_days_in_period" => $distinct_sale_days,
            "actual_days_in_data_span" => $actual_days_in_data_span, 
            "historical_period_days_setting" => $historical_period_days
        );
    }
}

?>

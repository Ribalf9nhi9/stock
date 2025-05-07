<?php

// Corrected Class Name: Model_products
class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
        $this->load->model("model_alerts"); // Load Model_alerts
	}

	/* get the product data */
	public function getProductData($id = null)
	{
		$this->db->select("products.*, categories.name as category_name, categories.default_reorder_point as category_default_reorder_point");
		$this->db->from("products");
		// Corrected JSON_EXTRACT syntax
		$this->db->join("categories", "JSON_UNQUOTE(JSON_EXTRACT(products.category_id, '$[0]')) = categories.id", "left"); 

		if($id) {
			$this->db->where("products.id", $id);
			$query = $this->db->get();
			return $query->row_array();
		}

		$this->db->order_by("products.id", "DESC");
		$query = $this->db->get();
		return $query->result_array();
	}

    /* get active product data */
	public function getActiveProductData()
	{
        // Corrected JSON_EXTRACT syntax
		$sql = "SELECT products.*, categories.name as category_name, categories.default_reorder_point as category_default_reorder_point FROM products LEFT JOIN categories ON JSON_UNQUOTE(JSON_EXTRACT(products.category_id, '$[0]')) = categories.id WHERE products.availability = ? ORDER BY products.id DESC";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

    /* create a new product */
	public function create($data)
	{
        if($data) {
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

    /* update product data */
	public function update($data, $id)
	{
        if($data && $id) {
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

    /* get products with negative quantity */
	public function getNegativeQuantityProducts()
	{
		$sql = "SELECT * FROM products WHERE qty < 0 ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

    /* remove a product */
	public function remove($id)
	{
        if($id) {
            $this->db->where("id", $id);
            $delete = $this->db->delete("products");
            return ($delete == true) ? true : false;
		}
        return false;
	}

    /* count total number of products */
	public function countTotalProducts()
	{
		$sql = "SELECT * FROM products";
		$query = $this->db->query($sql);
        return $query->num_rows();
	}

    /* Set the needs_reorder flag for a product - This might be deprecated or adapted */
    public function setReorderFlag($id, $flag_value = 1)
    {
        if($id) {
            $data = array("needs_reorder" => $flag_value); // Assuming "needs_reorder" column exists
            $this->db->where("id", $id);
            $update = $this->db->update("products", $data);
            return ($update == true) ? true : false;
        }
        return false;
    }

    /* get low stock products */
    public function getLowStockProducts()
    {
        // Corrected JSON_EXTRACT syntax
        $sql = "SELECT p.*, c.name as category_name, c.default_reorder_point as category_reorder_point 
                FROM products p
                LEFT JOIN categories c ON JSON_UNQUOTE(JSON_EXTRACT(p.category_id, '$[0]')) = c.id
                WHERE p.availability = 1 AND (
                    (p.reorder_point IS NOT NULL AND p.qty <= p.reorder_point AND p.reorder_point > 0) OR 
                    (p.reorder_point IS NULL AND c.default_reorder_point IS NOT NULL AND p.qty <= c.default_reorder_point AND c.default_reorder_point > 0)
                )
                ORDER BY p.id DESC";
        $query = $this->db->query($sql);
        $low_stock_products = $query->result_array();

        // Automatically create alerts for these products if not already active
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

    // Check and create low stock alert for a specific product
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

    // Update product quantity directly
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
            // After updating quantity, re-check if an alert needs to be created or if an existing one can be resolved
            $this->check_and_create_low_stock_alert($product_id);
            
            // Attempt to resolve if stock is now above reorder point
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
}

?>
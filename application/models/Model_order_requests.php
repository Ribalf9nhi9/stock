<?php 

defined("BASEPATH") OR exit("No direct script access allowed");

class Model_order_requests extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Creates a new order request.
	 * 
	 * @param array $data Data for the new order request (product_id, required_qty, requested_by_user_id, etc.)
	 * @return bool True on success, false on failure.
	 */
	public function create($data)
	{
		// Check if data is provided
        if($data) {
            // Ensure required fields are present (at least product_id and required_qty)
            if (!isset($data["product_id"]) || !isset($data["required_qty"])) {
                return false; // Missing essential data
            }

            // Set default status if not provided
            if (!isset($data["status"])) {
                $data["status"] = "pending";
            }

            // Set request timestamp if not provided (though DB default should handle this)
            if (!isset($data["request_timestamp"])) {
                 $data["request_timestamp"] = date("Y-m-d H:i:s");
            }

			// Insert data into the 'order_requests' table
            $insert = $this->db->insert("order_requests", $data);
			// Return true if insertion was successful, false otherwise
            return ($insert == true) ? true : false;
		}
        // Return false if no data provided
        return false;
	}

    /**
     * Retrieves order request data.
     *
     * @param int|null $id Optional ID to fetch a specific request.
     * @param string|null $status Optional status to filter requests (e.g., 'pending').
     * @return array Array of order requests or single request array if ID is provided.
     */
    public function getOrderRequestData($id = null, $status = null)
    {
        $this->db->select("order_requests.*, products.name as product_name, products.sku as product_sku"); // Select columns and join product name/sku
        $this->db->from("order_requests");
        $this->db->join("products", "products.id = order_requests.product_id", "left"); // Join with products table

        if ($id) {
            $this->db->where("order_requests.id", $id);
            $query = $this->db->get();
            return $query->row_array(); // Return single row
        }

        if ($status) {
            $this->db->where("order_requests.status", $status);
        }

        $this->db->order_by("order_requests.request_timestamp", "DESC"); // Order by request time
        $query = $this->db->get();
        return $query->result_array(); // Return array of results
    }

    /**
     * Updates an order request.
     *
     * @param array $data Data to update (e.g., status, fulfilled_by_user_id, fulfillment_timestamp).
     * @param int $id The ID of the order request to update.
     * @return bool True on success, false on failure.
     */
    public function update($data, $id)
    {
        if ($data && $id) {
            // Set fulfillment timestamp if status is changing to 'fulfilled' and timestamp isn't set
            if (isset($data["status"]) && $data["status"] == "fulfilled" && !isset($data["fulfillment_timestamp"])) {
                $data["fulfillment_timestamp"] = date("Y-m-d H:i:s");
            }

            $this->db->where("id", $id);
            $update = $this->db->update("order_requests", $data);
            return ($update == true) ? true : false;
        }
        return false;
    }

    // Potential future methods:
    // - countPendingRequests()
    // - getRequestsByProduct($product_id)
    // - remove($id) // If needed
}


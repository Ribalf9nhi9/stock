<?php 

// Corrected Class Name: Model_products
class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
        // Removed incorrect call to $this->not_logged_in();
	}

	/* get the product data */
	public function getProductData($id = null)
	{
		// If an ID is provided, fetch a single product
        if($id) {
			$sql = "SELECT * FROM products where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array(); // Return single row
		}

        // If no ID, fetch all products
		$sql = "SELECT * FROM products ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array(); // Return array of results
	}

    /* get active product data */
	public function getActiveProductData()
	{
		$sql = "SELECT * FROM products WHERE availability = ? ORDER BY id DESC";
		$query = $this->db->query($sql, array(1)); // Fetch only active products
		return $query->result_array();
	}

    /* create a new product */
	public function create($data)
	{
		// Check if data is provided
        if($data) {
			// Insert data into the 'products' table
            $insert = $this->db->insert("products", $data);
			// Return true if insertion was successful, false otherwise
            return ($insert == true) ? true : false;
		}
        // Return false if no data provided
        return false;
	}

    /* update product data */
	public function update($data, $id)
	{
		// Check if data and ID are provided
        if($data && $id) {
			// Set the WHERE clause for the update
            $this->db->where("id", $id);
			// Perform the update on the 'products' table
            $update = $this->db->update("products", $data);
			// Return true if update was successful, false otherwise
            return ($update == true) ? true : false;
		}
        // Return false if no data or ID provided
        return false;
	}

    /* get products with negative quantity */
	public function getNegativeQuantityProducts()
	{
		$sql = "SELECT * FROM products WHERE qty < 0 ORDER BY id DESC"; // Fetch products where quantity is less than 0
		$query = $this->db->query($sql);
		return $query->result_array();
	}

    /* remove a product */
	public function remove($id)
	{
		// Check if ID is provided
        if($id) {
			// Set the WHERE clause for deletion
            $this->db->where("id", $id);
			// Perform the delete operation on the 'products' table
            $delete = $this->db->delete("products");
			// Return true if deletion was successful, false otherwise
            return ($delete == true) ? true : false;
		}
        // Return false if no ID provided
        return false;
	}

    /* count total number of products */
	public function countTotalProducts()
	{
		$sql = "SELECT * FROM products";
		$query = $this->db->query($sql);
		// Return the total number of rows found
        return $query->num_rows();
	}

    /* Set the needs_reorder flag for a product */
    public function setReorderFlag($id, $flag_value = 1)
    {
        // Check if ID is provided
        if($id) {
            // Data to update: set the needs_reorder column to the flag value (default 1)
            $data = array("needs_reorder" => $flag_value);
            // Set the WHERE clause
            $this->db->where("id", $id);
            // Perform the update
            $update = $this->db->update("products", $data);
            // Return true if update was successful, false otherwise
            return ($update == true) ? true : false;
        }
        // Return false if no ID provided
        return false;
    }

}


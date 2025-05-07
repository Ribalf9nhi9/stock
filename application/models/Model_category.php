<?php 

class Model_category extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get active brand infromation */
	public function getActiveCategroy()
	{
		$sql = "SELECT * FROM categories WHERE active = ?";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

	/* get the brand data */
	public function getCategoryData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM categories WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM categories";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function create($data)
	{
		if($data) {
			// Ensure default_reorder_point is either an integer or null
			if (isset($data["default_reorder_point"]) && $data["default_reorder_point"] === 
                "") {
				$data["default_reorder_point"] = null;
			}
			$insert = $this->db->insert("categories", $data);
			return ($insert == true) ? true : false;
		}
		return false; // Added return false if no data
	}

	public function update($data, $id)
	{
		if($data && $id) {
			// Ensure default_reorder_point is either an integer or null
			if (isset($data["default_reorder_point"]) && $data["default_reorder_point"] === 
                "") {
				$data["default_reorder_point"] = null;
			}
			$this->db->where("id", $id);
			$update = $this->db->update("categories", $data);
			return ($update == true) ? true : false;
		}
		return false; // Added return false if no data or id
	}

	public function remove($id)
	{
		if($id) {
			$this->db->where("id", $id);
			$delete = $this->db->delete("categories");
			return ($delete == true) ? true : false;
		}
		return false; // Added return false if no id
	}

}
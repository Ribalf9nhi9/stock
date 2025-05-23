<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Category';

		$this->load->model('model_category');
	}

	public function index()
	{
		if(!in_array('viewCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->render_template('category/index', $this->data);	
	}	

	public function fetchCategoryDataById($id) 
	{
		if($id) {
			$data = $this->model_category->getCategoryData($id);
			echo json_encode($data);
		}
		// Removed return false as it's not standard for CI controller methods outputting JSON
	}

	public function fetchCategoryData()
	{
		$result = array('data' => array());

		$data = $this->model_category->getCategoryData();

		foreach ($data as $key => $value) {

			$buttons = '';

			if(in_array('updateCategory', $this->permission)) {
				$buttons .= '<button type="button" class="btn btn-default" onclick="editFunc('.$value['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button>';
			}

			if(in_array('deleteCategory', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}
				

			$status = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

			$result['data'][$key] = array(
				$value['name'],
                isset($value['default_reorder_point']) ? $value['default_reorder_point'] : 'N/A', // Display default reorder point
				$status,
				$buttons
			);
		} 

		echo json_encode($result);
	}

	public function create()
	{
		if(!in_array('createCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		$this->form_validation->set_rules('category_name', 'Category name', 'trim|required');
		$this->form_validation->set_rules('active', 'Active', 'trim|required');
        $this->form_validation->set_rules('default_reorder_point', 'Default Reorder Point', 'trim|integer|greater_than_equal_to[0]');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
        	$data = array(
        		'name' => $this->input->post('category_name'),
        		'active' => $this->input->post('active'),
                'default_reorder_point' => ($this->input->post('default_reorder_point') === "" || $this->input->post('default_reorder_point') === null) ? null : (int)$this->input->post('default_reorder_point'),
        	);

        	$create = $this->model_category->create($data);
        	if($create == true) {
        		$response['success'] = true;
        		$response['messages'] = 'Succesfully created';
        	}
        	else {
        		$response['success'] = false;
        		$response['messages'] = 'Error in the database while creating the category information';			
        	}
        }
        else {
        	$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }

        echo json_encode($response);
	}

	public function update($id)
	{
		if(!in_array('updateCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_category_name', 'Category name', 'trim|required');
			$this->form_validation->set_rules('edit_active', 'Active', 'trim|required');
            $this->form_validation->set_rules('edit_default_reorder_point', 'Default Reorder Point', 'trim|integer|greater_than_equal_to[0]');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'name' => $this->input->post('edit_category_name'),
	        		'active' => $this->input->post('edit_active'),	
                    'default_reorder_point' => ($this->input->post('edit_default_reorder_point') === "" || $this->input->post('edit_default_reorder_point') === null) ? null : (int)$this->input->post('edit_default_reorder_point'),
	        	);

	        	$update = $this->model_category->update($data, $id);
	        	if($update == true) {
	        		$response['success'] = true;
	        		$response['messages'] = 'Succesfully updated';
	        	}
	        	else {
	        		$response['success'] = false;
	        		$response['messages'] = 'Error in the database while updated the category information';			
	        	}
	        }
	        else {
	        	$response['success'] = false;
	        	foreach ($_POST as $key => $value) {
	        		$response['messages'][$key] = form_error($key);
	        	}
	        }
		}
		else {
			$response['success'] = false;
    		$response['messages'] = 'Error please refresh the page again!!';
		}

		echo json_encode($response);
	}

	public function remove()
	{
		if(!in_array('deleteCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}
		
		$category_id = $this->input->post('category_id');

		$response = array();
		if($category_id) {
			$delete = $this->model_category->remove($category_id);
			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the category information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}

		echo json_encode($response);
	}

}
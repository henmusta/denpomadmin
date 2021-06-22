<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Create By : Aryo
 * Youtube : Aryo Coding
 */
class Mod_pengguna extends CI_Model
{
	var $table = 'tbl_pengguna';
	var $column_search = array('id','nama','email','hp','nik','status'); 
	var $column_order = array('id','nama','email','hp','nik','status');
	var $order = array('id' => 'desc'); 
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

		private function _get_datatables_query()
	{
		
		$this->db->from('tbl_pengguna');
		$i = 0;

	foreach ($this->column_search as $item) // loop column 
	{
	if($_POST['search']['value']) // if datatable send POST for search
	{

	if($i===0) // first loop
	{
	$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
	$this->db->like($item, $_POST['search']['value']);
	}
	else
	{
		$this->db->or_like($item, $_POST['search']['value']);
	}

		if(count($this->column_search) - 1 == $i) //last loop
		$this->db->group_end(); //close bracket
	}
	$i++;
	}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	function count_all()
	{
		$this->db->from('tbl_pengguna');
		return $this->db->count_all_results();
	}

    function cek_email($email)
    {
        $this->db->where("email",$email);
        return $this->db->get("tbl_pengguna");
    }

    function insert_pengguna($tabel, $data)
    {
        $insert = $this->db->insert($tabel, $data);
        return $insert;
    }

    function update_pengguna($id, $data)
    {
        $this->db->where('id', $id);
		$this->db->update('tbl_pengguna', $data);
    }
    

    function get_image($id)
    {
        $this->db->select('image');
        $this->db->from('tbl_pengguna');
        $this->db->where('id', $id);
        return $this->db->get();
    }

    function get_pengguna($id)
    {   
        $this->db->where('id',$id);
        return $this->db->get('tbl_pengguna')->row();
    }


    function delete_pengguna($id, $table)
    {
        $this->db->where('id', $id);
        $this->db->delete($table);
    }

}
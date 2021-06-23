<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Create By : Aryo
 * Youtube : Aryo Coding
 */
class Mod_pengaduan_petugas extends CI_Model
{
	var $table = 'tbl_laporan';
	var $column_search = array('tbl_laporan.id','tbl_pengguna.nama','tbl_laporan.status'); 
	var $column_order = array('tbl_laporan.id','tbl_pengguna.nama','tbl_laporan.status');
	var $order = array('tbl_laporan.id' => 'desc'); 
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

		private function _get_datatables_query()
	{
		
		$this->db->select('tbl_laporan.id as id, 
			         tbl_pengguna.nama as nama, 
			         tbl_pengguna.image as image_pengguna, 
			         tbl_laporan.alamat as alamat, 
			         tbl_laporan.deskripsi as deskripsi, 
			         tbl_laporan.tanggal as tanggal, 
			         tbl_laporan.lat as lat, 
			         tbl_laporan.lon as lon, 
			         tbl_laporan.image as image_laporan, 
			         kategori.nama_kat as nama_kat, 
			         tbl_laporan.status as status ')
		->from('tbl_laporan')
		->join('tbl_pengguna','tbl_pengguna.id = tbl_laporan.id_pengguna ')
		->join('kategori','kategori.id = tbl_laporan.id_kategori ')
		->where('tbl_pengguna.level !=','Warga')
		->group_by('tbl_laporan.id');
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
		$this->db->from('tbl_laporan');
		$this->db->join('tbl_pengguna','tbl_pengguna.id = tbl_laporan.id_pengguna ');
		return $this->db->count_all_results();
	}


    function update_laporan($id, $data)
    {
        $this->db->where('id', $id);
		$this->db->update('tbl_laporan', $data);
    }
    

    function get_pengaduan($id)
    {   
    	return $this->db->select('tbl_laporan.id as id, 
			         tbl_pengguna.nama as nama, 
			         tbl_pengguna.email as email, 
			         tbl_pengguna.image as image_pengguna, 
			         tbl_pengguna.alamat as alamat_pengguna, 
			         tbl_pengguna.hp as hp, 
			         tbl_pengguna.nik as nik, 
			         tbl_laporan.alamat as alamat_tkp, 
			         tbl_laporan.deskripsi as deskripsi, 
			         tbl_laporan.tanggal as tanggal, 
			         tbl_laporan.lat as lat, 
			         tbl_laporan.lon as lon, 
			         tbl_laporan.image as image_laporan, 
			         kategori.nama_kat as nama_kat, 
			         tbl_laporan.status as status ')
		->from('tbl_laporan')
		->join('tbl_pengguna','tbl_pengguna.id = tbl_laporan.id_pengguna ')
		->join('kategori','kategori.id = tbl_laporan.id_kategori ')
		->where('tbl_laporan.id',$id)
		->group_by('tbl_laporan.id')->get();
        // $this->db->where('id',$id);
        // return $this->db->get('tbl_laporan');
    }

    function delete_pengguna($id, $table)
    {
        $this->db->where('id', $id);
        $this->db->delete($table);
    }

    function konfirmasi_pengaduan($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_laporan', $data);
    }

}
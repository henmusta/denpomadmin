<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/**
 * Create BY Aryo
 */
class Pengaduan_warga extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mod_pengaduan_warga');

    }

    public function index()
    {
        $this->template->load('layoutbackend', 'pengaduan/Pengaduan_warga');
    }


    public function ajax_list()
    {
        ini_set('memory_limit','512M');
        set_time_limit(3600);
        $list = $this->Mod_pengguna->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $pengguna) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $pengguna->nama;
            $row[] = $pengguna->email;
            $row[] = $pengguna->hp;
            $row[] = $pengguna->nik;            
            $row[] = $pengguna->alamat;
            $row[] = $pengguna->image;
            $row[] = $pengguna->status;
            $row[] = $pengguna->level;
            $row[] = $pengguna->id;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Mod_pengguna->count_all(),
                        "recordsFiltered" => $this->Mod_pengguna->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function update_pengguna()
    {
      
    }

    public function edit_pengguna($id)
    {    
        $data = $this->Mod_pengguna->get_pengguna($id);
        echo json_encode($data);
    }


    public function delete_pengguna(){
        $id = $this->input->post('id');
        $this->Mod_pengguna->delete_pengguna($id, 'tbl_pengguna');
        $data['status'] = TRUE;
        echo json_encode($data);
    }


}
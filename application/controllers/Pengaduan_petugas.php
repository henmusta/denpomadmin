<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/**
 * Create BY Aryo
 */
class Pengaduan_petugas extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_pengaduan_petugas');

    }

    public function index()
    {
        $this->template->load('layoutbackend', 'pengaduan/pengaduan_warga');
    }

    public function ajax_list()
    {
        ini_set('memory_limit','512M');
        set_time_limit(3600);
        $list = $this->mod_pengaduan_petugas->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $laporan) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $laporan->nama;
            $row[] = $laporan->image_pengguna;
            $row[] = $laporan->tanggal;
            $row[] = $laporan->alamat;   
            $row[] = $laporan->deskripsi;         
            $row[] = $laporan->image_laporan;
            $row[] = $laporan->nama_kat;
            $row[] = $laporan->status;
            $row[] = $laporan->id;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->mod_pengaduan_petugas->count_all(),
                        "recordsFiltered" => $this->mod_pengaduan_petugas->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }


    public function konfirmasi_pengaduan()
    {
        $id      = $this->input->post('id');
        $save  = array(
            'status' => $this->input->post('status'),
            'keterangan' => $this->input->post('keterangan')
        );
        $this->mod_pengaduan_petugas->konfirmasi_pengaduan($id, $save);
        echo json_encode(array("status" => TRUE));
    }

    function view_pengaduan(){
        // get personil
        $id = $this->input->post('id');
        $data_pengaduan = $this->mod_pengaduan_petugas->get_pengaduan($id);
        $cek = $data_pengaduan->row_array();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }else{
            if ($cek['lat']!=null){
                $status = 'success';
                $msg = $data_pengaduan->result();
            }else{
                $status = 'error';
                $msg = 'alamat tidak ditemukan';
                $data_pengaduan = null;
            }
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status'=>$status,'msg'=>$msg,'data_pengaduan'=>$data_pengaduan)));
        }
    }



    public function delete_laporan(){
        $id = $this->input->post('id');
        $g = $this->mod_pengaduan_petugas->get_image($id)->row_array();
        if ($g != null) {
            //hapus gambar yg ada diserver
            unlink('assets/foto/laporan/'.$g['image']);
        }
        $this->mod_pengaduan_petugas->delete_laporan($id, 'tbl_laporan');
        $data['status'] = TRUE;
        echo json_encode($data);
    }


}
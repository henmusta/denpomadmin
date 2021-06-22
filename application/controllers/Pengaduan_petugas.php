<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/**
 * Create BY Aryo
 */
class Pengguna extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mod_pengguna');

    }

    public function index()
    {
        $this->template->load('layoutbackend', 'pengguna/pengguna');
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


    public function insert_pengguna()
    {
       // var_dump($this->input->post('username'));
        $this->_validate();
        $email = $this->input->post('email');
        $cek = $this->Mod_pengguna->cek_email($email);
        if($cek->num_rows() > 0){
            echo json_encode(array("error" => "Email Sudah Digunakan!!"));
        }else{
            $nama = slug($this->input->post('nama'));
            $config['upload_path']   = './assets/foto/pengguna/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png'; //mencegah upload backdor
            $config['max_size']      = '1000';
            $config['max_width']     = '2000';
            $config['max_height']    = '1024';
            $config['file_name']     = $nama; 
            
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('imagefile')){
            $gambar = $this->upload->data();
            
            $save  = array(
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'hp' => $this->input->post('hp'),
                'nik' => $this->input->post('nik'),
                'alamat'  => $this->input->post('alamat'),
                'password'  => get_hash($this->input->post('password')),
                'level'  => $this->input->post('level'),
                'status' => $this->input->post('status'),
                'image' => $gambar['file_name']
            );
            
            $this->Mod_pengguna->insert_pengguna("tbl_pengguna", $save);
            echo json_encode(array("status" => TRUE));
            }else{//Apabila tidak ada gambar yang di upload
                $save  = array(
                'nama' => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'hp' => $this->input->post('hp'),
                'nik' => $this->input->post('nik'),
                'alamat'  => $this->input->post('alamat'),
                'password'  => get_hash($this->input->post('password')),
                'level'  => $this->input->post('level'),
                'status' => $this->input->post('status'),
                'image' => $gambar['file_name']
            );
            
            $this->Mod_pengguna->insert_pengguna("tbl_pengguna", $save);
            echo json_encode(array("status" => TRUE));
            }
        }
    }

    public function update_pengguna()
    {
        if(!empty($_FILES['imagefile']['name'])) {
        // $this->_validate();
        $id = $this->input->post('id');
        
        $nama = slug($this->input->post('nama'));
        $config['upload_path']   = './assets/foto/pengguna/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png'; //mencegah upload backdor
        $config['max_size']      = '1000';
        $config['max_width']     = '2000';
        $config['max_height']    = '1024';
        $config['file_name']     = $nama; 
        
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('imagefile')){
            $gambar = $this->upload->data();
            //Jika Password tidak kosong
            if ($this->input->post('password')) {
                    $save  = array(
                    'nama'    => $this->input->post('nama'),
                    'email'   => $this->input->post('email'),
                    'hp'      => $this->input->post('hp'),
                    'nik'     => $this->input->post('nik'),
                    'alamat'  => $this->input->post('alamat'),
                    'password'  => get_hash($this->input->post('password')),
                    'level'   => $this->input->post('level'),
                    'status'  => $this->input->post('status'),
                    'image'   => $gambar['file_name']
                );
            }else{//Jika password kosong
                $save  = array(
                    'nama'    => $this->input->post('nama'),
                    'email'   => $this->input->post('email'),
                    'hp'      => $this->input->post('hp'),
                    'nik'     => $this->input->post('nik'),
                    'alamat'  => $this->input->post('alamat'),
                    'level'   => $this->input->post('level'),
                    'status'  => $this->input->post('status'),
                'image' => $gambar['file_name']
                );
            }
            
            
            $g = $this->Mod_pengguna->get_image($id)->row_array();

            if ($g != null) {
                //hapus gambar yg ada diserver
                unlink('assets/foto/pengguna/'.$g['image']);
            }
            
            $this->Mod_pengguna->update_pengguna($id, $save);
            echo json_encode(array("status" => TRUE));
            }else{//Apabila tidak ada gambar yang di upload

                 //Jika Password tidak kosong
            if ($this->input->post('password')) {
                    $save  = array(
                          'nama'    => $this->input->post('nama'),
                    'email'   => $this->input->post('email'),
                    'hp'      => $this->input->post('hp'),
                    'nik'     => $this->input->post('nik'),
                    'alamat'  => $this->input->post('alamat'),
                    'password'  => get_hash($this->input->post('password')),
                    'level'   => $this->input->post('level'),
                    'status'  => $this->input->post('status')
                );
            }else{//Jika password kosong
                $save  = array(
                     'nama'    => $this->input->post('nama'),
                    'email'   => $this->input->post('email'),
                    'hp'      => $this->input->post('hp'),
                    'nik'     => $this->input->post('nik'),
                    'alamat'  => $this->input->post('alamat'),
                    'level'   => $this->input->post('level'),
                    'status'  => $this->input->post('status')
                );
            }
             
                $this->Mod_pengguna->update_pengguna($id, $save);
                echo json_encode(array("status" => TRUE));
            }
        }else{
            // $this->_validate();
            $id = $this->input->post('id');
            if ($this->input->post('password')) {
                $save  = array(
             'nama'    => $this->input->post('nama'),
                    'email'   => $this->input->post('email'),
                    'hp'      => $this->input->post('hp'),
                    'nik'     => $this->input->post('nik'),
                    'alamat'  => $this->input->post('alamat'),
                    'password'  => get_hash($this->input->post('password')),
                    'level'   => $this->input->post('level'),
                    'status'  => $this->input->post('status')
                );
            }else{
                $save  = array(
                'nama'    => $this->input->post('nama'),
                    'email'   => $this->input->post('email'),
                    'hp'      => $this->input->post('hp'),
                    'nik'     => $this->input->post('nik'),
                    'alamat'  => $this->input->post('alamat'),
                    'level'   => $this->input->post('level'),
                    'status'  => $this->input->post('status')
                );
            }
            
            $this->Mod_pengguna->update_pengguna($id, $save);
            echo json_encode(array("status" => TRUE));
        }
    }



    // public function update_pengguna()
    // {
    //     if(!empty($_FILES['imagefile']['name'])) {
    //     // $this->_validate();
    //     $id = $this->input->post('id');
        
    //     $nama = slug($this->input->post('nama'));
    //     $config['upload_path']   = './assets/foto/pengguna/';
    //     $config['allowed_types'] = 'gif|jpg|jpeg|png'; //mencegah upload backdor
    //     $config['max_size']      = '1000';
    //     $config['max_width']     = '2000';
    //     $config['max_height']    = '1024';
    //     $config['file_name']     = $nama; 
        
    //         $this->upload->initialize($config);
            
    //         if ($this->upload->do_upload('imagefile')){
    //         $gambar = $this->upload->data();
    //         //Jika Password tidak kosong
    //         if ($this->input->post('password')) {
    //                 $save  = array(
    //                     'nama' => $this->input->post('nama'),
    //                     'email' => $this->input->post('email'),
    //                     'hp' => $this->input->post('hp'),
    //                     'nik' => $this->input->post('nik'),
    //                     'alamat'  => $this->input->post('alamat'),
    //                     'password'  => get_hash($this->input->post('password')),
    //                     'level'  => $this->input->post('level'),
    //                     'status' => $this->input->post('status'),
    //                     'image' => $gambar['file_name']
    //             );
    //         }else{//Jika password kosong
    //             $save  = array(
    //                     'nama' => $this->input->post('nama'),
    //                     'email' => $this->input->post('email'),
    //                     'hp' => $this->input->post('hp'),
    //                     'nik' => $this->input->post('nik'),
    //                     'alamat'  => $this->input->post('alamat'),
    //                     'level'  => $this->input->post('level'),
    //                     'status' => $this->input->post('status'),
    //                     'image' => $gambar['file_name']
    //             );
    //         }
            
            
    //         $g = $this->Mod_pengguna->get_image($id)->row_array();

    //         if ($g != null) {
    //             //hapus gambar yg ada diserver
    //             unlink('assets/foto/pengguna/'.$g['image']);
    //         }
            
    //         $this->Mod_pengguna->update_pengguna($id, $save);
    //         echo json_encode(array("status" => TRUE));
    //         }else{//Apabila tidak ada gambar yang di upload

    //              //Jika Password tidak kosong
    //         if ($this->input->post('password')) {
    //                 $save  = array(
    //                  'nama' => $this->input->post('nama'),
    //                     'email' => $this->input->post('email'),
    //                     'hp' => $this->input->post('hp'),
    //                     'nik' => $this->input->post('nik'),
    //                     'alamat'  => $this->input->post('alamat'),
    //                     'password'  => get_hash($this->input->post('password')),
    //                     'level'  => $this->input->post('level'),
    //                     'status' => $this->input->post('status'),
    //                      'image' => $gambar['file_name']
    //             );
    //         }else{//Jika password kosong
    //             $save  = array(
    //            'nama' => $this->input->post('nama'),
    //                     'email' => $this->input->post('email'),
    //                     'hp' => $this->input->post('hp'),
    //                     'nik' => $this->input->post('nik'),
    //                     'alamat'  => $this->input->post('alamat'),
    //                     'level'  => $this->input->post('level'),
    //                     'status' => $this->input->post('status'),
    //                      'image' => $gambar['file_name']
    //             );
    //         }
             
    //             $this->Mod_pengguna->update_pengguna($id, $save);
    //             echo json_encode(array("status" => TRUE));
    //         }
    //     }else{
    //         // $this->_validate();
    //         $id = $this->input->post('id');
    //         if ($this->input->post('password')) {
    //             $save  = array(
    //             'nama' => $this->input->post('nama'),
    //                     'email' => $this->input->post('email'),
    //                     'hp' => $this->input->post('hp'),
    //                     'nik' => $this->input->post('nik'),
    //                     'alamat'  => $this->input->post('alamat'),
    //                     'level'  => $this->input->post('level'),
    //                     'status' => $this->input->post('status'),
    //                     'image' => $gambar['file_name']
    //             );
    //         }else{
    //             $save  = array(
    //                       'nama' => $this->input->post('nama'),
    //                     'email' => $this->input->post('email'),
    //                     'hp' => $this->input->post('hp'),
    //                     'nik' => $this->input->post('nik'),
    //                     'alamat'  => $this->input->post('alamat'),
    //                     'level'  => $this->input->post('level'),
    //                     'status' => $this->input->post('status'),
    //                      'image' => $gambar['file_name']
    //             );
    //         }
            
    //         $this->Mod_pengguna->update_pengguna($id, $save);
    //         echo json_encode(array("status" => TRUE));
    //     }
    // }
    public function edit_pengguna($id)
    {    
        $data = $this->Mod_pengguna->get_pengguna($id);
        echo json_encode($data);
    }
    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($this->input->post('nama') == '')
        {
            $data['inputerror'][] = 'nama';
            $data['error_string'][] = 'Nama Perlu Diisi!';
            $data['status'] = FALSE;
        }

        if($this->input->post('email') == '')
        {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'email Perlu Diisi!';
            $data['status'] = FALSE;
        }

        if($this->input->post('nik') == '')
        {
            $data['inputerror'][] = 'nik';
            $data['error_string'][] = 'NIK Perlu Diisi!';
            $data['status'] = FALSE;
        }

        if($this->input->post('password') == '')
        {
            $data['inputerror'][] = 'password';
            $data['error_string'][] = 'Password Perlu Diisi!';
            $data['status'] = FALSE;
        }

        if($this->input->post('status') == '')
        {
            $data['inputerror'][] = 'status';
            $data['error_string'][] = 'Status Perlu Dipilih';
            $data['status'] = FALSE;
        }

        if($this->input->post('level') == '')
        {
            $data['inputerror'][] = 'level';
            $data['error_string'][] = 'Level Perlu Dipilih';
            $data['status'] = FALSE;
        }

        /*if($this->input->post('image') == '')
        {
            $data['inputerror'][] = 'image';
            $data['error_string'][] = 'Image is required';
            $data['status'] = FALSE;
        }*/

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }
    public function delete_pengguna(){
        $id = $this->input->post('id');
        $g = $this->Mod_pengguna->get_image($id)->row_array();
        if ($g != null) {
            //hapus gambar yg ada diserver
            unlink('assets/foto/pengguna/'.$g['image']);
        }
        $this->Mod_pengguna->delete_pengguna($id, 'tbl_pengguna');
        $data['status'] = TRUE;
        echo json_encode($data);
    }


}
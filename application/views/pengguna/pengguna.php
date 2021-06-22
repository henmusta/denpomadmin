
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header bg-light">
						<h3 class="card-title"><i class="fa fa-list text-blue"></i> Data Pengguna</h3>
						<div class="text-right">
							<button type="button" class="btn btn-sm btn-outline-primary" onclick="add_pengguna()" title="Add Data"><i class="fas fa-plus"></i> Add</button>
						</div>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<table id="tbl_pengguna" class="table table-bordered table-striped table-hover">
							<thead>
								<tr class="bg-info">
									<th>No</th>
									<th>Nama</th>
									<th>Email</th>
                                    <th>Hp</th>
                                    <th>NIK</th>
                                    <th>Alamat</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Level</th>
                                    <th>Aksi</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<script type="text/javascript">
var save_method; //for save method string
var table;

$(document).ready(function() {

    //datatables
    table =$("#tbl_pengguna").DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "sEmptyTable": "Data Pengguna Belum Ada"
        },
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('pengguna/ajax_list')?>",
            "type": "POST"
        },
         //Set column definition initialisation properties.
         "columnDefs": [
         { 
            "targets": [ -1 ], //last column
            "render": function ( data, type, row ) {

              if (row[7]=="Suspend" || "Blokir") { 
                return "<a class=\"btn btn-xs btn-outline-info\" href=\"javascript:void(0)\" title=\"View\" onclick=\"vuser("+row[9]+")\"><i class=\"fas fa-eye\"></i></a> <a class=\"btn btn-xs btn-outline-primary\"  href=\"javascript:void(0)\" title=\"Edit\" onclick=\"edit_pengguna("+row[9]+")\"><i class=\"fas fa-edit\"></i></a><a class=\"btn btn-xs btn-outline-danger\" href=\"javascript:void(0)\" title=\"Delete\"  onclick=\"delete_pengguna("+row[9]+")\"><i class=\"fas fa-trash\"></i></a>"
              }else{
               return "<a class=\"btn btn-xs btn-outline-info\" href=\"javascript:void(0)\" title=\"View\" onclick=\"vuser("+row[9]+")\"><i class=\"fas fa-eye\"></i></a> <a class=\"btn btn-xs btn-outline-primary\" href=\"javascript:void(0)\" title=\"Edit\" onclick=\"edit_pengguna("+row[9]+")\"><i class=\"fas fa-edit\"></i></a> <a class=\"btn btn-xs btn-outline-warning\" href=\"javascript:void(0)\" title=\"Reset Password\" onclick=\"riset("+row[9]+")\"><i>Riset Pass</i></a>";
             }

            },

            "orderable": false, //set not orderable
        },

         {
            "targets": [ 6 ],
            "render": function(data , type , row){
              if (row[6]!=null) {
                return "<img class=\"myImgx\"  src='<?php echo base_url("assets/foto/pengguna/");?>"+row[6]+"' width=\"100px\" height=\"100px\">";
              }else{
                return "<img class=\"myImgx\"  src='<?php echo base_url("assets/foto/default-150x150.png");?>' width=\"100px\" height=\"100px\">";
              }
            }
          },

        ],
    });

 //set input/textarea/select event when change value, remove class error and remove text help block 
 $("input").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
    $(this).removeClass('is-invalid');
 });
 $("textarea").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
    $(this).removeClass('is-invalid');
 });
 $("select").change(function(){
    $(this).parent().parent().removeClass('has-error');
    $(this).next().empty();
    $(this).removeClass('is-invalid');
 });

});

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});


//delete
function delkat(id){

    Swal.fire({
  title: 'Are you sure?',
  text: "You won't be able to revert this!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes, delete it!'
}).then((result) => {

        $.ajax({
        url:"<?php echo site_url('kategori/delete');?>",
        type:"POST",
        data:"id="+id,
        cache:false,
         dataType: 'json',
        success:function(respone){
        if (respone.status == true) {
            reload_table();
        Swal.fire(
          'Deleted!',
          'Your file has been deleted.',
          'success'
        );
        }else{
          Toast.fire({
                  icon: 'error',
                  title: 'Delete Error!!.'
                });
        }
        }
    });
})
}


function batal() {
    $('#form')[0].reset();
    reload_table();
    var image = document.getElementById('v_image');
    image.src ="<?php echo base_url('assets/foto/user/default.png')?>";
  }


function add_pengguna()
{
  save_method = 'add';
    var image = document.getElementById('v_image');
    image.src ="<?php echo base_url('assets/foto/user/default.png')?>";
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Add User'); // Set Title to Bootstrap modal title
  }



  function edit_pengguna(id){

   save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
      url : "<?php echo site_url('pengguna/edit_pengguna')?>/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {

            $('[name="id"]').val(data.id);
            $('[name="nama"]').val(data.nama);
            $('[name="email"]').val(data.email);
            $('[name="nik"]').val(data.nik);
            $('[name="alamat"]').val(data.alamat);
            $('[name="password"]').val(data.password);
            $('[name="hp"]').val(data.hp);
            $('[name="status"]').val(data.status);
            $('[name="level"]').val(data.level);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Pengguna'); // Set title to Bootstrap modal title
        
        if (data.image==null) {
          var image = "<?php echo base_url('assets/foto/user/default.png')?>";
          $("#v_image").attr("src",image);
        }else{
         var image = "<?php echo base_url('assets/foto/pengguna/')?>";
         $("#v_image").attr("src",image+data.image);
       }
       
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit User'); // Set title to Bootstrap modal title

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert('Error get data from ajax');
          }
        });
  }





  var loadFile = function(event) {
    var image = document.getElementById('v_image');
    image.src = URL.createObjectURL(event.target.files[0]);
  };


  function save()
  {
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
     if(save_method == 'add') {
        url = "<?php echo site_url('Pengguna/insert_pengguna')?>";//arahin ke kategori insert
    } else {
        url = "<?php echo site_url('Pengguna/update_pengguna')?>";//arahin ke kategori update
    }
    var formdata = new FormData($('#form')[0]);
    $.ajax({
      url : url,
      type: "POST",
      data: formdata,
      dataType: "JSON",
      cache: false,
      contentType: false,
      processData: false,
      success: function(data)
      {

            if(data.status) //if success close modal and reload ajax table
            {
              $('#modal_form').modal('hide');
              reload_table();
              Toast.fire({
                icon: 'success',
                title: 'Success!!.'
              });
            }
            else
            {
              for (var i = 0; i < data.inputerror.length; i++) 
              {
                $('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
                $('[name="'+data.inputerror[i]+'"]').closest('.kosong').append('<span></span>');
                $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]).addClass('invalid-feedback');
              }
            }
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert(textStatus);
            // alert('Error adding / update data');
            Toast.fire({
              icon: 'error',
              title: 'Error!!.'
            });
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

          }
        });
  }


  function delete_pengguna(id){
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {

      $.ajax({
        url:"<?php echo site_url('pengguna/delete_pengguna');?>",
        type:"POST",
        data:"id="+id,
        cache:false,
        dataType: 'json',
        success:function(respone){
          if (respone.status == true) {
            reload_table();
            Swal.fire(
              'Deleted!',
              'Your file has been deleted.',
              'success'
              );
          }else{
            Toast.fire({
              icon: 'error',
              title: 'Delete Error!!.'
            });
          }
        }
      });

    })
  }

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content ">

			<div class="modal-header">
				<h3 class="modal-title"></h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>

			</div>
			<div class="modal-body form">
			     <form action="#" id="form" class="form-horizontal" enctype="multipart/form-data">
					<input type="hidden" value="" name="id"/> 
					<div class="card-body">
						<div class="form-group row ">
							<label for="nama" class="col-sm-3 col-form-label">Nama</label>
							<div class="col-sm-9 kosong">
								<input type="text" class="form-control" name="nama" id="nama" placeholder="Nama Pengguna" >
								<span class="help-block"></span>
							</div>
						</div>
                        <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9 kosong">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email Pengguna" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">Hp</label>
                            <div class="col-sm-9 kosong">
                                <input type="text" class="form-control" name="hp" id="hp" placeholder="Nomor Handphone" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">NIK</label>
                            <div class="col-sm-9 kosong">
                                <input type="text" class="form-control" name="nik" id="nik" placeholder="NIK" >
                                <span class="help-block"></span>
                            </div>

                        </div>
                         <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">Alamat</label>
                            <div class="col-sm-9 kosong">
                                <input type="text" class="form-control" name="alamat" id="alamat" placeholder="alamat" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">Password</label>
                            <div class="col-sm-9 kosong">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group row ">
                          <label for="is_active" class="col-sm-3 col-form-label">Status</label>
                          <div class="col-sm-9 kosong">
                            <select class="form-control" name="status" id="status">
                              <option value=""></option>
                              <option value="Aktif">Aktif</option>
                              <option value="Blokir">Blokir</option>
                              <option value="Suspend">Suspend</option>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row ">
                            <label for="nama" class="col-sm-3 col-form-label">Level</label>
                            <div class="col-sm-9 kosong">
                            <select class="form-control" name="level" id="level">
                              <option value=""></option>
                              <option value="Admin">Admin</option>
                              <option value="Komandan">Komandan</option>
                              <option value="Anggota">Anggota</option>
                              <option value="Warga">Warga</option>
                            </select>
                            </div>
                        </div>
                       <div class="form-group row ">
                         <label for="imagefile" class="col-sm-3 col-form-label">Foto</label>
                         <div class="col-sm-9 kosong ">
                         <img  id="v_image" width="100px" height="100px">
                         <input type="file" class="form-control btn-file" onchange="loadFile(event)" name="imagefile" id="imagefile" placeholder="Image" value="UPLOAD">
                      </div>
            </div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
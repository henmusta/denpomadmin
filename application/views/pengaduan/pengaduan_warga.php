
<!-- Main content -->
<section class="content" id="datatable" name="datatable">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header bg-light">
						<h3 class="card-title"><i class="fa fa-list text-blue"></i> Data Pengaduan</h3>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<table id="tbl_pen_warga" class="table table-bordered table-striped table-hover">
							<thead>
								<tr class="bg-info">
									<th>No</th>
                  <th>Nama</th>
                  <th>Gambar User</th>
                  <th>Tanggal</th>
                  <th>Alamat TKP</th>
                  <th>Deskripsi Singkat</th>
                  <th>Gambar Laporan</th>
                  <th>Kategori</th>
                  <th>Status</th>
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






<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
<script>
  $("#clskonfir").on('click', function(){
    $("#detail_laporan").attr("hidden",true);
    $("#datatable").attr("hidden",false);
  });
  var map;
  var markers = [];
  function initialize() {
    var mapOptions = {
      zoom: 16,
            center: new google.maps.LatLng(-7.018590, 110.409794)
          };
          map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
            google.maps.event.addListener(map, 'rightclick', addLatLng);
            google.maps.event.addListener(map, "rightclick", function(event) {
              var lat = event.latLng.lat();
              var lng = event.latLng.lng();     
              $('#latitude').val(lat);
              $('#longitude').val(lng);
          });
          }
        function addLatLng(event) {
          var marker = new google.maps.Marker({
            position: event.latLng,
            title: 'Simple GIS',
            map: map
          });
          markers.push(marker);
        }
        function clearmap(){
          $('#latitude').val('');
          $('#longitude').val('');
          setMapOnAll(null);
        }
        function setMapOnAll(map) {
          for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
          }
          markers = [];
        }
        function edit_laporan(id){
          $("#detail_laporan").attr("hidden",false);
          $("#datatable").attr("hidden",true);
          $.ajax({
            url : "<?php echo site_url('pengaduan_warga/view_pengaduan')?>/",
            data:"id="+id,
            dataType : 'json',
            type : 'POST',
            success : function(data,status){
              if (data.status!='error') {
                clearmap();
                        //load marker
                        $.each(data.msg,function(m,n){
                          var myLatLng = {lat: parseFloat(n["lat"]), lng: parseFloat(n["lon"])};
                          addMarker(myLatLng);

                          $('[name="id"]').val(n["id"]);

                          $('[name="nama"]').val(n["nama"]);

                          $('[name="email"]').val(n["email"]);

                          $('[name="alamat"]').val(n["alamat_pengguna"]);

                          $('[name="hp"]').val(n["hp"]);

                          $('[name="nik"]').val(n["nik"]);

                          $('[name="deskripsi"]').val(n["deskripsi"]);


                          if (n['image_pengguna']==null) {
                            var image = "<?php echo base_url('assets/foto/user/default.png')?>";
                            $("#p_image").attr("src",image);
                          }else{
                           var image = "<?php echo base_url('assets/foto/pengguna/')?>";
                           $("#p_image").attr("src",image+n['image_pengguna']);
                         }


                         return false;
                       })
                      }else{
                        alert(data.msg);
                      }
                    }
                  })
        }
        // Menampilkan marker lokasi jembatan
        function addMarker(location) {
          console.log(location);
          var marker = new google.maps.Marker({
            position: location,
            map: map,
            animation: google.maps.Animation.BOUNCE,
            title : "Pelapor"
          });
          markers.push(marker);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
      </script>
      <script type="text/javascript">
var save_method; //for save method string
// var table;

$(document).ready(function() {

    //datatables
    table =$("#tbl_pen_warga").DataTable({
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
          "url": "<?php echo site_url('pengaduan_warga/ajax_list')?>",
          "type": "POST"
        },
         //Set column definition initialisation properties.
         "columnDefs": [
         { 
            "targets": [ -1 ], //last column
            "render": function ( data, type, row ) {

              if (row[8] =="Ditolak") { 
                return "<a class=\"btn btn-xs btn-outline-primary\"  href=\"javascript:void(0)\" title=\"Konfirmasi\" onclick=\"edit_laporan("+row[9]+")\"><i class=\"fas fa-edit\"></i>Konfirmasi</a><a class=\"btn btn-xs btn-outline-danger\" href=\"javascript:void(0)\" title=\"Delete\"  onclick=\"delete_laporan("+row[9]+")\"><i class=\"fas fa-trash\"></i></a>"
              }else{
               return "<a class=\"btn btn-xs btn-outline-primary\"  href=\"javascript:void(0)\" title=\"Konfirmasi\" onclick=\"edit_laporan("+row[9]+")\"><i class=\"fas fa-edit\"></i>Konfirmasi</a>";
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
function del_lap(id){

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

function Konfirmasi()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url = "<?php echo site_url('pengaduan_warga/konfirmasi_pengaduan')?>";//arahin ke kategori update
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
              $("#detail_laporan").attr("hidden",true);
              $("#datatable").attr("hidden",false);
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

</script>



<section class="content" id="detail_laporan" name="detail_laporan" hidden="true">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">

        <!-- Profile Image -->
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <div class="text-center">
              <img id="p_image" name="p_image" class="profile-user-img img-fluid img-circle" src="" alt="User profile picture">
            </div>
            <h3 class="profile-username text-center">Profil</h3>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- About Me Box -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Detail Pelapor</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">



            <strong><i class="fas fa-book mr-1"></i>Email</strong>

            <input type="text" class="form-control form-control-border" id="email" name="email" placeholder="Email" readonly="readonly">

            <hr>

            <strong><i class="fas fa-book mr-1"></i>Nama</strong>

            <input type="text" class="form-control form-control-border" id="nama" name="nama" placeholder="Nama" readonly="readonly">

            <hr>

            <strong><i class="fas fa-map-marker-alt mr-1"></i>Alamat</strong>

            <input type="text" class="form-control form-control-border" id="alamat" name="alamat" placeholder="Alamat" readonly="readonly">

            <hr>

            <strong><i class="fas fa-pencil-alt mr-1"></i>Nomor Hp</strong>

            <input type="text" class="form-control form-control-border" id="hp" name="hp" placeholder="Handphone" readonly="readonly">

            <hr>

            <strong><i class="fas fa-pencil-alt mr-1"></i>NIK</strong>

            <input type="text" class="form-control form-control-border" id="nik" name="nik" placeholder="NIK" readonly="readonly">

            <hr>

            <strong><i class="fas fa-pencil-alt mr-1"></i>DESKRIPSI</strong>

            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>

            <hr>

          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Konfirmasi Pengaduan</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <form action="#" id="form" class="form-horizontal" enctype="multipart/form-data">
               <input type="hidden" id="id" name="id" readonly="readonly">
              <div class="form-group">
                <input type="radio" name="status" value="Menunggu"> Menunggu<br>
                <input type="radio" name="status" value="Diproses"> Diproses<br>
                <input type="radio" name="status" value="Diterima"> Diterima<br>
                <input type="radio" name="status" value="Ditolak">  Ditolak
              </div>
            <div class="form-group">
              <label for="exampleFormControlTextarea1">Masukan Pesan Tanggapan Pengaduan</label>
              <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>

            <button type="button" onclick="Konfirmasi()" id="btnSave" name="btnSave" class="btn btn-primary btn-block"><b>Tanggapi Pengaduan</b></button>


          </form>
        </div>
        <!-- /.card-body -->
      </div>
    </div>
    <!-- /.col -->
    <div class="col-md-9">
      <div class="card">
        <div class="card-header bg-light">
         <button type="button" class="btn btn-info">Maps</button>
         <button type="button" id="clskonfir" class="btn btn-default float-right">Batal</button>
       </div>
       <!-- /.card-header -->
       <div class="card-body">
        <div class="jumbotron">
          <ul class="list-group">
            <li class="list-group-item">
              <div class="panel-body" style="height:400px;" id="map-canvas">    
              </li>
            </ul>
          </div>

          <!-- /.card -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Foto Laporan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="panel-body" style="height:400px;">    
                <img src="" class="product-image" alt="Product Image">
              </div>
            </div>
            <!-- /.card-body -->
          </div>

        </div>
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</div><!-- /.container-fluid -->
</section>
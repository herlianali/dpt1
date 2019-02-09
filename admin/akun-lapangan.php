<style>
  .formvalidasi{
  	position:absolute;
  	padding:10px;
  	border-radius:5px;
  	background:#fff;
  	box-shadow: 0px 0px 4px 0px #ccc;
  	text-align:right;
  	z-index: 999;
  }
</style>

  <?php
    atable_init();
    if(isset($_GET['delete']) && $_GET['delete'] != '') {
    	if($_GET['delete'] != ID) {
        //echo "DELETE FROM akun_lapangan WHERE nik=".$_GET['delete'];
    		pg_query("DELETE FROM akun_lapangan1 WHERE nik='$_GET[delete]'");
    		?><script>window.location="?h=akun-lapangan";</script><?php
    		die();
    	}
    }


    $atableb = new Atable();
    $atableb->limit = 5;
    $atableb->caption = "Data Akun Lapangan";
    $atableb->query = "SELECT nik, nama, kecamatan, desa, rt, rw FROM penduduk1";
    $atableb->col = '["nik", "nama", "kecamatan", "desa", "rt", "rw", "$action;"]';
    $atableb->colv = '["NIK", "NAMA", "KECAMATAN", "DESA", "RT", "RW", "ACTION"]';
    $atableb->colnumber = TRUE;
    $atableb->collist=TRUE;
    $atableb->xls=TRUE;
    $atableb->param= 'extract(params($row));';
    echo $atableb->load();

    function params($row){
      $cekakun = pg_num_rows(pg_query($GLOBALS['dpt'],"select * from akun_lapangan1 where nik='$row->nik'"));
      $password = pg_fetch_array(pg_query($GLOBALS['dpt'],"select * from akun_lapangan1 where nik='$row->nik'"));
      if($cekakun==1){
        $data['action'] = "<button type='button' onclick='showhidefrm(\"".$row->nik."\")' class='btn btn-primary btn-xs' title='Lihat Password'><center><span class='fa fa-eye' aria-hidden='true'></span></center></button>
        <div id='frmvalidlppm".$row->nik."' style='display:none;' class='formvalidasi'>
          <label>Username : ".$password['username']."<br> Password : ".$password['password']."
         </div>
         <a class='btn btn-danger btn-xs' title='Hapus Akun' onClick='return confirm(\"Apakah Anda ingin menghapus data ini?\")' href='?h=akun-lapangan&delete=".$password["nik"]."' role='button'>
		      <center><span class='fa fa-trash' aria-hidden='true'></span></center></a>";
      }else{
        $data['action'] ="<a href='#myModal' id='custId' class='btn btn-primary btn-xs' data-toggle='modal' data-id=".$row->nik.">Buat Akun</a>";
      }
        return $data;
    }
  ?>
  <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <form role="form" id="form-edit" method="post" action="?h=akun-lapangan">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  				<h3 class="modal-title" id="myModalLabel">Data Akun</h3>
  			</div>
  			<div class="modal-body">
  				<div class="fetched-data" style="text-align: justify; background-color: #fff; opacity:0.8;"></div>
  			</div>
  			<div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
  				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  			</div>
  		</div>
    </form>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
  	$('#myModal').on('show.bs.modal', function (e) {
  		var rowid = $(e.relatedTarget).data('id');
  		$.ajax({
  			url : 'simpan-akun.php?rowid='+ rowid,
  			method : 'get',
  			success : function(data){
  				$('.fetched-data').html(data);
  			}
  		});
  	});
  });

var validfrm=true;
function showhidefrm(id){
	var divfrm=document.getElementById('frmvalidlppm'+id);
	if(validfrm){
		divfrm.style.display="block";
		validfrm=false;
	}else{
		divfrm.style.display="none";
		validfrm=true;
	}
}
</script>

  <?php
  if(isset($_POST['simpan'])){
    $nik = $_POST['nik'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $update = pg_query($dpt,"insert into akun_lapangan1 (nik,username,password) values ('$nik','$username','$password')");
  }
  ?>

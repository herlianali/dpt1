<?php
atable_init();
?>
<style>
.atable td{
  font-size: 16px !important;
}
.table {
	border-collapse: collapse;
	font-size: 13px;
}
.table th,
.table td {
	border: 1px solid #e1edff;
	border-top: 0;
	border-right: 0;
	padding: 7px 17px;
}
.table th,
.table td:last-child {
	border-right: 1px solid #e1edff;
}
caption.title {
	caption-side: top;
	margin-bottom: 10px;
}

/* Table Body */
.table tbody td {
	color: #353535;
	position: relative;
	white-space: nowrap;
}
.table tbody td:empty{
	background-color: #ffcccc;
}
.toolbox {
	width: 45px;
}
a.more {
	color: #717171;
	display: block;
	position: absolute;
	right: 3px;
	top: 26px;
	padding: 10px;
	display: none !important;
}
.btn-right{
  position: absolute;
  right: 50px;
  top: 26px;
  padding: 10px;
	display: none;
}

@media screen and (max-width: 659px) {
  .atable .table td {
    text-align: left !important;
  }
  .atable .table tr {
    margin-bottom: 3px;
  }

	.collapses .more, .btn-right {
		display: block !important;
	}
	.collapses thead th.column-primary {
		width: 100%;
	}

	.collapses thead th:not(.column-primary) {
		display:none;
	}

	.collapses td {
		display: block;
		width: auto;
	}

	.collapses td:nth-child(n+2)::before {
		float: left;
		text-transform: uppercase;
		font-weight: bold;
		content: attr(data-header);
		width: 80px;
	}

	.collapses .expanded td:nth-child(n+2) {
		display: block;
	}

	.collapses td:nth-child(n+2) {
		display: none;
	}

}
</style>
<?php
if(isset($_POST['nkk'])){
  $nkk = $_POST['nkk'];
  $status = '1';

  $inputwarga = pg_query("update penduduk1 set status='$status', petugas='".$_SESSION['nik']."', tgl_approve='".date('Y-m-d H:i:s')."' where nkk='$nkk'");
  if($inputwarga){
    echo "<script>window.location='?h=cari'</script>";
  }else{
    echo "<script>window.location='?h=cari'</script>";
  }
}
$kcmt = "";
$dsa = "";
$t = "";

if(isset($_POST['toatable'])){
  $kcmt = $_POST['kecamatan'];
  $dsa = $_POST['desa'];
  $t = $_POST['tps'];
}

  $andwhere="";
if($kcmt!=""){
	$andwhere .= "and kecamatan ='$kcmt' ";
}
if($dsa!=""){
	$andwhere .= "and desa ='$dsa' ";
}
if($t!=""){
	$andwhere .= "and tps = '$t' ";
}


  if (isset($_GET['pesan'])) {
	  $pesan=$_GET['pesan'];
	  if($pesan==1){
	    echo "<div class='alert alert-success' role='alert'>
	    <strong><i class='ti-check'></i> Sukses!</strong>
	    Data Sudah Tersimpan. Anda terdaftar sebagai warga Muhammadiyah
	    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
	  	<span aria-hidden='true'>&times;</span>
			</button>
	    </div>";
		}elseif ($pesan==2) {
			echo "<div class='alert alert-danger' role='alert'>
		  <strong><i class='ti-alert'></i> Gagal!</strong>
	    Data Gagal Tersimpan
		  <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
		  <span aria-hidden='true'>&times;</span>
		  </button>
		  </div>";
		}
	}
//end Approve
?>

<section class="panel">
  <div class="panel-body">
    <form class="form-horizontal bucket-form" method="POST" action="">
      <div class="form-group">
        <label class="col-sm-3 control-label col-lg-3" for="inputSuccess">Kecamatan</label>
        <div class="col-lg-6">
          <select id="kecamatan" class="form-control m-bot15" name="kecamatan">
            <option value="">--Pilih Kecamatan--</option>
            <?php
            $kec = pg_query("select kecamatan from penduduk1 group by kecamatan order by kecamatan asc");
            while($kecamatan = pg_fetch_array($kec)){
              if($kecamatan['kecamatan']==$kcmt){
                echo "<option value=".$kecamatan['kecamatan']." selected>".$kecamatan['kecamatan']."</option>";
              }else{
                echo "<option value=".$kecamatan['kecamatan'].">".$kecamatan['kecamatan']."</option>";
              }
            }
            ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label col-lg-3" for="inputSuccess">Desa</label>
        <div class="col-lg-6">
          <select id="desa" class="form-control m-bot15" name="desa">
            <option value="">--Pilih Desa--</option>
            <?php
            $ds = pg_query("select kecamatan,desa from penduduk1 group by kecamatan,desa order by desa asc");
            while($desa = pg_fetch_array($ds)){
              if($desa['desa']==$dsa){
                echo "<option id='desa' class=".$desa['kecamatan']." value=".$desa['desa']." selected>".$desa['desa']."</option>";
              }else{
                echo "<option id='desa' class=".$desa['kecamatan']." value=".$desa['desa'].">".$desa['desa']."</option>";
              }
            }
            ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label col-lg-3" for="inputSuccess">TPS</label>
        <div class="col-lg-6">
          <select id="tps" class="form-control m-bot15" name="tps">
            <option value="">--Pilih TPS--</option>
            <?php
            $tp = pg_query("select desa, tps from penduduk1 group by desa,tps order by tps asc");
            while($tps = pg_fetch_array($tp)){
              if($tps['tps']==$t){
                echo "<option id='tps' class=".$tps['desa']." value=".$tps['tps']." selected>".$tps['tps']."</option>";
              }else{
                echo "<option id='tps' class=".$tps['desa']." value=".$tps['tps'].">".$tps['tps']."</option>";
              }
            }
            ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <button type="submit" name="toatable" class="btn btn-success">Cari</button>
        </div>
      </div>
    </form>
  </div>
</section>

<?php
$lokasi="";
$level=$_SESSION['level'];
if($level==0){
  $dtl=pg_fetch_array(pg_query("select * from penduduk1 where nik='".$_SESSION['nik']."'"));
  $lokasi=$dtl['kecamatan'].' - '.$dtl['desa'];
}

$atbl = new Atable();
$atbl->limit = 10;
$atbl->caption = "Data Penduduk ".$lokasi;
$atbl->query = "SELECT id, nkk, nik, nama, tgl_lahir, status_kawin, kecamatan, desa, rt, rw, tps, status FROM penduduk1";
$atbl->orderby = "nama ASC";
$atbl->where = "nik !='' $andwhere";
//$atbl->showsql = TRUE;

/*if($level==0){
  $atbl->where = "kecamatan='".$dtl['kecamatan']."' and desa='".$dtl['desa']."'";
}*/
$atbl->col = '["$nn;","$keluarga;"]';
$atbl->colv = '["",""]';
$atbl->colnumber = FALSE;
$atbl->style = 'table table-hover collapses';
//$atbl->datainfo = TRUE;
//$atbl->collist=TRUE;
//$atbl->xls=TRUE;
//$atbl->add=TRUE;
//$atbl->edit=TRUE;
//$atbl->delete=TRUE;

$atbl->param= 'extract(params($row));';


echo $atbl->load();

function params($row){
  //('.date('d-m-Y',strtotime($row->tgl_lahir)).')
  $dt['keluarga']="<u>Keluarga:</u><br>";
  $nkk=pg_query($GLOBALS['dpt'],"select nik,nama from penduduk1 where nkk='".$row->nkk."' and nik!='".$row->nik."'");
  while($kel=pg_fetch_object($nkk)){
    $dt['keluarga'].='&nbsp;<b>'.$kel->nama.'</b><br>&nbsp;&nbsp;&nbsp;<small>'.$kel->nik.'</small><br>';
  }

  if($row->status=='1'){
     $status='<span class="text-success glyphicon glyphicon-ok btn-right" aria-hidden="true"></span>';
  }else{
    $status='<form method="POST" action=""><button type="submit" value="'.$row->nkk.'" name="nkk" class="btn btn-primary btn-sm btn-right">Approve</button></form>';
  }
  $dt['nn'] = '<b>'.$row->nama.'</b><br><small>'.$row->nik.'</small>'.$status.'<a href="javascript:void(0)" class="more btn btn-default btn-sm btn-right" onclick="collapses(this)">Det</a>';

  return $dt;
}

?>

<script src="../dist/js/jquery-1.10.2.min.js"></script>
        <script src="../dist/js/jquery.chained.min.js"></script>
        <script>
            $("#desa").chained("#kecamatan");
            $("#tps").chained("#desa");
        </script>
<script>
var trbf='';
function collapses(me){
  var tr = $(me).parent().parent();
  if(trbf!==''){
    if(trbf.html()!=tr.html()){
      trbf.toggleClass('expanded');
      trbf=tr;
    }else{
      trbf='';
    }
  }else{
    trbf=tr;
  }
	tr.toggleClass('expanded');
	return false;
}
</script>

<?php
atable_init();
?>
<style>
*{
  font-size: 16px;
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
	display: none !important;
}

@media screen and (max-width: 520px) {
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

  $inputwarga = pg_query("update penduduk set status='$status', petugas='".$_SESSION['nik']."', tgl_approve='".date('Y-m-d H:i:s')."' where nkk='$nkk'");
  if($inputwarga){
    echo "<script>window.location='page.php?p=cari'</script>";
  }else{
    echo "<script>window.location='page.php?p=cari'</script>";
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
}//end Approve


$lokasi="";
$level=$_SESSION['level'];
if($level==0){
  $dtl=pg_fetch_array(pg_query("select * from penduduk where nik='".$_SESSION['nik']."'"));
  $lokasi=$dtl['kecamatan'].' - '.$dtl['desa'];
}

$atbl = new Atable();
$atbl->limit = 10;
$atbl->caption = "Data Penduduk ".$lokasi;
$atbl->query = "SELECT id, nkk, nik, nama, tgl_lahir, status_kawin, kecamatan, desa, rt, rw, status FROM penduduk";
$atbl->orderby = "nama ASC";
if($level==0){
  $atbl->where = "kecamatan='".$dtl['kecamatan']."' and desa='".$dtl['desa']."'";
}
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
  $nkk=pg_query($GLOBALS['dpt'],"select nik,nama from penduduk where nkk='".$row->nkk."' and nik!='".$row->nik."'");
  while($kel=pg_fetch_object($nkk)){
    $dt['keluarga'].='&nbsp;<b>'.$kel->nama.'</b><br>&nbsp;&nbsp;&nbsp;<small>'.$kel->nik.'</small><br>';
  }

  if($row->status=='1'){
     $status='<span class="text-success btn-right glyphicon glyphicon-ok" aria-hidden="true"></span>';
  }else{
    $status='<form method="POST" action=""><button type="submit" value="'.$row->nkk.'" name="nkk" class="btn btn-primary btn-sm btn-right">Approve</button></form>';
  }
  $dt['nn'] = '<b>'.$row->nama.'</b><br><small>'.$row->nik.'</small>'.$status.'<a href="javascript:void(0)" class="more btn btn-default btn-sm btn-right" onclick="collapses(this)">Det</a>';

  return $dt;
}

?>

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

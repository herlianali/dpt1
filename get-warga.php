<?php
require_once "koneksi.php";
//koneksi_dpt();
$qwarga = pg_query($dpt,"select * from penduduk where nik='".$_POST['nik']."'");
if(pg_num_rows($qwarga)==1){
	$penduduk = pg_fetch_array($qwarga);
	echo $penduduk['nama'].'|'.$penduduk['tgl_lahir'].'|'.$penduduk['kecamatan'].'|'.$penduduk['desa'].'|'.$penduduk['rt'].'|'.$penduduk['rw'];
}
//tutup_dpt();
?>

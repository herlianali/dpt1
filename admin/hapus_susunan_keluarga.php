<?php
include '../koneksi.php';

$id = $_GET['id'];
pg_query($dpt,"DELETE FROM penduduk1 WHERE id='$id'");
header("location:index.php?h=pemilih");
?>

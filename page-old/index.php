<div style="text-align:center">
  <h3>Data Statistik</h3><br><br>
<?php
$jmlmuh=pg_num_rows(pg_query("select nik from penduduk where status='1'"));
?>
  <b>JUMLAH</b><br>
  <h3><?php echo $jmlmuh;?></h3>
</div>

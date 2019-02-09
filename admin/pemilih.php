<?php
  atable_init();
  $atableb = new Atable();
  $atableb->limit = 5;
  $atableb->caption = "Data Pemilih";
  $atableb->query = "SELECT nkk, nik, nama, kecamatan, desa, rt, rw, status FROM penduduk1";
  $atableb->where = "status='1'";
  $atableb->orderby = "nkk asc";
  $atableb->col = '["nik", "nama", "kecamatan", "desa", "rt", "rw", "$action;"]';
  $atableb->colv = '["NIK", "NAMA", "KECAMATAN", "DESA", "RT", "RW", "DETAIL"]';
  $atableb->colnumber = TRUE;
  $atableb->collist=TRUE;
  $atableb->xls=TRUE;
  $atableb->param= 'extract(params($row));';
  echo $atableb->load();

  function params($row){
      $data['action'] ="<a href='#myModal' id='custId' class='btn btn-primary btn-xs' data-toggle='modal' data-id=".$row->nkk.">Detail</a>";
      return $data;
  }
?>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <form role="form" id="form-edit" method="post" action="?h=akun-lapangan">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title" id="myModalLabel">Susunan Keluarga</h3>
        </div>
        <div class="modal-body">
          <div class="fetched-data" style="text-align: justify; background-color: #fff; opacity:0.8;"></div>
        </div>
        <div class="modal-footer">
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
        //alert(rowid);
  			url : 'susunan-keluarga.php?rowid='+ rowid,
  			method : 'get',
  			success : function(data){
  				$('.fetched-data').html(data);
  			}
  		});
  	});
  });
</script>

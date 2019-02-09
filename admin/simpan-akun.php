<?php
include "../koneksi.php";
    if(isset($_GET['rowid'])){
    $id = $_GET['rowid'];
    $data = pg_fetch_array(pg_query($dpt,"select * from penduduk1 where nik ='$id'"));
?>

    <div class="form-group">
        <label>NIK</label>
        <input type="text" class="form-control" name="nik" value="<?php echo $data['nik'] ; ?>" readonly>
        <p style="color:red" id="error_edit_nama"></p>
    </div>
    <div class="form-group">
        <label>Nama</label>
        <input class="form-control" name="nama" value="<?php echo $data['nama'] ; ?>" readonly>
        <p style="color:red" id="error_edit_nama"></p>
    </div>
    <div class="form-group">
        <label>Kecamatan</label>
        <input class="form-control" name="nama" value="<?php echo $data['kecamatan'] ; ?>" readonly>
        <p style="color:red" id="error_edit_nama"></p>
    </div>
    <div class="form-group">
        <label>Desa</label>
        <input class="form-control" name="nama" value="<?php echo $data['desa'] ; ?>" readonly>
        <p style="color:red" id="error_edit_nama"></p>
    </div>
    <div class="form-group">
        <label>Username</label>
        <input type="text" class="form-control" value="<?php echo $data['nik'] ; ?>" name="username" readonly>

    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="text" class="form-control" name="password">
    </div>
<?php } ?>

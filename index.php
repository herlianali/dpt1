<?php
session_start();
if(isset($_SESSION['username'])){
	header('location:page.php');
	exit;
}
if(isset($_POST['login'])){
	require_once ("koneksi.php");
	function nosqli($user,$pass){
		$banlist = array (
			"insert", "select", "update", "delete", "distinct", "having", "truncate", "replace",
			"handler", "like", " as ", "or ", "procedure", "limit", "order by", "group by", "asc", "desc"
		);
		if(eregi("[a-zA-Z0-9]+", $user)) {
			$user = trim(str_replace($banlist, '', $user));
		}else{
			$user = NULL;
		}
		if(eregi("[a-zA-Z0-9]+", $pass)){
			$pass = trim(str_replace($banlist, '', $pass));
		}else{
			$pass = NULL;
		}
		$array = array( 'nik' => $user, 'password' => $pass );
		// ---------------------------------------------
		if(in_array(NULL,$array)){
			return false;
		}else{
			return $array;
		}
	}
	$username=pg_escape_string($_POST['username']);
	$password=$_POST['password'];

	$login=pg_query("SELECT * FROM akun_lapangan1 WHERE username='$username' AND password='$password'");
	$ketemu=pg_num_rows($login);

	if($ketemu == 1 ){
		$row = pg_fetch_array($login);
		session_start();
		$_SESSION['username']=$username;
		$_SESSION['nik']=$row['nik'];
		$_SESSION['level']=$row['level'];
		header("Location: admin");
	}else{
		echo '<br><br><div class="alert alert-danger">Upss...!!! Login gagal, Harap Masukkan Username dan Password yang benar.</div>';
	}
}

	/*if(isset($_POST['daftar'])){
		include "koneksi.php";
		$nik = $_POST['nik'];
		$status = '1';

		$cekdata = pg_num_rows(pg_query($dpt,"select * from penduduk where nik='$nik'"));
		if($cekdata==1){
			$input = pg_query($dpt,"update penduduk set status='$status' where nik='$nik'");
			if($input){
				session_start();
		    $_SESSION['usser']=$nik;
	      echo "<script>window.location='page.php?&pesan=1'</script>";
	    }else{
	      echo "<script>window.location='index.php?&pesan=2'</script>";
	    }
		}else{
			echo "<script>window.location='index.php?&pesan=3'</script>";
		}
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
		}elseif ($pesan==3) {
			echo "<div class='alert alert-danger' role='alert'>
			<strong><i class='ti-alert'></i> Gagal!</strong>
			Anda belum terdaftar di database Muhammadiyah
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			<span aria-hidden='true'>&times;</span>
			</button>
			</div>";
	}
}*/
?>
<!DOCTYPE html>
<head>
	<title>DPT J</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
	Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
	<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<link rel="stylesheet" href="admin/css/bootstrap.min.css" >
	<link href="admin/css/style.css" rel='stylesheet' type='text/css' />
	<link href="admin/css/style-responsive.css" rel="stylesheet"/>
	<link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="admin/css/font.css" type="text/css"/>
	<link href="admin/css/font-awesome.css" rel="stylesheet">
	<script src="admin/js/jquery2.0.3.min.js"></script>
</head>
	<body>
		<div class="log-w3" style='padding-bottom:180px;'>
			<div class="w3layouts-main">
				<h3 align="center"><img src="j.png" width="150px" height="150px"><br><br>LOGIN</h3>
				<form action="" method="POST">
					<input type="text" class="ggg" name="username" placeholder="Username">
					<input type="password" class="ggg" name="password" placeholder="Password">
					<!--<input type="text" class="ggg" name="nik" placeholder="Nik" id="nik" required onkeyup="getWarga(this,1)">
					<div id="data" style="display:none">
						<input type="text" class="ggg" name="nama" placeholder="Nama" id="nama" readonly>
						<input type="text" class="ggg" name="tgl_lahir" id="tgl_lahir" placeholder="Tgl Lahir" readonly>
						<input type="text" class="ggg" name="kecamatan" id="kecamatan" placeholder="Kecamatan" readonly>
						<input type="text" class="ggg" name="desa" id="desa" placeholder="Desa" readonly>
						<input type="text" class="ggg" name="rt" id="rt" placeholder="RT" readonly>
						<input type="text" class="ggg" name="rw" id="rw" placeholder="RW" readonly>
					</div>-->
						<div class="clearfix"></div>
						<button type="submit" value="login" name="login" class="btn btn-primary" style="width:100%;">Login</button>
				</form>
			</div>
		</div>
		<script src="admin/js/bootstrap.js"></script>
		<script src="admin/js/jquery.dcjqaccordion.2.7.js"></script>
		<script src="admin/js/scripts.js"></script>
		<script src="admin/js/jquery.slimscroll.js"></script>
		<script src="admin/js/jquery.nicescroll.js"></script>
		<script src="admin/js/jquery.scrollTo.js"></script>

		<script type="text/javascript">

		/*function getWarga(me,anggota){
		  $.post('get-warga.php',{nik:me.value},function(data){
		    if(data!=""){
		      var dtwarga = data.split('|');
		      document.getElementById('nama').value = dtwarga[0];
		      document.getElementById('tgl_lahir').value = dtwarga[1];
		      document.getElementById('kecamatan').value = dtwarga[2];
		      document.getElementById('desa').value = dtwarga[3];
		      document.getElementById('rt').value = dtwarga[4];
					document.getElementById('rw').value = dtwarga[5];
					$('#data').slideToggle( "slow" );
		    }else{
		      document.getElementById('nama'+anggota).value = "";
		      document.getElementById('tgl_lahir'+anggota).value = "";
		      document.getElementById('kecamatan'+anggota).value = "";
					document.getElementById('desa'+anggota).value = "";
		      document.getElementById('rt'+anggota).value = "";
		      document.getElementById('rw'+anggota).value = "";
					$('#data').slideToggle( "slow" );
		    }
		  });
		}*/
		</script>
	</body>
</html>

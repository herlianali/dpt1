<?php
if(isset($_POST['sess'])){
	session_start();
	echo (isset($_SESSION['progress'])?$_SESSION['progress']:"0")."%";
	exit;
}
if(isset($_POST['del'])){
	$id = $_POST['id'];
	$del = pg_query("delete from penduduk1 where id='$id'");
	if($del){
		echo 'true';
	}else{
		echo 'false';
	}
	exit;
}
if(isset($_FILES['file'])){
	if( 0 < $_FILES['file']['error']){
		echo 'Error: ' . $_FILES['file']['error'] . '<br>';
	}else{
		require "../lib/phpexcel/PHPExcel.php";
		require "../koneksi.php";

		$tmpfname = $_FILES["file"]["tmp_name"];
		$allowed_types = array ('image/png','image/jpeg');
		$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
		$detected_type = finfo_file($fileInfo, $tmpfname);
		//echo $detected_type;

		$tmpfname = $_FILES["file"]["tmp_name"];
		$filename = $_FILES["file"]["name"];
		$filenm=explode("_",$filename);
		$kecamatan=strtoupper($filenm[2]);
		$desa=substr(strtoupper($filenm[3]),0,-5);
		//echo $kecamatan.' _ '.$desa;

		$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
		$excelObj = $excelReader->load($tmpfname);

		$cols=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

		$worksheet = $excelObj->getSheet(2);
		$lastRow = $worksheet->getHighestRow();

		$rows=array("nkk","nik","nama","tgl_lahir","status_kawin","kecamatan","desa","rt","rw","tps");

		$penduduk=array();

		$gagal="";
		$sada="";
		$tempnik=array();
		
		for ($row = 2; $row <= $lastRow; $row++) {
			session_start();
			$penduduk["nkk"]=$worksheet->getCell("B".$row)->getValue();
			$penduduk["nik"]=$worksheet->getCell("C".$row)->getValue();
			$penduduk["nama"]=$worksheet->getCell("D".$row)->getValue();
			$penduduk["tgl_lahir"]=date('Y-m-d',strtotime(str_replace("|","-",$worksheet->getCell("F".$row)->getValue())));
			$penduduk["status_kawin"]=$worksheet->getCell("G".$row)->getValue();
			$penduduk["kecamatan"]=$kecamatan;
			$penduduk["desa"]=$desa;
			$penduduk["rt"]=$worksheet->getCell("J".$row)->getValue();
			$penduduk["rw"]=$worksheet->getCell("K".$row)->getValue();
			$penduduk["tps"]=$worksheet->getCell("O".$row)->getValue();
			/*for ($ncols = 2; $ncols <= 11; $ncols++) {
				if($cols[$ncols]=='H'){
					$penduduk[$rows[$ncols-2]]=$kecamatan;
				}else if($cols[$ncols]=='I'){
					$penduduk[$rows[$ncols-2]]=$desa;
				}else{
					$penduduk[$rows[$ncols-2]]=$worksheet->getCell($cols[$ncols].$row)->getValue();
				}
			}*/
			//echo '<pre>',print_r($penduduk),'</pre><br><br>';

			if(!in_array($penduduk['nik'],$tempnik)){
				array_push($tempnik,$penduduk['nik']);
			}else{
				$sada.=$penduduk['nik'].' - '.$penduduk['nama'].'<br>';
			}
			$ada=pg_num_rows(pg_query("select * from penduduk1 where nik='".$penduduk['nik']."'"));
			if($ada==0){
				$save=pg_query("insert into penduduk1 (nkk,nik,nama,tgl_lahir,status_kawin,kecamatan,desa,rt,rw,tps)
				values('".$penduduk['nkk']."', '".$penduduk['nik']."', '".trim(str_replace("'","''",$penduduk['nama']))."', '".$penduduk['tgl_lahir']."', '".$penduduk['status_kawin']."', '".$penduduk['kecamatan']."', '".$penduduk['desa']."', '".$penduduk['rt']."', '".$penduduk['rw']."', '".$penduduk['tps']."')");

				if(!$save){
					$gagal.=$penduduk['nik'].' - '.$penduduk['nama'].'<br>';
				}
			}else{
				$save=pg_query("update penduduk1 set
				nama='".trim(str_replace("'","''",$penduduk['nama']))."', tgl_lahir='".$penduduk['tgl_lahir']."', status_kawin='".$penduduk['status_kawin']."', kecamatan='".$penduduk['kecamatan']."',
				desa='".$penduduk['desa']."', rt='".$penduduk['rt']."', rw='".$penduduk['rw']."', tps='".$penduduk['tps']."'
				where nik='".$penduduk['nik']."'");

				if(!$save){
					$gagal.=$penduduk['nik'].' - '.$penduduk['nama'].'<br>';
				}
			}
			$_SESSION['progress'] = intval($row/$lastRow *100);
			session_write_close();
		}

		session_start();
		unset($_SESSION['progress']);
		if($save){
			echo 'true|'.$gagal.'|'.$kecamatan.' - '.$desa.'<br>'.$sada;
		}else{
			echo 'false|'.$gagal.'|'.$kecamatan.' - '.$desa.$sada;
		}
	}
	exit;
}

session_start();
unset($_SESSION['progress']);
?>

<style>
#formupload {
	top: 50%;
	left: 50%;
	width: 100%;
	height: 100px;
	border: 4px dashed #ccc;
	border-radius: 20px;
}
#formupload p {
	position: absolute;
	margin-top: 30px;
	margin-left: -20px;
	width: 100%;
	text-align: center;
	color: #aaa;
	font-size: 20px;
	font-family: Arial;
}
#formupload input {
	margin: 0;
	padding: 0;
	width: 100%;
	height: 100%;
	opacity: 0;
}

input[type=file] {
	display: block;
}
.atablewrap{overflow: auto;}
</style>
<?php atable_init();?>
</head>
<body>

<form id="formupload" action="" method="post">
	<p id="progress">Drag & Drop For Upload</p>
	<input name="file" type="file" onchange="upload(this);" multiple/>
</form>
<div id="datagagal"></div><div id="datadouble"></div>
<?php
$atable = new Atable();
$atable->limit = 5;
$atable->caption = "Data Penduduk";
//$atable->databases = 'pgsql';
$atable->query = "SELECT id,nkk,nik,nama,tgl_lahir,status_kawin,kecamatan,desa,rt,rw,tps,status FROM penduduk1";
$atable->orderby = "id desc";
$atable->col = '["nkk","nik","nama","tgl_lahir","status_kawin","kecamatan","desa","rt","rw","tps","status","$del;"]';

$atable->colv = '["NKK","NIK","NAMA","TANGGAL LAHIR","STATUS KAWIN","KECAMATAN","DESA","RT","RT","TPS","STATUS","DEL"]';

$atable->colnumber = TRUE;
$atable->param='extract(action($row));';

$atable->collist=TRUE;
$atable->xls=TRUE;

echo $atable->load();

function action($row){
	$data['del']='<button type="button" class="btn btn-danger" onclick="deletes('.$row->id.');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
	return $data;
}

?>

<script type="text/javascript">
var checkprog;
function upload(me){
	clearInterval(checkprog);
	me.disabled = true;
	var jmlfile = $(me).prop("files").length;
	for(var n=0;n<jmlfile;n++){
		var file_data = $(me).prop("files")[n];
		var form_data = new FormData();
		form_data.append("file", file_data);

		$.ajax({
		  xhr: function() {
		    var xhr = new window.XMLHttpRequest();
		    xhr.upload.addEventListener("progress", function(evt) {
		    	if(evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						percentComplete = parseInt(percentComplete * 100);
						//console.log(percentComplete);
						$("#progress").html("Upload "+percentComplete+"%");
						if (percentComplete === 100) {
							checkProgress();
							$("#progress").html("Process");
						}
		      }
		    }, false);
		    return xhr;
		  },
			url: 'upload-data.php',//window.location.pathname,
			dataType: "text",
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: "post",
			success: function(data){
				//console.log(data);
				me.disabled = false;
				clearInterval(checkprog);
				$(me).val("");
				if(data.includes("true")){
					$("#progress").html("Process Complete. Drag & Drop For Upload");
					atable_reload(0);
					console.log(data);
				}else{
					console.log(data);
					$("#progress").html("Process Failed. Drag & Drop For Upload");
				}

				var dt=data.split("|");
				if(dt[1]!=""){
					$('#datagagal').html('Gagal Upload Ke database<br>'+dt[1]);
				}
				if(dt[2]!=""){
					document.getElemetnById('datadouble').innerHTML+='Data Double<br>'+dt[2];
					//$('#datadouble').html('Data Double<br>'+dt[2]);
				}
				atable_reload(0);
			},
			fail: function(xhr, textStatus, errorThrown){
				clearInterval(checkprog);
				$("#progress").html("Process Failed. Drag & Drop For Upload");
			}
		});
	}
};

function deletes(no){
	var conf = confirm('Delete data ini?');
	if(conf){
		var form_data = new FormData();
		form_data.append("del", "delete");
		form_data.append("id", no);
		$.ajax({
			url: 'upload-data.php',//window.location.pathname,
			dataType: "text",
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: "post",
			success: function(data){
				console.log(data);
				if(data.includes("true")){
				}else{
					console.log(data);
				}
				atable_reload(0);
			}
		});
	}
}

function checkProgress(){
	checkprog=setInterval(function(){
		var form_data = new FormData();
		form_data.append("sess", "session");
		$.ajax({
			url: 'upload-data.php',//window.location.pathname,
			dataType: "text",
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: "post",
			success: function(data){
				//console.log(data);
				$("#progress").html("Process Database "+data);
				if(data=='100%'){
					clearInterval(checkprog);
					$("#progress").html("Process Complete. Drag & Drop For Upload");
				}
			}
		});
	}, 1000);
}
clearInterval(checkprog);
</script>

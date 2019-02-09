var loader = "<div id='aloader'><div id='loader'></div></div>";
(function($) {
	// fungsi dijalankan setelah seluruh dokumen ditampilkan
	$(window).load(function() {
		document.getElementById("content").innerHTML=loader;
	});
	$(document).ready(function(e) {
		// deklarasikan variabel
		var main = "login.php";
		var soal = "soal.php";
		var result = "test_result.php";

		$('#selesai').live("click", function(){
			var selesai = confirm("Apakah Anda yakin ingin menyelesaikan tes?");
			if(selesai){
				var maxsoal = document.getElementById("maxsoal").value;
				var soal=[];
				for(var i=1;i<=maxsoal;i++){
					soal[i]=$("input[name="+i+"]:checked").val();
				}
				document.getElementById("content").innerHTML=loader;
				$.post(result, {soal:soal,maxsoal:maxsoal} ,function(data) {
					$("#content").html(data);
				});
			}
		});

		$('#logout').live("click", function(){
			var alogout = confirm("Anda yakin akan keluar?\nTes akan di anggap gagal apabila Anda keluar saat mengerjakan.");
			if(alogout){
				$.post("soal.php", {h:0,m:0,s:0} ,function(data) {});
				$.post("logout.php", {username:"1" } ,function(data) {
					$("#content").load(main);
					document.getElementById("menu").innerHTML='';
				});
			}
		});

		$('#masuk').live("click", function(){
				var username = document.getElementById("username");
				var password = document.getElementById("password");
				username.style.border = "solid 1px #ccc";
				password.style.border = "solid 1px #ccc";
				if(username.value != "" && password.value != ""){
					document.getElementById("alert-info").innerHTML=loader;
					document.getElementById("login-panel").style.display = "none";
					$.post(main, {username: username.value,password:password.value} ,function(data) {
						if(data){
							$("#content").load(soal);
							document.getElementById("menu").innerHTML="<input type='button' id='logout' value='Keluar' style='background:#520301;padding:7px;border:none;'/>";
						}else{
							document.getElementById("login-panel").style.display = "block";
							document.getElementById("alert-info").innerHTML="<div class='alert alert-danger' role='alert' style='position:absolute;width:100%;text-align:center;'>Username dan Password Salah</div>";
						}
					});
				}else{
					if(username.value == "" && password.value == ""){
						document.getElementById("alert-info").innerHTML="<div class='alert alert-danger' role='alert' style='position:absolute;width:100%;text-align:center;'>Masukkan Username dan Password</div>";
						username.style.border = "solid 1px #f1a";
						password.style.border = "solid 1px #f1a";
					}else if(username.value == ""){
						document.getElementById("alert-info").innerHTML="<div class='alert alert-danger' role='alert' style='position:absolute;width:100%;text-align:center;'>Masukkan Username</div>";
						username.style.border = "solid 1px #f1a";
					}else if(password.value == ""){
						document.getElementById("alert-info").innerHTML="<div class='alert alert-danger' role='alert' style='position:absolute;width:100%;text-align:center;'>Masukkan Password</div>";
						password.style.border = "solid 1px #f1a";
					}
				}
		});

	});
}) (jQuery);



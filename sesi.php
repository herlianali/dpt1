<?php
session_start();
if(isset($_SESSION['username']) && $_SESSION['username']==true) {
} else {
	echo '<meta http-equiv="refresh" content="0; url=https://dpt1.umsida.ac.id/">';
	//header("location:../index.php");
	die();
}
?>

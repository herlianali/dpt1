<?php
include "../sesi.php";
include "../koneksi.php";
include "../lib/atable.php";
?>
<!DOCTYPE html>
<head>
  <title>DPT J</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
  Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
  <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
  <link rel="stylesheet" href="css/bootstrap.min.css" >
  <link href="css/style.css" rel='stylesheet' type='text/css' />
  <link href="css/style-responsive.css" rel="stylesheet"/>
  <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
  <link href="css/font-awesome.css" rel="stylesheet">
  <script src="js/jquery2.0.3.min.js"></script>
</head>
<body>
  <section id="container">
    <?php
      include "header.php";
      include "left-menu.php";
    ?>
  <section id="main-content">
    <?php
      isset($_GET['h']) ? $modul = $_GET['h'] : $modul = "";
      include "konten.php";
      include "footer.php";
    ?>
</section>
</section>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/scripts.js"></script>
<script src="js/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.scrollTo.js"></script>
<!-- calendar -->
	<script type="text/javascript" src="js/monthly.js"></script>
	<!-- //calendar -->
</body>
</html>

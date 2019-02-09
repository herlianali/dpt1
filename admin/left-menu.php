<?php
  isset($_GET['h']) ? $modul = $_GET['h'] : $modul = "";
?>
<aside>
  <div id="sidebar" class="nav-collapse">
    <div class="leftside-navigation">
      <ul class="sidebar-menu" id="nav-accordion">
        <li>
          <a <?php echo $modul=="dashboard"?'class="active"':'';?> href="?h=dashboard">
            <i class="fa fa-dashboard"></i>
            <span>Dashboard</span>
          </a>
        </li>

      <?php if($_SESSION['level']=='1'){?>
        <li class="sub-menu">
          <a <?php echo $modul=="muhammadiyah" || $modul=="non-muhammadiyah"?'class="active"':'';?> href="javascript:;">
            <i class="fa fa-book"></i>
            <span>Data</span>
          </a>
          <ul class="sub">
            <li><a <?php echo $modul=="muhammadiyah"?'class="active"':'';?> href="?h=pemilih">Pemilih</a></li>
            <li><a <?php echo $modul=="dashboard"?'class="active"':'';?> href="?h=non-pemilih">Non Pemilih</a></li>
          </ul>
        </li>
        <!--<li>
          <a <?php //echo $modul=="akun-lapangan"?'class="active"':'';?> href="?h=akun-lapangan">
            <i class="fa fa-user"></i>
            <span>Akun Lapangan</span>
          </a>
        </li>-->
        <li>
          <a <?php echo $modul=="upload-data"?'class="active"':'';?> href="?h=upload-data">
            <i class="fa fa-upload"></i>
            <span>Upload Data</span>
          </a>
        </li>
      <?php }?>
        <li>
          <a <?php echo $modul=="cari"?'class="active"':'';?> href="?h=cari">
            <i class="fa fa-user"></i>
            <span>Verifikasi Warga</span>
          </a>
        </li>
        <li>
        </li>
        <li>
          <a href="../logout.php">
            <i class="fa fa-power-off"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</aside>
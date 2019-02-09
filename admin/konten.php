<section class="wrapper">
  <div class="row">
    <div class="panel-body">
      <div class="col-md-12 w3ls-graph">
        <div class="agileinfo-grap">
          <div class="agileits-box">
            <header class="agileits-box-header clearfix">
              <div class="toolbar">
              <?php
              switch($modul) {
              	case "data-warga":
                case "dashboard":
                case "akun-lapangan":
                case "pemilih":
                case "non-pemilih":
                case "upload-data":
                case "cari":
              		require $modul.".php";
              		break;
                default:
              		require "dashboard.php";
              		break;
              }
              ?>
            </div>
          </header>
        <div class="agileits-box-body clearfix">
      </div>
    </div>
  </div>
</div>
</div>
</div>
</section>


    <div>
      <table class="table" ui-jq="footable" ui-options='{
        "paging": {
          "enabled": true
        },
        "filtering": {
          "enabled": true
        },
        "sorting": {
          "enabled": true
        }}'>
        <thead>
          <tr>
            <th>NO</th>
            <th>NIK</th>
            <th>NAMA</th>
            <th>KECAMATAN</th>
            <th>DESA</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if(isset($_GET['rowid'])){
            include "../koneksi.php";
            $id = $_GET['rowid'];
            $no = 1;
            //echo "select * from penduduk where nkk='$id' order by tgl_lahir asc";
            $data = pg_query($dpt,"select * from penduduk1 where nkk='$id' order by tgl_lahir asc");
            while($row = pg_fetch_array($data)){
              echo "<tr><td>".$no++."</td>
              <td>".$row['nik']."</td>
              <td>".$row['nama']."</td>
              <td>".$row['kecamatan']."</td>
              <td>".$row['desa']."</td>
              </tr>";
            }
          }
          ?>
        </tbody>
      </table>
    </div>

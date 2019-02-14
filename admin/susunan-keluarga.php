
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
            <th>Aksi</th>

          </tr>
        </thead>
        <tbody>
          <?php
          // if(isset($_GET['rowid'])){
          //   include "../koneksi.php";
          //   $id = $_GET['rowid'];
          //   $no = 1;
          //   //echo "select * from penduduk where nkk='$id' order by tgl_lahir asc";
          //   $data = pg_query($dpt,"select * from penduduk1 where nkk='$id' order by tgl_lahir asc");
          //   while($row = pg_fetch_array($data)){
          //     echo "<tr><td>".$no++."</td>
          //     <td>".$row['nik']."</td>
          //     <td>".$row['nama']."</td>
          //     <td>".$row['kecamatan']."</td>
          //     <td>".$row['desa']."</td>
          //     <td>"<button>Hapus</button>"</td>
          //     </tr>";
          //   }
          // }
          ?>
          <?php
          if (isset($_GET['rowid'])) {
            include "../koneksi.php";
            $id = $_GET['rowid'];
            $no = 1;
            $data = pg_query($dpt,"select * from penduduk1 where nkk='$id' order by tgl_lahir asc");
            while ($row = pg_fetch_array($data)) {
            ?><tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['nik']; ?></td>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo $row['kecamatan']; ?></td>
            <td><?php echo $row['desa']; ?></td>
            <td><a type="button" href="hapus_susunan_keluarga.php?id=<?php echo $row['id'] ?>" class="btn btn-primary">hapus</a></td>
            </tr>
            <?php
            }
          }
          ?>
        </tbody>
      </table>
    </div>

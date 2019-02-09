<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div id="xxx" style="min-width: 300px; height: 350px; margin: 0 auto"></div>

<script>

Highcharts.chart('xxx', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Jumlah DPT Muhammadiyah Kab. Sidoarjo'
    },
    subtitle: {
        text: 'Source: <a href="#">KPU Muhammadiyah</a>'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'Jumlah Pemilih: <b>{point.y:f} orang</b>'
    },
    series: [{
        name: 'Population',
        data: [
              <?php
              $jumdpt = pg_query("select count(nik) as jumlah,kecamatan from penduduk1 where status = '1' group by kecamatan");
              while($dptx = pg_fetch_array($jumdpt)){
            echo "['".$dptx['kecamatan']."', ".$dptx['jumlah']."],";
          }
          ?>
        ],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y:f}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});
</script>

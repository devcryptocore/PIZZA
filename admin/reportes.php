<?php
    include('../includes/header.php');
    $inicio = $_GET['inicio'] ?? '';
    $fin    = $_GET['fin'] ?? '';
    if ($inicio && $fin) {
        $finCompleto = $fin . ' 23:59:59';
        $whereVentas = "WHERE fechareg BETWEEN '$inicio' AND '$finCompleto'";
        $whereMov    = "WHERE fecha BETWEEN '$inicio' AND '$finCompleto'";
        $tituloRango = "Reporte desde $inicio hasta $fin";
    } else {
        $whereVentas = "WHERE MONTH(fechareg) = MONTH(CURDATE()) AND YEAR(fechareg) = YEAR(CURDATE())";
        $whereMov    = "WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
        $tituloRango = "Reporte del mes en curso";
    }
    $qVentas = $con->query("SELECT COALESCE(SUM(total),0) AS total_ventas, COALESCE(SUM(descuento),0) AS total_descuentos FROM ventas $whereVentas");
    $ventas = $qVentas->fetch_assoc();
    $qEgresos = $con->query("SELECT COALESCE(SUM(valor),0) AS total_egresos FROM movimientos $whereMov AND tipo='egreso'");
    $egresos = $qEgresos->fetch_assoc();
    $qAbonos = $con->query("SELECT COALESCE(SUM(valor),0) AS total_abonos FROM movimientos $whereMov AND tipo='abono'");
    $abonos = $qAbonos->fetch_assoc();
    $qMovs = $con->query("SELECT tipo, concepto, entidad, valor, fecha FROM movimientos $whereMov ORDER BY fecha DESC");
    $ganancia = ($ventas['total_ventas'] - $egresos['total_egresos'] - $abonos['total_abonos']);
    $ventasDia = [];
    $egresosDia = [];
    $labels = [];
    $qVD = $con->query("SELECT DATE(fechareg) AS dia, SUM(total) AS total
                        FROM ventas $whereVentas
                        GROUP BY dia ORDER BY dia ASC");
    while($v = $qVD->fetch_assoc()){
        $labels[] = $v['dia'];
        $ventasDia[$v['dia']] = $v['total'];
    }
    $qED = $con->query("SELECT DATE(fecha) AS dia, SUM(valor) AS total
                        FROM movimientos $whereMov AND tipo='egreso'
                        GROUP BY dia ORDER BY dia ASC");
    while($e = $qED->fetch_assoc()){
        $egresosDia[$e['dia']] = $e['total'];
    }
    $fechas = array_unique(array_merge(array_keys($ventasDia), array_keys($egresosDia)));
    sort($fechas);
    $valVentas = [];
    $valEgresos = [];
    foreach($fechas as $f){
        $valVentas[]  = $ventasDia[$f]  ?? 0;
        $valEgresos[] = $egresosDia[$f] ?? 0;
    }
?>
<link rel="stylesheet" href="../css/reportes.css?<?=$version;?>">
<body>
<div class="container">
    <h2>Reporte Financiero - <?php echo $tituloRango; ?></h2>
    <form method="get">
        <input class="search-bar-date" type="date" name="inicio" value="<?php echo $inicio; ?>">
        <input class="search-bar-date" type="date" name="fin" value="<?php echo $fin; ?>">
        <button type="submit">Consultar</button>
        <a href="reportes.php" style="margin-left:10px;color:#007bff;text-decoration:none;">Ver mes actual</a>
    </form>

    <div class="summary">
        <div class="card">
            <h3>Ventas Totales</h3>
            <p>$<?php echo number_format($ventas['total_ventas']); ?></p>
        </div>
        <div class="card" style="background:#17a2b8;">
            <h3>Descuentos</h3>
            <p>$<?php echo number_format($ventas['total_descuentos']); ?></p>
        </div>
        <div class="card" style="background:#ffc107;color:#333;">
            <h3>Gastos / Egresos</h3>
            <p>$<?php echo number_format($egresos['total_egresos']); ?></p>
        </div>
        <div class="card" style="background:#28a745;">
            <h3>Ganancia Estimada</h3>
            <p>$<?php echo number_format($ganancia); ?></p>
        </div>
        <div class="card" style="background:#6f42c1;">
            <h3>Abonos a Deudas</h3>
            <p>$<?php echo number_format($abonos['total_abonos']); ?></p>
        </div>
    </div>
    <div class="charts">
        <div class="chart-box">
            <canvas id="chartResumen"></canvas>
        </div>
        <div class="chart-box">
            <canvas id="chartEvolucion"></canvas>
        </div>
    </div>
    <div class="export">
        <button onclick="window.print()">Exportar a PDF</button>
    </div>
    <h3 style="margin-top:30px;">Movimientos Financieros</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Entidad</th>
                    <th>Valor</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            <?php while($m = $qMovs->fetch_assoc()): ?>
                <tr>
                    <td><?php echo ucfirst($m['tipo']); ?></td>
                    <td><?php echo $m['concepto']; ?></td>
                    <td><?php echo ucfirst($m['entidad']); ?></td>
                    <td>$<?php echo number_format($m['valor']); ?></td>
                    <td><?php echo date("d-m-Y H:i", strtotime($m['fecha'])); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
new Chart(document.getElementById('chartResumen').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Ventas', 'Descuentos', 'Egresos', 'Ganancia', 'Abonos'],
        datasets: [{
            label: 'Resumen financiero',
            data: [
                <?php echo $ventas['total_ventas']; ?>,
                <?php echo $ventas['total_descuentos']; ?>,
                <?php echo $egresos['total_egresos']; ?>,
                <?php echo $ganancia; ?>,
                <?php echo $abonos['total_abonos']; ?>
            ],
            backgroundColor: ['#007bff', '#17a2b8', '#ffc107', '#28a745', '#6f42c1']
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false }, title: { display: true, text: 'Resumen financiero global' } } }
});

new Chart(document.getElementById('chartEvolucion').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($fechas); ?>,
        datasets: [
            {
                label: 'Ventas',
                data: <?php echo json_encode($valVentas); ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.1)',
                tension: 0.3
            },
            {
                label: 'Egresos',
                data: <?php echo json_encode($valEgresos); ?>,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.1)',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Evolución diaria de Ventas y Egresos' },
            legend: { position: 'bottom' }
        },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
</body>
</html>

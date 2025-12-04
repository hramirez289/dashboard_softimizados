<?php

include('database.php');        
include('consultasql.php');
?>



<div class="main-container">
  <?php include('includes/slidebar.php'); ?>
  <div class="content">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/stylegraf.css">

<!-- filtro de fecha-->
<header class="filter-bar">
  <div class="filter-item">
    <label for="start">Inicio ordenes de servicio</label>
    <input id="start" type="date" aria-label="Fecha inicio" value="<?php echo date('Y-m-01'); ?>">
  </div>
  <div class="filter-item">
    <label for="end">Término ordenes de servicio</label>
    <input id="end" type="date" aria-label="Fecha término" value="<?php echo date('Y-m-t'); ?>">
  </div>

  <div class="filter-item">
    <label for="empresaFilter">Filtrar por Empresa</label>
    <select id="empresaFilter" class="form-select" style="padding: 5px;">
      <option value="">Todas las empresas</option>
      <?php foreach ($listaEmpresas as $emp): ?>
        <option value="<?php echo htmlspecialchars($emp); ?>"><?php echo htmlspecialchars($emp); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <button id="apply" class="btn">Aplicar</button>
</header>

    <!-- tarjetas metricas-->

    <!-- tarjeta margen-->
    <div class="container-tarjetas">
      <div class="row">
        <div class="col metric-neto">
          <div class="card-body"> 
            <h5 class="card-title text-muted">Total venta neto</h5>
            <h2 class="fw-bold" id="totalOrdenServicio">$<?php echo number_format($totalordenservicio, 0, ',', '.'); ?></h2>
          </div>
        </div>

        <!-- tarjeta compras-->
        <div class="col metric-compras"> 
          <div class="card-body">
            <h5 class="card-title text-muted">Gastos compras</h5>
            <h2 class="fw-bold" id="totalCompras">$<?php echo number_format($totalcomprasmes, 0, ',', '.'); ?></h2>
          </div>
        </div>

        <!-- tarjeta venta neto-->
        <div class="col metric-margen">
          <div class="card-body">
            <h5 class="card-title text-muted">Margen</h5>
            <h2 class="fw-bold" id="totalVentas">$<?php echo number_format($margentotal, 0, ',', '.'); ?></h2>
          </div>
        </div>
      </div>
    </div>


    <!--primera tarjeta graficos-->

    <div class="graficos-dashboard">
      <div class="graficos-content">
        <h3 class="mb-3">Estado de órdenes de servicio</h3>
        <div class="graficos-wrapper">
          <div class="columna-izquierda">
            <canvas id="myChart4"></canvas>
          </div>
          
          <div class="columna-derecha">
            <div class="grafico-pequeno">
              <canvas id="myChart"></canvas>
            </div>
            <div class="grafico-pequeno">
              <canvas id="myChart2"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

<script src="assets/js/graficos.js"></script>

<script>
      document.addEventListener('DOMContentLoaded', function() {
        crearGraficos({
          estado_ot_a: <?php echo (int)$estado_ot_a; ?>,
          estado_ot_c: <?php echo (int)$estado_ot_c; ?>,
          confec: <?php echo (int)$confec; ?>,
          prev: <?php echo (int)$prev; ?>,
          corr: <?php echo (int)$corr; ?>,
          otros: <?php echo (int)$otros; ?>,
          prevcg: <?php echo (int)$prevcg; ?>,
          conCotizacion: <?php echo (int)($conCotizacion ?? 0); ?>,
          sinCotizacion: <?php echo (int)($sinCotizacion ?? 0); ?>,
        });
      });

      document.getElementById('apply').addEventListener('click', function() {
        const inicio = document.getElementById('start').value;
        const fin = document.getElementById('end').value;
        const empresa = document.getElementById('empresaFilter').value;
  
    if (fin < inicio) {
    alert('La fecha final debe ser igual o superior a la fecha inicial');
    return;
  }

  if (!inicio || !fin) {
    alert('Por favor selecciona ambas fechas');
    return;
  }


  
  fetch(`obtener_dashboard.php?inicio=${inicio}&fin=${fin}&empresa=${encodeURIComponent(empresa)}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        alert('Error: ' + data.error);
        return;
      } 

      if (empresa !== "") {
            document.getElementById('totalVentas').textContent = "---"; 
            document.getElementById('totalCompras').textContent = "---";
            document.getElementById('totalOrdenServicio').textContent = '$' + data.totalordenservicio_formateado;

        } else {
            document.getElementById('totalVentas').textContent = '$' + data.margentotal;
            document.getElementById('totalCompras').textContent = '$' + data.totalcomprasmes_formateado;
            document.getElementById('totalOrdenServicio').textContent = '$' + data.totalordenservicio_formateado;
        }

      // Actualizar gráficos
      crearGraficos(data);
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al obtener los datos');
    });
});
    </script>
  </div>
</div>






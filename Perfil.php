<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil del Niño</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
  <?php include 'menu.php'; ?>
<div class="container mt-4">
  <div class="row">
    <!-- Columna Izquierda -->
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header bg-primary text-white">Perfil del Niño</div>
        <div class="card-body">
          <p><strong>Nombre:</strong> Juanito Pérez</p>
          <p><strong>Edad:</strong> 6 años</p>
          <p><strong>Programa:</strong> Intervención Temprana</p>
          <p><strong>Tx:</strong> Lenguaje</p>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-info text-white">Próxima Sesión</div>
        <div class="card-body">
          <p><strong>Fecha:</strong> 10/07/2025</p>
          <p><strong>Hora:</strong> 10:30 AM</p>
          <p><strong>Psicóloga:</strong> Lic. Ana Ramírez</p>
          <p><strong>Observaciones:</strong> Revisión de avances</p>
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalValoracion">Valorar sesión</button>
        </div>
      </div>
    </div>

    <!-- Columna Derecha -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-warning">Desempeño por Evaluación</div>
        <div class="card-body">
          <canvas id="graficoRadar"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráfica de Línea -->
  <div class="card my-4">
    <div class="card-header bg-secondary text-white">Evolución por sesión</div>
    <div class="card-body">
      <canvas id="graficoLinea"></canvas>
    </div>
  </div>

<!-- Evaluaciones aplicadas -->
<div class="card mb-4">
  <div class="card-header bg-light">
    <strong>Evaluaciones aplicadas</strong>
  </div>
  <div class="card-body d-flex flex-wrap gap-4">
    <!-- Evaluación 1 -->
    <div class="text-center" style="width: 100px;">
      <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" width="64" alt="Archivo">
      <p class="mt-2 mb-0" style="font-size: 14px;">ESDM<br><small>10/06/2025</small></p>
    </div>
    <!-- Evaluación 2 -->
    <div class="text-center" style="width: 100px;">
      <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" width="64" alt="Archivo">
      <p class="mt-2 mb-0" style="font-size: 14px;">TEA Inicial<br><small>12/04/2025</small></p>
    </div>
    <!-- Evaluación 3 -->
    <div class="text-center" style="width: 100px;">
      <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" width="64" alt="Archivo">
      <p class="mt-2 mb-0" style="font-size: 14px;">Conductual<br><small>15/02/2025</small></p>
    </div>
  </div>

    <div class="card-footer text-muted">
      <a href="NewExam.php">
        <button class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg"></i> Agregar nueva evaluación
        </button>
      <small>Última actualización: 03/07/2025</small>
    </div>

</div>
  <!-- Historial de sesiones -->
  <div class="card">
    <div class="card-header bg-dark text-white">Historial de Sesiones</div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead class="table-light">
          <tr>
            <th>Fecha</th>
            <th>Psicóloga</th>
            <th>Participación</th>
            <th>Atención</th>
            <th>Tarea</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <!-- Simula 10 sesiones -->
          <tr><td>03/07/2025</td><td>Ana Ramírez</td><td>8</td><td>7</td><td>6</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>26/06/2025</td><td>Ana Ramírez</td><td>7</td><td>8</td><td>7</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>19/06/2025</td><td>Ana Ramírez</td><td>6</td><td>6</td><td>5</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>12/06/2025</td><td>Ana Ramírez</td><td>9</td><td>8</td><td>7</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>05/06/2025</td><td>Ana Ramírez</td><td>5</td><td>4</td><td>6</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>29/05/2025</td><td>Ana Ramírez</td><td>8</td><td>7</td><td>6</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>22/05/2025</td><td>Ana Ramírez</td><td>7</td><td>6</td><td>5</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>15/05/2025</td><td>Ana Ramírez</td><td>6</td><td>5</td><td>4</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>08/05/2025</td><td>Ana Ramírez</td><td>9</td><td>9</td><td>8</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
          <tr><td>01/05/2025</td><td>Ana Ramírez</td><td>6</td><td>7</td><td>6</td><td><button class="btn btn-sm btn-info">Ver</button></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Valoración -->
<div class="modal fade" id="modalValoracion" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Valorar Sesión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <label>Participación</label>
            <input type="number" class="form-control" min="1" max="10">
          </div>
          <div class="col-md-4">
            <label>Atención</label>
            <input type="number" class="form-control" min="1" max="10">
          </div>
          <div class="col-md-4">
            <label>Tarea en casa</label>
            <input type="number" class="form-control" min="1" max="10">
          </div>
        </div>
        <hr>
        <div class="mb-2">
          <label>Actividades realizadas</label>
          <select class="form-select mb-2">
            <option>Juego de memoria</option>
            <option>Pintura guiada</option>
            <option>Cuento participativo</option>
          </select>
          <select class="form-select mb-2">
            <option>Construcción con bloques</option>
            <option>Ejercicio de respiración</option>
            <option>Secuencia auditiva</option>
          </select>
        </div>
        <label>Observaciones</label>
        <textarea class="form-control" rows="3"></textarea>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar valoración</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts de Chart.js -->
<script>
  // Radar
  new Chart(document.getElementById('graficoRadar'), {
    type: 'radar',
    data: {
      labels: ['Lenguaje', 'Motricidad', 'Atención', 'Memoria', 'Social'],
      datasets: [
        {
          label: 'Inicial',
          data: [3, 2, 4, 3, 2],
          borderColor: 'rgba(0, 123, 255, 0.7)',
          fill: true
        },
        {
          label: 'Intermedia',
          data: [4, 3, 5, 3, 3],
          borderColor: 'rgba(255, 193, 7, 0.7)',
          fill: true
        },
        {
          label: 'Final',
          data: [5, 4, 6, 4, 4],
          borderColor: 'rgba(40, 167, 69, 0.7)',
          fill: true
        }
      ]
    }
  });

  // Línea: evolución de evaluación por sesión
  new Chart(document.getElementById('graficoLinea'), {
    type: 'line',
    data: {
      labels: ['01 May', '08 May', '15 May', '22 May', '29 May', '05 Jun', '12 Jun', '19 Jun', '26 Jun', '03 Jul'],
      datasets: [
        {
          label: 'Participación',
          data: [6, 9, 6, 7, 8, 5, 9, 6, 7, 8],
          borderColor: '#0d6efd',
          tension: 0.4
        },
        {
          label: 'Atención',
          data: [7, 9, 5, 6, 7, 4, 8, 6, 8, 7],
          borderColor: '#6610f2',
          tension: 0.4
        },
        {
          label: 'Tarea en casa',
          data: [6, 8, 4, 5, 6, 6, 7, 5, 7, 6],
          borderColor: '#dc3545',
          tension: 0.4
        }
      ]
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

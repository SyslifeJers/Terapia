<!DOCTYPE html>
<html lang="es"> 
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel Principal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <!-- Navbar -->
  <?php include 'menu.php'; ?>
  <!-- Contenido principal -->
  <div class="container mt-4">
    <h1 class="mb-4">Panel de Control</h1>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="card border-primary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">👦 Pacientes</h5>
            <p class="card-text">Ver todos los niños registrados.</p>
            <a href="Pacientes.php" class="btn btn-primary">Ir</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-info shadow-sm">
          <div class="card-body">
            <h5 class="card-title">📊 Evaluaciones</h5>
            <p class="card-text">Revisar y comparar resultados.</p>
            <a href="Modulos.php" class="btn btn-info">Ir</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-2">
      <div class="col-md-4">
        <div class="card border-warning shadow-sm">
          <div class="card-body">
            <h5 class="card-title">📚 Actividades</h5>
            <p class="card-text">Consulta y asigna actividades.</p>
            <a href="#" class="btn btn-warning">Ir</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-secondary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">📄 Encuestas</h5>
            <p class="card-text">Sube o aplica encuestas.</p>
            <a href="ModuloEncuesta.php" class="btn btn-secondary">Ir</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

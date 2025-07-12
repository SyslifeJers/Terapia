<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulos de Evaluación</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .modulo {
      text-align: center;
      padding: 20px;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      transition: all 0.2s ease;
      background-color: #f8f9fa;
    }
    .modulo:hover {
      background-color: #e9ecef;
      transform: scale(1.02);
    }
    .modulo i {
      font-size: 2.5rem;
      color: #0d6efd;
    }
    .modulo-title {
      margin-top: 10px;
      font-size: 1rem;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <?php include 'menu.php'; ?>
  <div class="container py-5">
    <h2 class="mb-4">📁 Módulos de Evaluación Clínica</h2>
<a href="ModuloEncuesta.php" class="btn btn-success">
  <i class="bi bi-plus-circle me-1"></i> Nuevo Registro
</a>
<hr>
    <div class="row g-4">

      <!-- Lenguaje -->
      <div class="col-6 col-md-3">
        <div class="modulo">
          <i class="bi bi-folder"></i>
          <div class="modulo-title">Lenguaje</div>
        </div>
      </div>

      <!-- Psicología -->
      <div class="col-6 col-md-3">
        <div class="modulo">
          <i class="bi bi-folder2-open"></i>
          <div class="modulo-title">Psicología</div>
        </div>
      </div>

      <!-- Fisioterapia -->
      <div class="col-6 col-md-3">
        <div class="modulo">
          <i class="bi bi-hospital"></i>
          <div class="modulo-title">Fisioterapia</div>
        </div>
      </div>

      <!-- Motricidad -->
      <div class="col-6 col-md-3">
        <div class="modulo">
          <i class="bi bi-bicycle"></i>
          <div class="modulo-title">Motricidad</div>
        </div>
      </div>

      <!-- Terapia Ocupacional -->
      <div class="col-6 col-md-3">
        <div class="modulo">
          <i class="bi bi-clipboard-check"></i>
          <div class="modulo-title">Terapia Ocupacional</div>
        </div>
      </div>

      <!-- Educación Especial -->
      <div class="col-6 col-md-3">
        <div class="modulo">
          <i class="bi bi-person-badge"></i>
          <div class="modulo-title">Educación Especial</div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

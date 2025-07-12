<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Exámenes Disponibles</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <?php include 'menu.php'; ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>📄 Exámenes disponibles para aplicar</h3>
  </div>

  <!-- Tabla de exámenes -->
  <div class="table-responsive">
    <table class="table table-bordered align-middle table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Nombre del Examen</th>
          <th>Área</th>
          <th>Sección</th>
          <th>Versión</th>
          <th>Aplicar</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>ESDM - Nivel 2</td>
          <td>Lenguaje</td>
          <td>Sección A</td>
          <td>2023</td>
          <td><a href="examen.php" class="btn btn-success btn-sm">Aplicar</a></td>
        </tr>
        <tr>
          <td>2</td>
          <td>CBCL - Comportamiento</td>
          <td>Psicológica</td>
          <td>Sección 1</td>
          <td>2021</td>
          <td><a href="#" class="btn btn-success btn-sm">Aplicar</a></td>
        </tr>
        <tr>
          <td>3</td>
          <td>Perfil Sensorial Infantil</td>
          <td>Física / Sensorial</td>
          <td>Completo</td>
          <td>2022</td>
          <td><a href="#" class="btn btn-success btn-sm">Aplicar</a></td>
        </tr>
        <tr>
          <td>4</td>
          <td>Evaluación de Motricidad Fina</td>
          <td>Motricidad</td>
          <td>Sección B</td>
          <td>2023</td>
          <td><a href="#" class="btn btn-success btn-sm">Aplicar</a></td>
        </tr>
        <!-- Agrega más exámenes aquí -->
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

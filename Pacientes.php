<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Pacientes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <?php include 'menu.php'; ?>

  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>👦 Lista de Pacientes</h2>
    
    </div>

    <!-- Tabla de pacientes -->
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Programa</th>
            <th>Tx</th>
            <th>Responsable</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Ana Sofía García</td>
            <td>6</td>
            <td>Lenguaje</td>
            <td>Tx-001</td>
            <td>Mamá - Laura</td>
            <td>
              <a href="Perfil.php?id=1" class="btn btn-sm btn-success">Ver</a>
              <a href="#" class="btn btn-sm btn-warning">Editar</a>
              <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Juan Pablo Ramírez</td>
            <td>5</td>
            <td>Motricidad</td>
            <td>Tx-002</td>
            <td>Papá - Miguel</td>
            <td>
              <a href="#" class="btn btn-sm btn-success">Ver</a>
              <a href="#" class="btn btn-sm btn-warning">Editar</a>
              <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
            </td>
          </tr>
          <!-- Más pacientes aquí -->
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

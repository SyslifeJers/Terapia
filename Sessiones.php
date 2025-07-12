
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Próximas Sesiones</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

  <?php include 'menu.php'; ?>

  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0">📅 Próximas Sesiones</h2>
      <button class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Agendar nueva
      </button>
    </div>

    <!-- Tabla de sesiones -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Niño(a)</th>
            <th>Terapeuta</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Área</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Ana Sofía García</td>
            <td>Lic. Carmen Ruiz</td>
            <td>08/07/2025</td>
            <td>10:00 AM</td>
            <td>Lenguaje</td>
            <td>
              <a href="Perfil.php" class="btn btn-sm btn-success">Perfil</a>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Juan Pablo Ramírez</td>
            <td>Psic. Roberto Díaz</td>
            <td>08/07/2025</td>
            <td>11:30 AM</td>
            <td>Psicología</td>
            <td>
              <a href="Perfil.php" class="btn btn-sm btn-success">Perfil</a>

            </td>
          </tr>
          <!-- Más filas aquí -->
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

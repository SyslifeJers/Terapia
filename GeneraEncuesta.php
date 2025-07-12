
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Aplicar Encuesta - ESDM Nivel 2</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <!-- Navbar -->
  <?php include 'menu.php'; ?>

  <div class="container">
    <h2 class="mb-4">📄 Encuesta: ESDM Nivel 2</h2>

    <form id="formEncuesta">

      <!-- Pregunta 1 -->
      <div class="card mb-3">
        <div class="card-header bg-light">
          <strong>1. Sigue las instrucciones “para” o “espera” sin ayudas ni gestos.</strong>
        </div>
        <div class="card-body row">
          <div class="col-md-3">
            <label class="form-label">Observado</label><br>
            <input type="radio" name="p1_obs" value="1"> Sí
            <input type="radio" name="p1_obs" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Informado por padres</label><br>
            <input type="radio" name="p1_padres" value="1"> Sí
            <input type="radio" name="p1_padres" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Informado por otros</label><br>
            <input type="radio" name="p1_otros" value="1"> Sí
            <input type="radio" name="p1_otros" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Código</label>
            <select name="p1_codigo" class="form-select">
              <option value="">Seleccionar</option>
              <option value="1">Adquirido</option>
              <option value="2">Emergente</option>
              <option value="3">No observado</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Pregunta 2 -->
      <div class="card mb-3">
        <div class="card-header bg-light">
          <strong>2. Identifica señalando o mostrando varias partes del cuerpo en sí mismo y otra persona.</strong>
        </div>
        <div class="card-body row">
          <div class="col-md-3">
            <label class="form-label">Observado</label><br>
            <input type="radio" name="p2_obs" value="1"> Sí
            <input type="radio" name="p2_obs" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Informado por padres</label><br>
            <input type="radio" name="p2_padres" value="1"> Sí
            <input type="radio" name="p2_padres" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Informado por otros</label><br>
            <input type="radio" name="p2_otros" value="1"> Sí
            <input type="radio" name="p2_otros" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Código</label>
            <select name="p2_codigo" class="form-select">
              <option value="">Seleccionar</option>
              <option value="1">Adquirido</option>
              <option value="2">Emergente</option>
              <option value="3">No observado</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Pregunta 3 -->
      <div class="card mb-3">
        <div class="card-header bg-light">
          <strong>3. Mira a las personas y a fotos de personas cuando estas son nombradas.</strong>
        </div>
        <div class="card-body row">
          <div class="col-md-3">
            <label class="form-label">Observado</label><br>
            <input type="radio" name="p3_obs" value="1"> Sí
            <input type="radio" name="p3_obs" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Informado por padres</label><br>
            <input type="radio" name="p3_padres" value="1"> Sí
            <input type="radio" name="p3_padres" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Informado por otros</label><br>
            <input type="radio" name="p3_otros" value="1"> Sí
            <input type="radio" name="p3_otros" value="0"> No
          </div>
          <div class="col-md-3">
            <label class="form-label">Código</label>
            <select name="p3_codigo" class="form-select">
              <option value="">Seleccionar</option>
              <option value="1">Adquirido</option>
              <option value="2">Emergente</option>
              <option value="3">No observado</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Botón de envío -->
      <div class="text-end mb-5">
        <button type="submit" class="btn btn-success">💾 Guardar respuestas</button>
      </div>

    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

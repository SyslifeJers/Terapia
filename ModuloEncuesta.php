<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Encuesta - Alta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <?php include 'menu.php'; ?>

  <div class="container mt-5">
    <h2 class="mb-4">📄 Alta de Encuesta</h2>

<!-- Subir PDF -->
<div class="card mb-4">
  <div class="card-header bg-primary text-white">1. Subir archivo PDF</div>
  <div class="card-body">
    <form>

      <!-- Nombre -->
      <div class="mb-3">
        <label for="nombreEncuesta" class="form-label">Nombre de la encuesta</label>
        <input type="text" class="form-control" id="nombreEncuesta" placeholder="Ej. ESDM, Vineland, etc.">
      </div>

      <!-- Área de aplicación -->
      <div class="mb-3">
        <label for="area" class="form-label">Área</label>
        <select class="form-select" id="area">
          <option selected disabled>Selecciona un área</option>
          <option value="lenguaje">Lenguaje</option>
          <option value="psicologica">Psicológica</option>
          <option value="fisica">Física</option>
          <option value="ocupacional">Terapia Ocupacional</option>
        </select>
      </div>

      <!-- Sección -->
      <div class="mb-3">
        <label for="seccion" class="form-label">Sección</label>
        <select class="form-select" id="seccion">
          <option selected disabled>Selecciona una sección</option>
          <option value="1">Sección 1</option>
          <option value="2">Sección 2</option>
          <option value="3">Sección 3</option>
        </select>
      </div>

      <!-- Archivo PDF -->
      <div class="mb-3">
        <label for="pdfFile" class="form-label">Seleccionar archivo PDF</label>
        <input type="file" class="form-control" id="pdfFile" accept="application/pdf">
      </div>

      <!-- Botón -->
      <button type="button" class="btn btn-success" onclick="simularExtraccion()">
        <i class="bi bi-upload"></i> Subir y extraer preguntas
      </button>
    </form>
  </div>
</div>

    <!-- Preguntas extraídas -->
    <div class="card mb-5 d-none" id="bloquePreguntas">
      <div class="card-header bg-info text-white">2. Preguntas detectadas (corregibles)</div>
      <div class="card-body">
        <form id="formPreguntas">
          <div id="contenedorPreguntas">
            <!-- Preguntas simuladas aquí -->
          </div>
          <a href="GeneraEncuesta.php" class="btn btn-primary mt-3">💾 Guardar encuesta</a>
        </form>
      </div>
    </div>

  </div>

  <!-- Script de Bootstrap y simulación -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 <script>
function simularExtraccion() {
  const preguntasSimuladas = [
    "¿El niño responde a su nombre cuando se le llama?",
    "¿Hace contacto visual durante una interacción social?",
    "¿Usa gestos para comunicarse como señalar o decir adiós?",
    "¿Participa en juegos simples con otros niños o adultos?",
    "¿Puede seguir instrucciones sencillas?"
  ];

  const contenedor = document.getElementById("contenedorPreguntas");
  contenedor.innerHTML = "";

  preguntasSimuladas.forEach((texto, i) => {
    const preguntaId = `pregunta_${i}`;

    contenedor.innerHTML += `
      <div class="card mb-4" id="${preguntaId}">
        <div class="card-header">
          <strong>Pregunta ${i + 1}:</strong> ${texto}
          <div class="form-check form-switch float-end">
            <input class="form-check-input" type="checkbox" id="${preguntaId}_tipo" onchange="cambiarTipo('${preguntaId}')">
            <label class="form-check-label" for="${preguntaId}_tipo">Opción múltiple</label>
          </div>
        </div>
        <div class="card-body">
          <div class="respuesta-libre">
            <label class="form-label">Respuesta libre:</label>
            <textarea class="form-control" name="${preguntaId}_libre" rows="2"></textarea>
          </div>
          <div class="respuesta-multiple d-none">
            <div class="row">
              <div class="col-md-3">
                <label class="form-label">Observado</label>
                <select name="${preguntaId}_observado" class="form-select">
                  <option value="">Seleccionar</option>
                  <option value="1">Sí</option>
                  <option value="2">No</option>
                  <option value="3">Parcial</option>
                  <option value="4">No aplica</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Informado por padres</label>
                <select name="${preguntaId}_padres" class="form-select">
                  <option value="">Seleccionar</option>
                  <option value="1">Sí</option>
                  <option value="2">No</option>
                  <option value="3">Parcial</option>
                  <option value="4">No aplica</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Informado por otros</label>
                <select name="${preguntaId}_otros" class="form-select">
                  <option value="">Seleccionar</option>
                  <option value="1">Sí</option>
                  <option value="2">No</option>
                  <option value="3">Parcial</option>
                  <option value="4">No aplica</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Código</label>
                <select name="${preguntaId}_codigo" class="form-select">
                  <option value="">Seleccionar</option>
                  <option value="1">Adquirido</option>
                  <option value="2">Emergente</option>
                  <option value="3">No observado</option>
                  <option value="4">No aplica</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  document.getElementById("bloquePreguntas").classList.remove("d-none");
}

// Cambia el tipo de respuesta entre libre y múltiple
function cambiarTipo(id) {
  const libre = document.querySelector(`#${id} .respuesta-libre`);
  const multiple = document.querySelector(`#${id} .respuesta-multiple`);
  const check = document.getElementById(`${id}_tipo`);

  if (check.checked) {
    libre.classList.add('d-none');
    multiple.classList.remove('d-none');
  } else {
    libre.classList.remove('d-none');
    multiple.classList.add('d-none');
  }
}
</script>


</body>
</html>

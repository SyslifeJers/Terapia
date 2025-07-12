<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario de Evaluación Múltiple</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .pregunta {
      padding: 15px;
      border-bottom: 1px solid #dee2e6;
    }
    .pregunta:last-child {
      border-bottom: none;
    }
    .pregunta strong {
      display: block;
      margin-bottom: 8px;
    }
    .form-textarea {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <?php include 'menu.php'; ?>
<div class="container py-4">
  <h3 class="mb-4">📝 Evaluación</h3>

  <form id="formEvaluacion">

    <!-- Pregunta 1 -->
    <div class="pregunta">
      <strong>1. ¿Mantiene contacto visual al interactuar?</strong>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="p1" id="p1_si" value="Sí" autocomplete="off">
        <label class="btn btn-outline-success" for="p1_si">Sí</label>

        <input type="radio" class="btn-check" name="p1" id="p1_parcial" value="Parcial" autocomplete="off">
        <label class="btn btn-outline-warning" for="p1_parcial">Parcial</label>

        <input type="radio" class="btn-check" name="p1" id="p1_no" value="No" autocomplete="off">
        <label class="btn btn-outline-danger" for="p1_no">No</label>
      </div>
      <textarea class="form-control form-textarea" name="p1_comentario" placeholder="Comentario adicional..."></textarea>
    </div>

    <!-- Pregunta 2 -->
    <div class="pregunta">
      <strong>2. ¿Sigue instrucciones simples sin ayuda?</strong>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="p2" id="p2_si" value="Sí" autocomplete="off">
        <label class="btn btn-outline-success" for="p2_si">Sí</label>

        <input type="radio" class="btn-check" name="p2" id="p2_parcial" value="Parcial" autocomplete="off">
        <label class="btn btn-outline-warning" for="p2_parcial">Parcial</label>

        <input type="radio" class="btn-check" name="p2" id="p2_no" value="No" autocomplete="off">
        <label class="btn btn-outline-danger" for="p2_no">No</label>
      </div>
      <textarea class="form-control form-textarea" name="p2_comentario" placeholder="Comentario adicional..."></textarea>
    </div>

    <!-- Pregunta 3 -->
    <div class="pregunta">
      <strong>3. ¿Participa activamente en la actividad?</strong>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="p3" id="p3_si" value="Sí" autocomplete="off">
        <label class="btn btn-outline-success" for="p3_si">Sí</label>

        <input type="radio" class="btn-check" name="p3" id="p3_parcial" value="Parcial" autocomplete="off">
        <label class="btn btn-outline-warning" for="p3_parcial">Parcial</label>

        <input type="radio" class="btn-check" name="p3" id="p3_no" value="No" autocomplete="off">
        <label class="btn btn-outline-danger" for="p3_no">No</label>
      </div>
      <textarea class="form-control form-textarea" name="p3_comentario" placeholder="Comentario adicional..."></textarea>
    </div>

    <!-- Pregunta 4 -->
    <div class="pregunta">
      <strong>4. ¿Muestra iniciativa en la comunicación?</strong>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="p4" id="p4_si" value="Sí" autocomplete="off">
        <label class="btn btn-outline-success" for="p4_si">Sí</label>

        <input type="radio" class="btn-check" name="p4" id="p4_parcial" value="Parcial" autocomplete="off">
        <label class="btn btn-outline-warning" for="p4_parcial">Parcial</label>

        <input type="radio" class="btn-check" name="p4" id="p4_no" value="No" autocomplete="off">
        <label class="btn btn-outline-danger" for="p4_no">No</label>
      </div>
      <textarea class="form-control form-textarea" name="p4_comentario" placeholder="Comentario adicional..."></textarea>
    </div>

    <!-- Pregunta 5 -->
    <div class="pregunta">
      <strong>5. ¿Presenta frustración o desconexión frecuente?</strong>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="p5" id="p5_si" value="Sí" autocomplete="off">
        <label class="btn btn-outline-success" for="p5_si">Sí</label>

        <input type="radio" class="btn-check" name="p5" id="p5_parcial" value="Parcial" autocomplete="off">
        <label class="btn btn-outline-warning" for="p5_parcial">Parcial</label>

        <input type="radio" class="btn-check" name="p5" id="p5_no" value="No" autocomplete="off">
        <label class="btn btn-outline-danger" for="p5_no">No</label>
      </div>
      <textarea class="form-control form-textarea" name="p5_comentario" placeholder="Comentario adicional..."></textarea>
    </div>

    <!-- Guardar -->
    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> Guardar evaluación
      </button>
    </div>

  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

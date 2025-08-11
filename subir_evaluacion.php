<?php
include_once 'includes/head.php';
?>
<div class="nk-content nk-content-fluid">
  <div class="container-xl wide-xl">
    <div class="nk-content-body">
      <h3>Subir evaluación</h3>
      <form method="post" action="pacientes/upload_exam.php" enctype="multipart/form-data" class="gy-3">
        <div class="row g-3 align-center">
          <div class="col-12">
            <div class="form-group">
              <label class="form-label" for="file">Archivo</label>
              <div class="form-control-wrap">
                <input type="file" class="form-control" id="file" name="file" required>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label class="form-label" for="id">ID Paciente</label>
              <div class="form-control-wrap">
                <input type="number" class="form-control" id="id" name="id" required>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label class="form-label" for="note">Nota</label>
              <div class="form-control-wrap">
                <textarea class="form-control" id="note" name="note"></textarea>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Subir</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
include_once 'includes/footer.php';
?>

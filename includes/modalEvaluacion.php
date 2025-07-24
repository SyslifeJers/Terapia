<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="pacientes/guardar_evaluacion.php" class="form-validate">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva evaluación</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_nino" value="<?php echo $id ?? 0; ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id'] ?? 0; ?>">
                    <div class="form-group">
                        <label class="form-label" for="participacion">Participación</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button"  class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" value="5" id="participacion" name="participacion" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="form-label" for="atencion">Atención</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" value="5" id="atencion" name="atencion" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tarea_casa">Tarea en casa</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" value="5" id="tarea_casa" name="tarea_casa" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="observaciones">Observaciones</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

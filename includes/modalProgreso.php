<div class="modal fade" id="modalProgreso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="pacientes/guardar_progreso.php" class="form-validate">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo progreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_nino" value="<?php echo $id ?? 0; ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id'] ?? 0; ?>">
                    <div class="form-group">
                        <label class="form-label" for="lenguaje">Lenguaje</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" id="lenguaje" name="lenguaje" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="motricidad">Motricidad</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" id="motricidad" name="motricidad" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="atencion_pg">Atención</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" id="atencion_pg" name="atencion" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="memoria">Memoria</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" id="memoria" name="memoria" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="social">Social</label>
                        <div class="form-control-wrap number-spinner-wrap">
                            <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                            <input type="number" class="form-control number-spinner" id="social" name="social" min="1" max="10" required>
                            <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="observaciones_pg">Observaciones</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="observaciones_pg" name="observaciones"></textarea>
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

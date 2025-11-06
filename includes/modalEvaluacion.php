<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $hayCriterios = !empty($criteriosAsignados ?? []); ?>
            <form method="POST" action="guardar_evaluacion.php" class="form-validate">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva evaluación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_nino" value="<?php echo $id ?? 0; ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id'] ?? 0; ?>">
                    <?php if ($hayCriterios): ?>
                        <?php foreach ($criteriosAsignados as $criterio): ?>
                            <div class="form-group">
                                <label class="form-label" for="criterio-<?php echo (int)$criterio['id_criterio']; ?>">
                                    <?php echo htmlspecialchars($criterio['nombre']); ?>
                                </label>
                                <div class="form-control-wrap number-spinner-wrap">
                                    <button type="button" class="btn btn-icon btn-warning number-spinner-btn number-minus" data-number="minus"><em class="icon ni ni-minus"></em></button>
                                    <input type="number" class="form-control number-spinner" value="5" id="criterio-<?php echo (int)$criterio['id_criterio']; ?>" name="criterios[<?php echo (int)$criterio['id_criterio']; ?>]" min="1" max="10" required>
                                    <button type="button" class="btn btn-icon btn-success number-spinner-btn number-plus" data-number="plus"><em class="icon ni ni-plus"></em></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Asigna criterios de evaluación al paciente para poder registrar una nueva valoración.</p>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="form-label" for="observaciones">Observaciones</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary" <?php echo $hayCriterios ? '' : 'disabled'; ?>>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

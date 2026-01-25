<div class="modal fade hist-eval-modal" id="modalHistEval" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial de Evaluaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped hist-eval-table" id="histEvalTable">
                        <thead>
                            <tr id="histEvalHeadRow">
                                <th>Fecha</th>
                                <th class="hist-eval-observaciones">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody id="histEvalBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistProg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial de Progreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="histProgTable">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Lenguaje</th>
                                <th>Motricidad</th>
                                <th>Atención</th>
                                <th>Memoria</th>
                                <th>Social</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody id="histProgBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditEvalObservaciones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar observaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editEvalObservacionesForm">
                    <input type="hidden" id="editEvalId" name="id_valoracion" />
                    <div class="mb-3">
                        <label for="editEvalObservaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="editEvalObservaciones" name="observaciones" rows="6"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveEvalObservaciones">Guardar</button>
            </div>
        </div>
    </div>
</div>

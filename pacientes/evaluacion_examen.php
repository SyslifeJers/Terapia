<?php
include_once '../includes/head.php';
include_once '../includes/menu_superior.php';
require_once '../database/conexion.php';

$id_nino = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <h3 class="nk-block-title page-title mb-4">Nueva evaluación</h3>
            <form id="evalForm" method="POST" action="guardar_examen_evaluacion.php">
                <input type="hidden" name="id_nino" value="<?php echo $id_nino; ?>">
                <input type="hidden" name="respuestas" id="respuestas">
                <div id="sec1">
                    <h5 class="mb-3">Sección 1</h5>
                    <div class="form-group">
                        <label class="form-label">1. ¿Mantiene contacto visual al interactuar?</label>
                        <select class="form-select" id="p1" required>
                            <option value="">Selecciona</option>
                            <option value="Si">Sí</option>
                            <option value="Parcial">Parcial</option>
                            <option value="No">No</option>
                        </select>
                        <textarea id="p1c" class="form-control mt-2" placeholder="Comentario"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">2. ¿Sigue instrucciones simples sin ayuda?</label>
                        <select class="form-select" id="p2" required>
                            <option value="">Selecciona</option>
                            <option value="Si">Sí</option>
                            <option value="Parcial">Parcial</option>
                            <option value="No">No</option>
                        </select>
                        <textarea id="p2c" class="form-control mt-2" placeholder="Comentario"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" onclick="nextSec(1)">Siguiente</button>
                </div>
                <div id="sec2" style="display:none;">
                    <h5 class="mb-3">Sección 2</h5>
                    <div class="form-group">
                        <label class="form-label">3. ¿Participa activamente en la actividad?</label>
                        <select class="form-select" id="p3" required>
                            <option value="">Selecciona</option>
                            <option value="Si">Sí</option>
                            <option value="Parcial">Parcial</option>
                            <option value="No">No</option>
                        </select>
                        <textarea id="p3c" class="form-control mt-2" placeholder="Comentario"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">4. ¿Muestra iniciativa en la comunicación?</label>
                        <select class="form-select" id="p4" required>
                            <option value="">Selecciona</option>
                            <option value="Si">Sí</option>
                            <option value="Parcial">Parcial</option>
                            <option value="No">No</option>
                        </select>
                        <textarea id="p4c" class="form-control mt-2" placeholder="Comentario"></textarea>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-secondary" onclick="prevSec(2)">Anterior</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function nextSec(n){
    document.getElementById('sec'+n).style.display='none';
    document.getElementById('sec'+(n+1)).style.display='block';
}
function prevSec(n){
    document.getElementById('sec'+n).style.display='none';
    document.getElementById('sec'+(n-1)).style.display='block';
}

const form=document.getElementById('evalForm');
form.addEventListener('submit',function(e){
    const data=[
        {pregunta:'¿Mantiene contacto visual al interactuar?',respuesta:document.getElementById('p1').value,comentario:document.getElementById('p1c').value},
        {pregunta:'¿Sigue instrucciones simples sin ayuda?',respuesta:document.getElementById('p2').value,comentario:document.getElementById('p2c').value},
        {pregunta:'¿Participa activamente en la actividad?',respuesta:document.getElementById('p3').value,comentario:document.getElementById('p3c').value},
        {pregunta:'¿Muestra iniciativa en la comunicación?',respuesta:document.getElementById('p4').value,comentario:document.getElementById('p4c').value}
    ];
    document.getElementById('respuestas').value=JSON.stringify(data);
});
</script>
<?php include_once '../includes/footer.php'; ?>

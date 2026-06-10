<?php include 'app/views/includes/header.php'; ?>
<?php include 'app/views/layouts/viewMenuLateral.php'; ?>

<div class="container-fluid pt-4 ml-[260px]">
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['status'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
            <?= $flash['message'] ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Servicios</h2>
        <button class="btn btn-primary" onclick="nuevoServicio()">
            <i class="fas fa-plus mr-2"></i>Nuevo Servicio
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicios as $s): ?>
                    <tr>
                        <td><?= $s['codigo_servicio'] ?></td>
                        <td><?= $s['nombre_servicio'] ?></td>
                        <td><?= $s['tipo_servicio'] ?></td>
                        <td><?= number_format($s['precio'], 2) ?></td>
                        <td><span class="badge badge-<?= $s['estado'] ? 'success' : 'danger' ?>"><?= $s['estado'] ? 'Activo' : 'Inactivo' ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editarServicio(<?= $s['codigo_servicio'] ?>)"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarServicio(<?= $s['codigo_servicio'] ?>)"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalServicio" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formServicio" method="POST" onsubmit="prepararEnvio()">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Servicio</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="codigo_servicio" id="codigo_servicio">
                    <input type="hidden" name="materiales_json" id="materiales_json">
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre_servicio" id="nombre_servicio" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio" id="precio" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <input type="text" name="tipo_servicio" id="tipo_servicio" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
                    </div>
                    <div class="form-group" id="groupEstado" style="display:none;">
                        <label><input type="checkbox" name="estado" id="estado"> Activo</label>
                    </div>

                    <hr>
                    <h6>Materiales Requeridos</h6>
                    <div id="containerMateriales"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addMaterialRow()">+ Añadir Material</button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const PRODUCTOS = <?= json_encode($productos) ?>;

function nuevoServicio() {
    $('#formServicio')[0].reset();
    $('#modalTitle').text('Nuevo Servicio');
    $('#formServicio').attr('action', '?url=servicio&type=register');
    $('#containerMateriales').empty();
    $('#groupEstado').hide();
    $('#modalServicio').modal('show');
}

function addMaterialRow(prodId = '', cant = 1) {
    let options = PRODUCTOS.map(p => `<option value="${p.codigo_producto}" ${p.codigo_producto == prodId ? 'selected' : ''}>${p.nombre_producto} (Stock: ${p.stock_actual})</option>`).join('');
    let html = `
        <div class="row mb-2 material-row">
            <div class="col-7"><select class="form-control sel-prod">${options}</select></div>
            <div class="col-3"><input type="number" class="form-control inp-cant" value="${cant}" min="1"></div>
            <div class="col-2"><button type="button" class="btn btn-danger" onclick="$(this).closest('.row').remove()">&times;</button></div>
        </div>`;
    $('#containerMateriales').append(html);
}

function editarServicio(id) {
    $.get('?url=servicio&type=getDetails&id=' + id, function(data) {
        $('#modalTitle').text('Editar Servicio');
        $('#formServicio').attr('action', '?url=servicio&type=update');
        $('#codigo_servicio').val(data.codigo_servicio);
        $('#nombre_servicio').val(data.nombre_servicio);
        $('#precio').val(data.precio);
        $('#tipo_servicio').val(data.tipo_servicio);
        $('#descripcion').val(data.descripcion);
        $('#estado').prop('checked', data.estado == 1);
        $('#groupEstado').show();
        $('#containerMateriales').empty();
        data.materiales.forEach(m => addMaterialRow(m.codigo_producto, m.cantidad_usada));
        $('#modalServicio').modal('show');
    });
}

function prepararEnvio() {
    let materiales = [];
    $('.material-row').each(function() {
        materiales.push({
            codigo_producto: $(this).find('.sel-prod').val(),
            cantidad_usada: $(this).find('.inp-cant').val()
        });
    });
    $('#materiales_json').val(JSON.stringify(materiales));
}

function eliminarServicio(id) {
    if(confirm('¿Seguro de eliminar este servicio?')) {
        $.post('?url=servicio&type=main', {deleteService: true, idservicio: id}, function(res) {
            if(res.status === 'success') location.reload();
        });
    }
}
</script>

<?php include 'app/views/includes/footer.php'; ?>
<?php 
$content = ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="fas fa-cart-plus me-2"></i>Nueva Venta</h2>
  <a href="<?= BASE_URL ?>ventas" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
</div>
<?php if (!empty($success)): ?><div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check me-2"></i><?= htmlspecialchars($success) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>ventas" id="ventaForm">
  <?= \App\Core\Csrf::field(); ?>
  <div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Datos del Cliente</h5></div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Nombre</label><input type="text" name="cliente_nombre" class="form-control" /></div>
        <div class="col-md-4"><label class="form-label">Documento</label><input type="text" name="cliente_documento" class="form-control" /></div>
        <div class="col-md-4"><label class="form-label">Teléfono</label><input type="text" name="cliente_telefono" class="form-control" /></div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Items</h5>
      <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()"><i class="fas fa-plus me-1"></i>Agregar</button>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0" id="itemsTable">
          <thead class="table-light"><tr><th style="width:30%">Repuesto</th><th>Cant.</th><th>Precio Unit.</th><th>Subtotal</th><th></th></tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header"><h6 class="mb-0">Notas</h6></div>
        <div class="card-body">
          <p class="text-muted small mb-1">- Los precios por defecto se toman del precio de venta del repuesto.</p>
          <p class="text-muted small mb-1">- Puede ajustar el precio unitario antes de guardar.</p>
          <p class="text-muted small mb-0">- El descuento se aplica sobre el subtotal general.</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header"><h6 class="mb-0">Totales</h6></div>
        <div class="card-body">
          <div class="mb-2 d-flex justify-content-between"><span>Subtotal:</span><strong id="subtotalDisplay">S/ 0.00</strong></div>
          <div class="mb-2 d-flex justify-content-between align-items-center">
            <span>Descuento:</span>
            <div class="input-group" style="width:160px;">
              <span class="input-group-text">S/</span>
              <input type="number" step="0.01" min="0" name="descuento" id="descuento" value="0" class="form-control" oninput="recalcTotals()" />
            </div>
          </div>
          <hr />
          <div class="mb-2 d-flex justify-content-between"><span>Total:</span><strong id="totalDisplay">S/ 0.00</strong></div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-end gap-2">
    <a href="<?= BASE_URL ?>ventas" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Registrar Venta</button>
  </div>
</form>

<script>
const repuestosData = <?php echo json_encode(array_map(function($r){
  return [
    'id' => $r->getId(),
    'nombre' => $r->getNombre(),
    'codigo' => $r->getCodigo(),
    'precio' => (float)$r->getPrecioVenta(),
    'stock' => (int)$r->getStockActual()
  ];
}, $repuestos)); ?>;

function addItemRow(){
  const tbody = document.querySelector('#itemsTable tbody');
  const tr = document.createElement('tr');
  const index = tbody.children.length; // Usar índice numérico
  
  const selectHtml = `<select name="items[${index}][repuesto_id]" class="form-select" onchange="updateRowPrice(this)"><option value="">--Seleccione--</option>${repuestosData.map(r=>`<option value="${r.id}" data-precio="${r.precio}" data-stock="${r.stock}">${r.codigo} - ${r.nombre} (Stock:${r.stock})</option>`).join('')}</select>`;
  tr.innerHTML = `<td>${selectHtml}</td>
    <td><input type="number" name="items[${index}][cantidad]" min="1" value="1" class="form-control" oninput="recalcRow(this)" /></td>
    <td><input type="number" step="0.01" name="items[${index}][precio_unitario]" class="form-control" value="0" oninput="recalcRow(this)" /></td>
    <td class="fw-bold subtotalCell">S/ 0.00</td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); recalcTotals();"><i class="fas fa-trash"></i></button></td>`;
  tbody.appendChild(tr);
}

function updateRowPrice(sel){
  const opt = sel.selectedOptions[0];
  if (!opt) return; 
  const precio = parseFloat(opt.dataset.precio || '0');
  const row = sel.closest('tr');
  row.querySelector('input[name*="[precio_unitario]"]').value = precio.toFixed(2);
  recalcRow(row.querySelector('input[name*="[precio_unitario]"]'));
}

function recalcRow(el){
  const row = el.closest('tr');
  const qty = parseFloat(row.querySelector('input[name*="[cantidad]"]').value||'0');
  const price = parseFloat(row.querySelector('input[name*="[precio_unitario]"]').value||'0');
  const sub = qty * price;
  row.querySelector('.subtotalCell').textContent = 'S/ ' + sub.toFixed(2);
  recalcTotals();
}

function recalcTotals(){
  let subtotal = 0;
  document.querySelectorAll('#itemsTable tbody tr').forEach(tr=>{
    const qty = parseFloat(tr.querySelector('input[name*="[cantidad]"]').value||'0');
    const price = parseFloat(tr.querySelector('input[name*="[precio_unitario]"]').value||'0');
    subtotal += qty * price;
  });
  document.getElementById('subtotalDisplay').textContent = 'S/ ' + subtotal.toFixed(2);
  let descuento = parseFloat(document.getElementById('descuento').value||'0');
  if (descuento > subtotal) descuento = subtotal;
  document.getElementById('descuento').value = descuento.toFixed(2);
  const total = subtotal - descuento;
  document.getElementById('totalDisplay').textContent = 'S/ ' + total.toFixed(2);
}

document.getElementById('ventaForm').addEventListener('submit', function(e){
  const rows = document.querySelectorAll('#itemsTable tbody tr');
  if (rows.length === 0) {
    e.preventDefault();
    alert('Debe agregar al menos un item');
    return;
  }
  
  // Verificar que cada fila tenga repuesto seleccionado
  let hasValidItems = false;
  rows.forEach(row => {
    const select = row.querySelector('select[name*="[repuesto_id]"]');
    const cantidad = row.querySelector('input[name*="[cantidad]"]');
    if (select && select.value && cantidad && cantidad.value > 0) {
      hasValidItems = true;
    }
  });
  
  if (!hasValidItems) {
    e.preventDefault();
    alert('Debe seleccionar al menos un repuesto válido con cantidad mayor a 0');
    return;
  }
});
</script>
<?php
$content = ob_get_clean();
include SRC_PATH . '/Views/layouts/app.php';
?>
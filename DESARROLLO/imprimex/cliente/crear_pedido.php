<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
requiereRol('cliente');

$u = usuarioActual();
$activo = 'nuevo';

$precios = $pdo->query('SELECT categoria, precio_por_unidad FROM categorias_precios')->fetchAll(PDO::FETCH_KEY_PAIR);
$error = $_SESSION['error_pedido'] ?? '';
unset($_SESSION['error_pedido']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>IMPRIMEX · Crear nuevo pedido</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<div class="topbar">
  <h1>IMPRIMEX · Plataforma de Gestión de Pedidos</h1>
  <p>Formulario guiado: categoría, tipo de trabajo, especificaciones, cantidad y fecha de entrega.</p>
</div>

<div class="container">
  <div class="layout">
    <?php include __DIR__ . '/../includes/nav_sidebar.php'; ?>

    <div class="main">
      <h2 class="page-title" style="margin-top:0;">Crear nuevo pedido</h2>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/cliente/guardar_pedido.php" method="post" enctype="multipart/form-data" id="form-pedido">
        <div class="layout" style="gap:18px;">
          <div class="card" style="flex:2;">

            <div class="form-group">
              <label>Paso 1 · Selecciona la categoría</label>
              <div class="category-grid">
                <?php
                $categorias = [
                  'Fachadas'     => 'Vinilos, lonas, letreros',
                  'Interiores'   => 'Papel mural, vinilos deco',
                  'Gran formato' => 'Pendones, banners',
                ];
                $i = 0;
                foreach ($categorias as $cat => $desc): $i++; ?>
                <label class="category-card" data-precio="<?= $precios[$cat] ?? 0 ?>">
                  <input type="radio" name="categoria" value="<?= $cat ?>" <?= $i === 1 ? 'checked' : '' ?> required>
                  <div class="cat-title"><?= $cat ?></div>
                  <div class="cat-sub"><?= $desc ?></div>
                </label>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="form-group">
              <label>Paso 2 · Tipo de trabajo</label>
              <input type="text" name="tipo_trabajo" placeholder="Ej: Vinilo impreso adhesivo para fachada" required>
            </div>

            <label style="margin-bottom:8px;">Paso 3 · Especificaciones</label>
            <div class="form-row">
              <div class="form-group">
                <label>Cantidad (m²)</label>
                <input type="number" step="0.01" min="0.01" name="cantidad" id="cantidad" placeholder="45" required>
              </div>
              <div class="form-group">
                <label>Fecha de entrega deseada</label>
                <input type="date" name="fecha_entrega" required>
              </div>
            </div>

            <div class="form-group">
              <label>Archivo de diseño (opcional)</label>
              <input type="file" name="archivo_diseno" accept=".pdf,.ai,.jpg,.jpeg,.png">
            </div>

            <div class="form-group">
              <label>Notas adicionales</label>
              <textarea name="notas" placeholder="Instrucciones especiales de instalación..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enviar pedido</button>
            <button type="submit" name="borrador" value="1" class="btn">Guardar como borrador</button>
          </div>

          <div class="card" style="flex:1; height:fit-content;">
            <h3 style="margin-top:0;">Resumen del pedido</h3>
            <div class="summary-box"><span class="k">Categoría:</span> <strong id="r-categoria"><?= array_key_first($categorias) ?></strong></div>
            <div class="summary-box"><span class="k">Cantidad:</span> <strong id="r-cantidad">—</strong></div>
            <div class="summary-box"><span class="k">Entrega estimada:</span> <strong id="r-fecha">—</strong></div>
            <hr style="border:none; border-top:1px solid var(--border); margin:14px 0;">
            <div class="k" style="font-size:13px;">Costo estimado</div>
            <div class="cost" id="r-costo">$ 0</div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <p class="footer-note">IMPRIMEX · Plataforma de Gestión de Pedidos</p>
</div>

<script>
const cards = document.querySelectorAll('.category-card');
const cantidadInput = document.getElementById('cantidad');
const fechaInput = document.querySelector('input[name=fecha_entrega]');

function marcarSeleccion() {
  cards.forEach(c => c.classList.toggle('selected', c.querySelector('input').checked));
}
function actualizarResumen() {
  const seleccionada = document.querySelector('.category-card input:checked').closest('label');
  const precio = parseFloat(seleccionada.dataset.precio || 0);
  const cantidad = parseFloat(cantidadInput.value || 0);
  document.getElementById('r-categoria').textContent = seleccionada.querySelector('input').value;
  document.getElementById('r-cantidad').textContent = cantidad ? cantidad + ' m²' : '—';
  document.getElementById('r-fecha').textContent = fechaInput.value || '—';
  document.getElementById('r-costo').textContent = '$ ' + Math.round(precio * cantidad).toLocaleString('es-CO');
}
cards.forEach(c => c.addEventListener('click', () => { marcarSeleccion(); actualizarResumen(); }));
cantidadInput.addEventListener('input', actualizarResumen);
fechaInput.addEventListener('input', actualizarResumen);
marcarSeleccion();
actualizarResumen();
</script>
</body>
</html>

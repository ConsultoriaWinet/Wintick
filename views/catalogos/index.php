<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Sistemas[] $sistemas */
/** @var app\models\Servicios[] $servicios */

$this->title = 'Catálogos';

$paneles = [
    'sistemas'  => ['titulo' => 'Sistemas',  'icono' => 'fa-desktop', 'datos' => $sistemas],
    'servicios' => ['titulo' => 'Servicios', 'icono' => 'fa-tools',   'datos' => $servicios],
];
?>

<style>
    .cat-wrap { max-width: 1200px; margin: 0 auto; padding: 8px 4px 40px; }
    .cat-head { display:flex; align-items:center; gap:10px; margin-bottom:4px; }
    .cat-head h1 { font-size:22px; font-weight:700; margin:0; color:var(--text,#1A1814); }
    .cat-head .fa-database { color:#8BA590; }
    .cat-sub { color:var(--text-3,#807868); font-size:13px; margin-bottom:18px; }

    .cat-grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:18px;
    }
    @media (max-width:880px){ .cat-grid{ grid-template-columns:1fr; } }

    .cat-card {
        background:var(--surface,#fff);
        border:1px solid var(--border,#E8E2D2);
        border-radius:14px;
        display:flex; flex-direction:column;
        overflow:hidden;
        box-shadow:0 2px 10px rgba(0,0,0,.04);
    }
    .cat-card-head {
        display:flex; align-items:center; gap:9px;
        padding:14px 16px;
        border-bottom:1px solid var(--border,#E8E2D2);
        background:var(--surface-2,#F5F1E8);
    }
    .cat-card-head i { color:#8BA590; font-size:16px; }
    .cat-card-head h2 { font-size:15px; font-weight:700; margin:0; flex:1; color:var(--text,#1A1814); }
    .cat-count {
        background:#8BA590; color:#fff; font-size:12px; font-weight:700;
        padding:2px 10px; border-radius:20px; min-width:26px; text-align:center;
    }

    /* Zona de alta */
    .cat-add { padding:12px 16px; border-bottom:1px solid var(--border,#E8E2D2); }
    .cat-add label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--text-3,#807868); display:block; margin-bottom:6px; }
    .cat-add textarea {
        width:100%; border:1px solid var(--border,#E8E2D2); border-radius:9px;
        padding:9px 11px; font-size:13px; resize:vertical; min-height:46px;
        font-family:'IBM Plex Sans',sans-serif; color:var(--text,#1A1814); background:var(--surface,#fff);
    }
    .cat-add textarea:focus { outline:none; border-color:#8BA590; box-shadow:0 0 0 3px rgba(139,165,144,.15); }
    .cat-add-hint { font-size:11px; color:var(--text-3,#9ca3af); margin-top:5px; }
    .cat-add-bar { display:flex; justify-content:flex-end; margin-top:9px; }
    .cat-btn {
        border:none; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:600;
        cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:background .15s, opacity .15s;
    }
    .cat-btn-add { background:#8BA590; color:#fff; }
    .cat-btn-add:hover { background:#7a9480; }
    .cat-btn-del { background:#fdecea; color:#c0392b; }
    .cat-btn-del:hover { background:#f8d7d3; }
    .cat-btn:disabled { opacity:.5; cursor:not-allowed; }

    /* Toolbar de la lista */
    .cat-tools { display:flex; align-items:center; gap:10px; padding:9px 16px; border-bottom:1px solid var(--border,#E8E2D2); }
    .cat-search { flex:1; position:relative; }
    .cat-search i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:12px; }
    .cat-search input {
        width:100%; border:1px solid var(--border,#E8E2D2); border-radius:8px;
        padding:7px 10px 7px 30px; font-size:13px; color:var(--text,#1A1814); background:var(--surface,#fff);
    }
    .cat-search input:focus { outline:none; border-color:#8BA590; }

    /* Lista */
    .cat-list { list-style:none; margin:0; padding:0; max-height:420px; overflow-y:auto; }
    .cat-row {
        display:flex; align-items:center; gap:11px; padding:9px 16px;
        border-bottom:1px solid var(--border,#F1ECE0); font-size:13.5px; color:var(--text,#1A1814);
    }
    .cat-row:hover { background:var(--surface-2,#FAF7F0); }
    .cat-row.cat-hidden { display:none; }
    .cat-row input[type=checkbox] { width:16px; height:16px; cursor:pointer; accent-color:#8BA590; flex-shrink:0; }
    .cat-row .cat-name { flex:1; word-break:break-word; }
    .cat-row .cat-id { font-size:11px; color:var(--text-3,#9ca3af); }
    .cat-row-del {
        border:none; background:none; color:#c0392b; cursor:pointer; font-size:13px;
        padding:4px 7px; border-radius:6px; opacity:.6; transition:opacity .15s, background .15s;
    }
    .cat-row-del:hover { opacity:1; background:#fdecea; }
    .cat-empty { padding:26px 16px; text-align:center; color:var(--text-3,#9ca3af); font-size:13px; }

    /* Footer de selección */
    .cat-foot {
        display:flex; align-items:center; gap:10px; padding:10px 16px;
        border-top:1px solid var(--border,#E8E2D2); background:var(--surface-2,#F5F1E8);
    }
    .cat-selall { display:flex; align-items:center; gap:7px; font-size:12.5px; color:var(--text-2,#4D483F); cursor:pointer; user-select:none; }
    .cat-selall input { width:15px; height:15px; accent-color:#8BA590; cursor:pointer; }
    .cat-selinfo { font-size:12px; color:var(--text-3,#807868); margin-left:auto; }
</style>

<div class="cat-wrap">
    <div class="cat-head">
        <i class="fas fa-database"></i>
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <div class="cat-sub">Administra los catálogos de Sistemas y Servicios. Solo visible para Desarrolladores.</div>

    <div class="cat-grid">
        <?php foreach ($paneles as $tipo => $panel): ?>
            <div class="cat-card" data-tipo="<?= $tipo ?>">
                <div class="cat-card-head">
                    <i class="fas <?= $panel['icono'] ?>"></i>
                    <h2><?= Html::encode($panel['titulo']) ?></h2>
                    <span class="cat-count" id="count-<?= $tipo ?>"><?= count($panel['datos']) ?></span>
                </div>

                <!-- Alta (individual o masiva) -->
                <div class="cat-add">
                    <label>Agregar (uno por línea)</label>
                    <textarea id="add-<?= $tipo ?>" rows="2" placeholder="Escribe un nombre, o varios separados por saltos de línea…"></textarea>
                    <div class="cat-add-hint"><i class="fas fa-info-circle"></i> Cada línea se guarda como un registro. Se omiten vacíos y duplicados.</div>
                    <div class="cat-add-bar">
                        <button type="button" class="cat-btn cat-btn-add" onclick="catAgregar('<?= $tipo ?>')">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>

                <!-- Buscador -->
                <div class="cat-tools">
                    <div class="cat-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Filtrar…" oninput="catFiltrar('<?= $tipo ?>', this.value)">
                    </div>
                </div>

                <!-- Lista -->
                <ul class="cat-list" id="list-<?= $tipo ?>">
                    <?php if (empty($panel['datos'])): ?>
                        <li class="cat-empty" data-empty="1">Sin registros.</li>
                    <?php else: ?>
                        <?php foreach ($panel['datos'] as $row): ?>
                            <li class="cat-row" data-id="<?= (int) $row->id ?>" data-nombre="<?= Html::encode(mb_strtolower($row->Nombre)) ?>">
                                <input type="checkbox" class="cat-chk" value="<?= (int) $row->id ?>" onchange="catActualizarSel('<?= $tipo ?>')">
                                <span class="cat-name"><?= Html::encode($row->Nombre) ?></span>
                                <span class="cat-id">#<?= (int) $row->id ?></span>
                                <button type="button" class="cat-row-del" title="Eliminar" onclick="catEliminar('<?= $tipo ?>', [<?= (int) $row->id ?>])">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <!-- Footer de selección masiva -->
                <div class="cat-foot">
                    <label class="cat-selall">
                        <input type="checkbox" id="selall-<?= $tipo ?>" onchange="catSelTodos('<?= $tipo ?>', this.checked)">
                        Seleccionar todo
                    </label>
                    <button type="button" class="cat-btn cat-btn-del" id="delsel-<?= $tipo ?>" onclick="catEliminarSeleccionados('<?= $tipo ?>')" disabled>
                        <i class="fas fa-trash"></i> Eliminar seleccionados
                    </button>
                    <span class="cat-selinfo" id="selinfo-<?= $tipo ?>">0 seleccionados</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const CAT_URL_GUARDAR  = '<?= Url::to(['catalogos/guardar']) ?>';
    const CAT_URL_ELIMINAR = '<?= Url::to(['catalogos/eliminar']) ?>';
    const CAT_CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function catEscape(s) {
        const d = document.createElement('div');
        d.textContent = s ?? '';
        return d.innerHTML;
    }

    function catPost(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CAT_CSRF },
            body: JSON.stringify(payload),
        }).then(r => r.json());
    }

    /* ── Agregar (individual o masivo) ── */
    function catAgregar(tipo) {
        const ta = document.getElementById('add-' + tipo);
        const nombres = ta.value.split('\n').map(s => s.trim()).filter(Boolean);
        if (nombres.length === 0) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Escribe al menos un nombre', showConfirmButton: false, timer: 1800 });
            return;
        }
        catPost(CAT_URL_GUARDAR, { tipo, nombres })
            .then(data => {
                if (!data.success) { Swal.fire('Error', data.message || 'No se pudo guardar', 'error'); return; }

                (data.items || []).forEach(item => catInsertarFila(tipo, item.id, item.Nombre));
                catRecount(tipo);
                ta.value = '';

                let msg = data.creados + ' agregado(s)';
                if (data.duplicados && data.duplicados.length) msg += ' · ' + data.duplicados.length + ' duplicado(s) omitido(s)';
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, showConfirmButton: false, timer: 2600 });
            })
            .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
    }

    function catInsertarFila(tipo, id, nombre) {
        const list = document.getElementById('list-' + tipo);
        const vacio = list.querySelector('[data-empty]');
        if (vacio) vacio.remove();

        const li = document.createElement('li');
        li.className = 'cat-row';
        li.dataset.id = id;
        li.dataset.nombre = (nombre || '').toLowerCase();
        li.innerHTML = `
            <input type="checkbox" class="cat-chk" value="${id}" onchange="catActualizarSel('${tipo}')">
            <span class="cat-name">${catEscape(nombre)}</span>
            <span class="cat-id">#${id}</span>
            <button type="button" class="cat-row-del" title="Eliminar" onclick="catEliminar('${tipo}', [${id}])">
                <i class="fas fa-trash"></i>
            </button>`;
        list.prepend(li);
    }

    /* ── Eliminar ── */
    function catEliminar(tipo, ids) {
        if (!ids || ids.length === 0) return;
        const plural = ids.length > 1;
        Swal.fire({
            title: plural ? `¿Eliminar ${ids.length} registros?` : '¿Eliminar este registro?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c0392b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(res => {
            if (!res.isConfirmed) return;
            catPost(CAT_URL_ELIMINAR, { tipo, ids })
                .then(data => {
                    if (!data.success) { Swal.fire('Error', data.message || 'No se pudo eliminar', 'error'); return; }

                    (data.ids || []).forEach(id => {
                        const li = document.querySelector(`#list-${tipo} .cat-row[data-id="${id}"]`);
                        if (li) li.remove();
                    });
                    catRecount(tipo);
                    catActualizarSel(tipo);

                    if (data.enUso && data.enUso.length) {
                        const lista = data.enUso.map(u => `• ${catEscape(u.nombre)} (${u.tickets} ticket${u.tickets !== 1 ? 's' : ''})`).join('<br>');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Algunos no se eliminaron',
                            html: `<div style="text-align:left;font-size:13px;">Están en uso por tickets:<br><br>${lista}</div>`,
                        });
                    } else {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.eliminados + ' eliminado(s)', showConfirmButton: false, timer: 2000 });
                    }
                })
                .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
        });
    }

    function catEliminarSeleccionados(tipo) {
        const ids = catIdsSeleccionados(tipo);
        if (ids.length === 0) return;
        catEliminar(tipo, ids);
    }

    /* ── Selección ── */
    function catIdsSeleccionados(tipo) {
        return Array.from(document.querySelectorAll(`#list-${tipo} .cat-chk:checked`)).map(c => parseInt(c.value, 10));
    }

    function catActualizarSel(tipo) {
        const n = catIdsSeleccionados(tipo).length;
        const total = document.querySelectorAll(`#list-${tipo} .cat-chk`).length;
        document.getElementById('selinfo-' + tipo).textContent = n + ' seleccionado' + (n !== 1 ? 's' : '');
        document.getElementById('delsel-' + tipo).disabled = n === 0;
        const selall = document.getElementById('selall-' + tipo);
        selall.checked = total > 0 && n === total;
        selall.indeterminate = n > 0 && n < total;
    }

    function catSelTodos(tipo, checked) {
        document.querySelectorAll(`#list-${tipo} .cat-row:not(.cat-hidden) .cat-chk`).forEach(c => { c.checked = checked; });
        catActualizarSel(tipo);
    }

    /* ── Filtro en cliente ── */
    function catFiltrar(tipo, q) {
        q = (q || '').trim().toLowerCase();
        document.querySelectorAll(`#list-${tipo} .cat-row`).forEach(li => {
            const match = !q || (li.dataset.nombre || '').includes(q);
            li.classList.toggle('cat-hidden', !match);
        });
    }

    /* ── Recontar ── */
    function catRecount(tipo) {
        const list = document.getElementById('list-' + tipo);
        const filas = list.querySelectorAll('.cat-row');
        document.getElementById('count-' + tipo).textContent = filas.length;
        if (filas.length === 0 && !list.querySelector('[data-empty]')) {
            const li = document.createElement('li');
            li.className = 'cat-empty';
            li.dataset.empty = '1';
            li.textContent = 'Sin registros.';
            list.appendChild(li);
        }
    }
</script>

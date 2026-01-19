<?php
/**
 * Archivo de DEBUGGING para verificar filtros
 * Descomenta las líneas de debug en TicketsSearch para usarlo
 */
?>

<!-- AGREGAR ESTO ARRIBA DE LA TABLA PARA DEBUG -->
<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 4px; font-family: monospace; font-size: 11px;">
    <strong>🔍 DEBUG - Parámetros GET recibidos:</strong><br>
    <?php if (!empty($_GET)): ?>
        <pre><?php var_dump($_GET); ?></pre>
    <?php else: ?>
        <span style="color: #999;">Sin parámetros GET</span>
    <?php endif; ?>
    
    <strong>🔍 DEBUG - Valores en $searchModel:</strong><br>
    <pre>
mes: <?= $searchModel->mes ?? 'null' ?>
fecha_inicio: <?= $searchModel->fecha_inicio ?? 'null' ?>
fecha_fin: <?= $searchModel->fecha_fin ?? 'null' ?>
cliente_id: <?= $searchModel->Cliente_id ?? 'null' ?>
sistema_id: <?= $searchModel->Sistema_id ?? 'null' ?>
servicio_id: <?= $searchModel->Servicio_id ?? 'null' ?>
prioridad: <?= $searchModel->Prioridad ?? 'null' ?>
estado: <?= $searchModel->Estado ?? 'null' ?>
asignado_a: <?= $searchModel->Asignado_a ?? 'null' ?>
    </pre>
    
    <strong>📊 Total tickets encontrados: <?= $dataProvider->getTotalCount() ?></strong>
</div>

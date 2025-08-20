<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Equipos - <?php echo e($concurso->nombre ?? ''); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .estado-en-orden { color: #155724; background: #d4edda; }
        .estado-descalificado { color: #721c24; background: #f8d7da; }
        .integrantes-list { margin: 0; padding-left: 18px; }
        .promedio-final { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Reporte de Equipos</h1>
    <h2>Concurso: <?php echo e($concurso->nombre ?? ''); ?></h2>
    <table>
        <thead>
            <tr>
                <th># Equipo</th>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Integrantes</th>
                <th>Promedio Final</th>
            </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $equipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($equipo->id); ?></td>
                <td>
                    <?php echo e($equipo->proyecto->nombre ?? 'Sin proyecto'); ?>

                </td>
                <td class="<?php echo e(($equipo->proyecto->estado ?? '') === 'Descalificado' ? 'estado-descalificado' : 'estado-en-orden'); ?>">
                    <?php echo e($equipo->proyecto->estado ?? 'Sin estado'); ?>

                </td>
                <td>
                    <ul class="integrantes-list">
                        <?php $__currentLoopData = $equipo->participantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($p->nombre); ?> <?php if($p->telefono): ?> (<?php echo e($p->telefono); ?>) <?php endif; ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </td>
                <td class="promedio-final">
                    <?php echo e($equipo->resultadoFinal->promedio_final ?? 'N/A'); ?>

                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <p>Fecha de generación: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i')); ?></p>
</body>
</html>
<?php /**PATH C:\Users\Cristino\Documents\web\proyecto-pwa\resources\views/pdf/reporte_equipos.blade.php ENDPATH**/ ?>
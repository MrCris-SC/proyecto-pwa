<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Participación en Concurso</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #222; margin: 40px; }
        h1, h2, h3 { color: #611232; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 30px; }
        .evaluador-block { border: 1px solid #8A1C4A; border-radius: 8px; margin-bottom: 20px; padding: 16px; }
        .evaluador-title { font-size: 1.1em; font-weight: bold; color: #8A1C4A; margin-bottom: 8px; }
        .puntajes-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .puntajes-table th, .puntajes-table td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        .puntajes-table th { background: #f3e6ee; }
        .contribucion { font-style: italic; color: #444; }
        .footer { text-align: center; font-size: 0.9em; color: #888; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Resumen de Participación en Concurso</h1>
        <h2><?php echo e($concurso->nombre ?? 'Concurso'); ?></h2>
        <p><strong>Equipo:</strong> <?php echo e($equipo->id); ?> | <strong>Proyecto:</strong> <?php echo e($equipo->proyecto->nombre ?? '-'); ?></p>
        <p><strong>Integrantes:</strong>
            <?php $__currentLoopData = $equipo->participantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e($p->nombre); ?><?php echo e(!$loop->last ? ',' : ''); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </p>
    </div>

    <div class="section">
        <h3>Evaluadores y Puntajes</h3>
        <?php $__empty_1 = true; $__currentLoopData = $evaluaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $evaluacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="evaluador-block">
                <div class="evaluador-title">
                    Evaluador <?php echo e($idx + 1); ?>

                    <?php if(!empty($evaluacion->perfil)): ?>
                        &mdash; Perfiles:
                        <?php $__currentLoopData = (array)$evaluacion->perfil; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($p); ?><?php echo e(!$loop->last ? ',' : ''); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
                <table class="puntajes-table">
                    <thead>
                        <tr>
                            <th>Criterio</th>
                            <th>Puntaje Asignado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $evaluacion->puntajes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $puntaje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($puntaje->criterio->nombre ?? '-'); ?></td>
                                <td><?php echo e($puntaje->puntaje_obtenido); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td colspan="3">
                                <strong>Comentario:</strong> <?php echo e($evaluacion->comentarios ?? '-'); ?>

                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="contribucion">
                    <strong>Contribución total de este evaluador:</strong>
                    <?php echo e($evaluacion->puntajes->sum('puntaje_obtenido')); ?>

                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>No hay evaluaciones registradas para este equipo.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Reporte generado el <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i')); ?></p>
    </div>
</body>
</html><?php /**PATH C:\Users\Cristino\Documents\web\proyecto-pwa\resources\views/pdf/resultados.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Evaluaciones - <?php echo e($concurso->nombre ?? 'Concurso'); ?></title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #222; margin: 40px; }
        h1, h2, h3 { color: #611232; }
        .header { text-align: center; margin-bottom: 30px; }
        .equipo-section { margin-bottom: 40px; }
        .equipo-title { font-size: 1.1em; font-weight: bold; color: #8A1C4A; margin-bottom: 8px; }
        .integrantes { margin-bottom: 8px; }
        .evaluador-block { border: 1px solid #8A1C4A; border-radius: 8px; margin-bottom: 16px; padding: 12px; }
        .evaluador-title { font-weight: bold; color: #8A1C4A; margin-bottom: 6px; }
        .puntajes-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .puntajes-table th, .puntajes-table td { border: 1px solid #ccc; padding: 4px 8px; text-align: left; }
        .puntajes-table th { background: #f3e6ee; }
        .contribucion { font-style: italic; color: #444; }
        .footer { text-align: center; font-size: 0.9em; color: #888; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Evaluaciones</h1>
        <h2><?php echo e($concurso->nombre ?? 'Concurso'); ?></h2>
    </div>
    <?php $__currentLoopData = $equipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="equipo-section">
            <div class="equipo-title">
                Equipo #<?php echo e($equipo->id); ?> | Proyecto: <?php echo e($equipo->proyecto->nombre ?? '-'); ?>

            </div>
            <div class="integrantes">
                <strong>Integrantes:</strong>
                <?php if($equipo->participantes && count($equipo->participantes)): ?>
                    <?php $__currentLoopData = $equipo->participantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($p->nombre); ?><?php echo e(!$loop->last ? ',' : ''); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    Sin integrantes
                <?php endif; ?>
            </div>
            <div>
                <strong>Evaluaciones:</strong>
                <?php if(isset($equipo->evaluaciones) && count($equipo->evaluaciones)): ?>
                    <?php $__currentLoopData = $equipo->evaluaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $evaluacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                        <td colspan="2">
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div>No hay evaluaciones registradas para este equipo.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="footer">
        <p>Reporte generado el <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Cristino\Documents\web\proyecto-pwa\resources\views/pdf/reporte_evaluaciones_todos.blade.php ENDPATH**/ ?>
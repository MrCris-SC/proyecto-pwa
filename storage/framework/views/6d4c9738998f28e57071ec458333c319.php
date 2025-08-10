<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resultados Finales</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        h1, h2 { color: #222; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        th, td { border: 1px solid #888; padding: 6px 10px; text-align: left; }
        th { background: #f8f8f8; }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Resultados Finales</h1>

    <?php $__currentLoopData = $modalidadesAgrupadas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modalidad => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h2><?php echo e($modalidad); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Proyecto</th>
                    <th>Categoría</th>
                    <th>Promedio Final</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data['resultados']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($idx + 1); ?></td>
                        <td><?php echo e($res->equipo->proyecto->nombre ?? 'Sin nombre'); ?></td>
                        <td>
                            <?php
                                $cat = $res->equipo->proyecto->categoria ?? '';
                                echo $cat === 'alumno' ? 'Alumno' : ($cat === 'docente' ? 'Docente' : ucfirst($cat));
                            ?>
                        </td>
                        <td><?php echo e($res->promedio_final); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>

<?php /**PATH C:\Users\Cristino\Documents\web\proyecto-pwa\resources\views/pdf/resultados_finales.blade.php ENDPATH**/ ?>
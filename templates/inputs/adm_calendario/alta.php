<?php /** @var gamboamartin\acl\controllers\controlador_adm_seccion $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<?php echo $controlador->inputs->adm_usuario_id; ?>
<?php echo $controlador->inputs->adm_seccion_id; ?>
<?php echo $controlador->inputs->titulo; ?>
<?php echo $controlador->inputs->descripcion; ?>

<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>

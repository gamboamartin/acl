<?php /** @var controllers\controlador_org_empresa $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->descripcion; ?>


<?php echo $controlador->inputs->adm_menu_id; ?>


<?php include (new views())->ruta_templates.'botons/submit/alta_bd_otro.php';?>

<div class="control-group btn-alta col-12">
    <div class="controls">
        <?php include 'templates/botons/adm_menu_alta.php';?>
    </div>
</div>

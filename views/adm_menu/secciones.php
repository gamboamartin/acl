<?php /** @var gamboamartin\acl\controllers\controlador_adm_menu $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_adm_seccion_alta_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/title.php"; ?>
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                        <?php include (new views())->ruta_templates."mensajes.php"; ?>

                        <?php echo $controlador->inputs->select->adm_menu_id; ?>
                        <?php echo $controlador->inputs->adm_seccion_menu_descripcion; ?>
                        <?php echo $controlador->inputs->hidden_adm_menu_id; ?>
                        <?php echo $controlador->inputs->hidden_seccion_retorno; ?>
                        <?php echo $controlador->inputs->hidden_id_retorno; ?>
                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="secciones" name="btn_action_next">Alta</button><br>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="widget widget-box box-container widget-mylistings">

                    <div class="">
                        <table id="adm_seccion" class="table table-striped" >
                            <thead>
                            <tr>
                                <?php echo $controlador->ths; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($controlador->secciones as $seccion){ ?>
                            <tr>
                                <td><?php echo $seccion['adm_seccion_id']; ?></td>
                                <td><?php echo $seccion['adm_seccion_descripcion']; ?></td>
                                <td><?php echo $seccion['adm_seccion_n_acciones']; ?></td>
                                <td>
                                    <?php foreach ($seccion['acciones'] as $link){ ?>
                                    <div class="col-md-3"><?php echo $link; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>

                        </table>
                    </div>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>
    </div>

</main>


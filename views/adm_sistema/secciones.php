<?php /** @var gamboamartin\acl\controllers\controlador_adm_sistema $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_adm_seccion_pertenece_alta_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/title.php"; ?>
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                        <?php include (new views())->ruta_templates."mensajes.php"; ?>

                        <?php echo $controlador->inputs->select->adm_sistema_id; ?>
                        <?php echo $controlador->inputs->select->adm_menu_id; ?>
                        <?php echo $controlador->inputs->select->adm_seccion_id; ?>
                        <?php echo $controlador->inputs->hidden_adm_sistema_id; ?>
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
                        <table id="adm_accion" class="table table-striped" >
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Sistema</th>
                                <th>Seccion</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($controlador->adm_secciones_pertenece as $adm_seccion_pertenece){ ?>
                                <tr>
                                    <td><?php echo $adm_seccion_pertenece['adm_seccion_pertenece_id']; ?></td>
                                    <td><?php echo $adm_seccion_pertenece['adm_seccion_descripcion']; ?></td>
                                    <td><?php echo $adm_seccion_pertenece['adm_sistema_descripcion']; ?></td>
                                    <td>
                                        <?php foreach ($adm_seccion_pertenece['acciones'] as $link){ ?>
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
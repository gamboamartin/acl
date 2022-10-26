<?php /** @var gamboamartin\acl\controllers\controlador_adm_grupo $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_adm_usuario_alta_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/title.php"; ?>
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                        <?php include (new views())->ruta_templates."mensajes.php"; ?>

                        <?php echo $controlador->inputs->select->adm_grupo_id; ?>
                        <?php echo $controlador->inputs->adm_usuario_user; ?>
                        <?php echo $controlador->inputs->adm_usuario_password; ?>
                        <?php echo $controlador->inputs->adm_usuario_email; ?>
                        <?php echo $controlador->inputs->adm_usuario_telefono; ?>
                        <?php echo $controlador->inputs->hidden_adm_grupo_id; ?>
                        <?php echo $controlador->inputs->hidden_seccion_retorno; ?>
                        <?php echo $controlador->inputs->hidden_id_retorno; ?>
                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="usuarios" name="btn_action_next">Alta</button><br>
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
                        <table id="adm_usuario" class="table table-striped" >
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Grupo</th>
                                <th>Telefono</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($controlador->adm_usuarios as $usuario){ ?>
                            <tr>
                                <td><?php echo $usuario['adm_usuario_id']; ?></td>
                                <td><?php echo $usuario['adm_usuario_user']; ?></td>
                                <td><?php echo $usuario['adm_usuario_email']; ?></td>
                                <td><?php echo $usuario['adm_grupo_descripcion']; ?></td>
                                <td><?php echo $usuario['adm_usuario_telefono']; ?></td>
                                <td>
                                    <?php foreach ($usuario['acciones'] as $link){ ?>
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


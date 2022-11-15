<?php
namespace tests\controllers;

use controllers\controlador_cat_sat_tipo_persona;
use gamboamartin\acl\controllers\controlador_adm_menu;
use gamboamartin\errores\errores;
use gamboamartin\template_1\html;
use gamboamartin\test\liberator;
use gamboamartin\test\test;
use html\adm_menu_html;
use html\nom_conf_factura_html;
use JsonException;
use models\em_cuenta_bancaria;
use models\fc_cfd_partida;
use models\fc_factura;
use models\fc_partida;
use models\nom_nomina;
use models\nom_par_deduccion;
use models\nom_par_percepcion;
use stdClass;


class controlador_adm_menu_Test extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/acl/config/generales.php';
        $this->paths_conf->database = '/var/www/html/acl/config/database.php';
        $this->paths_conf->views = '/var/www/html/acl/config/views.php';
    }

    public function test_inputs_secciones(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_menu';
        $_GET['accion'] = 'lista';
        $_GET['registro_id'] = 1;
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $controler = new controlador_adm_menu(link: $this->link, paths_conf: $this->paths_conf);
        $controler = new liberator($controler);

        $adm_menu_id = 1;
        $resultado = $controler->inputs_secciones($adm_menu_id);

        $this->assertIsObject($resultado);
        $this->assertStringContainsStringIgnoringCase("<div class='control-group col-sm-12'><label class='contro",$resultado->select->adm_menu_id);
        $this->assertStringContainsStringIgnoringCase("='control-label' for='adm_menu_id'>Menu",$resultado->select->adm_menu_id);

        $this->assertStringContainsStringIgnoringCase("div class='control-group col-sm-12'><label class='control-label' for='descripcion",$resultado->adm_seccion_menu_descripcion);
        $this->assertStringContainsStringIgnoringCase("<input type='hidden' name='adm_menu_id' value='1'>",$resultado->hidden_adm_menu_id);
        $this->assertStringContainsStringIgnoringCase("<input type='hidden' name='seccion_retorno' value='adm_menu'>",$resultado->hidden_seccion_retorno);
        $this->assertStringContainsStringIgnoringCase("<input type='hidden' name='id_retorno' value='1'>",$resultado->hidden_id_retorno);
        errores::$error = false;
    }


    public function test_secciones_data(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_menu';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $controler = new controlador_adm_menu(link: $this->link, paths_conf: $this->paths_conf);
        $controler = new liberator($controler);

        $adm_menu_id = 1;
        $resultado = $controler->secciones_data($adm_menu_id);


        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<a role='button' href='index.php?seccion=adm_seccion&accion=elimina_bd&registro_id=1&session_id=1' class='btn btn-danger col-sm-12'>elimina_bd</a>", $resultado[0]['acciones']['elimina_bd']);

        errores::$error = false;
    }


}


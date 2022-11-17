<?php
namespace tests\controllers;

use controllers\controlador_cat_sat_tipo_persona;
use gamboamartin\acl\controllers\controlador_adm_accion;
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


class controlador_adm_accion_Test extends test {
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

    public function test_get_adm_accion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_menu';
        $_GET['accion'] = 'lista';
        $_GET['registro_id'] = 1;
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $controler = new controlador_adm_accion(link: $this->link, paths_conf: $this->paths_conf);
        //$controler = new liberator($controler);

        $_GET['adm_seccion_id'] = 1;
        $resultado = $controler->get_adm_accion(false, false);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;



        $_GET['adm_seccion_id'] = 1;
        $_POST['not_in']['llave'] = 'a';
        $_POST['not_in']['values'] = array();
        $resultado = $controler->get_adm_accion(false, false);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

        $_GET['adm_seccion_id'] = 1;
        $_GET['adm_grupo_id'] = 38;
        $_POST['not_in']['llave'] = 'a';
        $_POST['not_in']['values'] = array();
        $resultado = $controler->get_adm_accion(false, false);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

        $_GET['adm_seccion_id'] = 1;
        $_GET['adm_grupo_id'] = 38;
        $_POST['not_in']['llave'] = 'adm_accion.id';
        $_POST['not_in']['values'] = array('19');
        $resultado = $controler->get_adm_accion(false, false);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;
    }




}


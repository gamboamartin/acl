<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_accion_basica;
use gamboamartin\errores\errores;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_accion_basica_html;
use links\secciones\link_adm_accion_basica;
use PDO;
use stdClass;


class controlador_adm_accion_basica extends system {


    public stdClass|array $adm_accion_basica = array();


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_accion_basica(link: $link);

        $html_ = new adm_accion_basica_html(html: $html);
        $obj_link = new link_adm_accion_basica(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_accion_basica_id']['titulo'] = 'Id';
        $datatables->columns['adm_accion_basica_codigo']['titulo'] = 'Cod';
        $datatables->columns['adm_accion_basica_descripcion']['titulo'] = 'Accion';
        $datatables->columns['adm_accion_basica_css']['titulo'] = 'CSS';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_accion_basica.id';
        $datatables->filtro[] = 'adm_accion_basica.codigo';
        $datatables->filtro[] = 'adm_accion_basica.descripcion';
        $datatables->filtro[] = 'adm_accion_basica.css';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Acciones Basicas';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $adm_accion_basica = (new adm_accion_basica($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener adm_accion_basica',data:  $adm_accion_basica);
                print_r($error);
                exit;
            }
            $this->adm_accion_basica = $adm_accion_basica;
        }

    }

}

<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\errores\errores;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_sistema_html;
use links\secciones\link_adm_sistema;
use models\adm_sistema;
use PDO;
use stdClass;


class controlador_adm_sistema extends system {

    public array $secciones = array();
    public stdClass|array $adm_sistema = array();


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_sistema(link: $link);

        $html_ = new adm_sistema_html(html: $html);
        $obj_link = new link_adm_sistema(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_sistema_id']['titulo'] = 'Id';
        $datatables->columns['adm_sistema_descripcion']['titulo'] = 'Descripcion';
        $datatables->columns['adm_sistema_codigo']['titulo'] = 'Codigo';
        $datatables->columns['adm_sistema_codigo_bis']['titulo'] = 'Codigo BIS';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Sistemas';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $adm_sistema = (new adm_sistema($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener adm_sistema',data:  $adm_sistema);
                print_r($error);
                exit;
            }
            $this->adm_sistema = $adm_sistema;
        }

    }


}

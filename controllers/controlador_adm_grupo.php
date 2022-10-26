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
use html\adm_grupo_html;
use html\adm_menu_html;
use links\secciones\link_adm_grupo;
use models\adm_grupo;
use models\adm_menu;
use PDO;
use stdClass;


class controlador_adm_grupo extends system {

    public array $secciones = array();
    public stdClass|array $adm_grupo = array();
    public string $link_adm_grupo_alta_bd = '';


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_grupo(link: $link);

        $html_ = new adm_grupo_html(html: $html);
        $obj_link = new link_adm_grupo($this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_grupo_id']['titulo'] = 'Id';
        $datatables->columns['adm_grupo_descripcion']['titulo'] = 'Id';
        $datatables->columns['adm_grupo_codigo']['titulo'] = 'Codigo';
        $datatables->columns['adm_grupo_codigo_bis']['titulo'] = 'Codigo BIS';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Grupos';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $adm_grupo = (new adm_grupo($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener adm_grupo',data:  $adm_grupo);
                print_r($error);
                exit;
            }
            $this->adm_grupo = $adm_grupo;
        }

        $link_adm_grupo_alta_bd = $this->obj_link->link_alta_bd(seccion: 'adm_grupo');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_grupo_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_grupo_alta_bd = $link_adm_grupo_alta_bd;

    }




}

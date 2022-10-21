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
use gamboamartin\system\datatables;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_menu_html;
use links\secciones\link_adm_menu;
use models\adm_menu;
use PDO;
use stdClass;


class controlador_adm_menu extends system {

    public array $secciones = array();
    public stdClass|array $adm_menu = array();
    public string $link_adm_seccion_alta_bd = '';


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_menu(link: $link);

        $html_ = new adm_menu_html(html: $html);
        $obj_link = new link_adm_menu($this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_menu_id']['titulo'] = 'Id';
        $datatables->columns['adm_menu_descripcion']['titulo'] = 'Id';
        $datatables->columns['adm_menu_codigo']['titulo'] = 'Codigo';
        $datatables->columns['adm_menu_codigo_bis']['titulo'] = 'Codigo BIS';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Menus';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $adm_menu = (new adm_menu($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener menu',data:  $adm_menu);
                print_r($error);
                exit;
            }
            $this->adm_menu = $adm_menu;
        }

        $link_adm_seccion_alta_bd = $this->obj_link->link_alta_bd(seccion: 'adm_seccion');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_seccion_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_seccion_alta_bd = $link_adm_seccion_alta_bd;

    }

    public function secciones(bool $header = true, bool $ws = false){


        $secciones = (new adm_menu($this->link))->secciones(adm_menu_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener secciones',data:  $secciones, header: $header,ws:  $ws);
        }


        $secciones = $this->rows_con_permisos(key_id:  'adm_seccion_id',rows:  $secciones,seccion: 'adm_seccion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar link',data:  $secciones, header: $header,ws:  $ws);
        }


        $this->secciones = $secciones;
        
        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:12,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_menu_id',data:  $select_adm_menu_id, header: $header,ws:  $ws);
        }

        $adm_seccion_menu_descripcion = (new adm_menu_html(html: $this->html_base))->input_descripcion(cols:12,row_upd:  new stdClass(), value_vacio: true);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_seccion_menu_descripcion',data:  $adm_seccion_menu_descripcion, header: $header,ws:  $ws);
        }

        $hidden_adm_menu_id = (new adm_menu_html(html: $this->html_base))->hidden(name: 'adm_menu_id', value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_adm_menu_id',data:  $hidden_adm_menu_id, header: $header,ws:  $ws);
        }

        $hidden_seccion_retorno = (new adm_menu_html(html: $this->html_base))->hidden(name: 'seccion_retorno', value: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_adm_menu_id',data:  $hidden_adm_menu_id, header: $header,ws:  $ws);
        }

        $hidden_id_retorno = (new adm_menu_html(html: $this->html_base))->hidden(name: 'id_retorno', value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_adm_menu_id',data:  $hidden_adm_menu_id, header: $header,ws:  $ws);
        }

        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_menu_id = $select_adm_menu_id;
        $this->inputs->adm_seccion_menu_descripcion = $adm_seccion_menu_descripcion;
        $this->inputs->hidden_adm_menu_id = $hidden_adm_menu_id;
        $this->inputs->hidden_seccion_retorno = $hidden_seccion_retorno;
        $this->inputs->hidden_id_retorno = $hidden_id_retorno;


        /*
        $columns['adm_seccion_menu_id'] = 'Id';

        $columns = (new datatables())->acciones_columnas(columns: $columns, link: $this->link, seccion: 'adm_seccion_menu');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al maquetar acciones ', data: $columns);
            var_dump($error);
            die('Error');
        }



        $this->datatable_init(columns: $columns, filtro: array(),identificador: '#adm_seccion');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar columnDefs', data: $this->datatable);
            var_dump($error);
            die('Error');
        }*/




    }


}

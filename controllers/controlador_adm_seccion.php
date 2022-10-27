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
use gamboamartin\system\html_controler;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_accion_html;
use html\adm_menu_html;
use html\adm_seccion_html;
use links\secciones\link_adm_seccion;
use models\adm_seccion;
use PDO;
use stdClass;


class controlador_adm_seccion extends system {

    public string $link_adm_accion_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_seccion(link: $link);

        $html_ = new adm_seccion_html(html: $html);
        $obj_link = new link_adm_seccion($this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_seccion_id']['titulo'] = 'Id';
        $datatables->columns['adm_seccion_codigo']['titulo'] = 'Cod';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Seccion';
        $datatables->columns['adm_menu_descripcion']['titulo'] = 'Menu';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables: $datatables, paths_conf: $paths_conf);



        $this->titulo_lista = 'Secciones';

        $link_adm_accion_alta_bd = $this->obj_link->link_alta_bd(seccion: 'adm_accion');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_accion_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_accion_alta_bd = $link_adm_accion_alta_bd;

    }

    public function acciones(bool $header = true, bool $ws = false){

        $adm_seccion = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener seccion',data:  $adm_seccion, header: $header,ws:  $ws);
        }

        $acciones = (new adm_seccion($this->link))->acciones(adm_seccion_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener acciones',data:  $acciones, header: $header,ws:  $ws);
        }


        $acciones = $this->rows_con_permisos(key_id:  'adm_accion_id',rows:  $acciones,seccion: 'adm_accion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar link',data:  $acciones, header: $header,ws:  $ws);
        }

        $this->acciones = $acciones;

        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:6,con_registros: true,id_selected:  $adm_seccion->adm_menu_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_menu_id',data:  $select_adm_menu_id, header: $header,ws:  $ws);
        }


        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:6,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_seccion_id',data:  $select_adm_seccion_id, header: $header,ws:  $ws);
        }

        $adm_accion_descripcion = (new adm_accion_html(html: $this->html_base))->input_descripcion(
            cols:12,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_accion_descripcion',data:  $adm_accion_descripcion, header: $header,ws:  $ws);
        }

        $adm_accion_titulo = (new adm_accion_html(html: $this->html_base))->input_titulo(
            cols:12,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_accion_descripcion',data:  $adm_accion_descripcion, header: $header,ws:  $ws);
        }

        $hidden_adm_seccion_id = (new adm_menu_html(html: $this->html_base))->hidden(name: 'adm_seccion_id', value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_adm_seccion_id',data:  $hidden_adm_seccion_id, header: $header,ws:  $ws);
        }


        $retornos = (new html_controler(html: $this->html_base))->retornos(registro_id: $this->registro_id,tabla:  $this->tabla);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener retornos',data:  $retornos, header: $header,ws:  $ws);
        }

        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_menu_id = $select_adm_menu_id;
        $this->inputs->select->adm_seccion_id = $select_adm_seccion_id;
        $this->inputs->adm_accion_descripcion = $adm_accion_descripcion;
        $this->inputs->adm_accion_titulo = $adm_accion_titulo;
        $this->inputs->hidden_adm_seccion_id = $hidden_adm_seccion_id;
        $this->inputs->hidden_seccion_retorno = $retornos->hidden_seccion_retorno;
        $this->inputs->hidden_id_retorno = $retornos->hidden_id_retorno;




    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: false, ws: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $inputs = (new adm_seccion_html(html: $this->html_base))->genera_inputs_alta(controler: $this, link: $this->link);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }

        return $r_alta;

    }

    public function modifica(bool $header, bool $ws = false, string $breadcrumbs = '', bool $aplica_form = true, bool $muestra_btn = true): array|string
    {
        $r_modifica = parent::modifica($header, $ws, $breadcrumbs, $aplica_form, $muestra_btn); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_modifica, header: $header,ws:$ws);
        }

        $select = (new adm_menu_html(html:$this->html_base))->select_adm_menu_id(cols: 12,con_registros: true,
            id_selected: $this->row_upd->adm_menu_id,link: $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar select',data:  $select, header: $header,ws:$ws);
        }



        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_menu_id = $select;


        return $r_modifica;
    }


}

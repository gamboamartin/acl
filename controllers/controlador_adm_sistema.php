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
use html\adm_menu_html;
use html\adm_seccion_html;
use html\adm_sistema_html;
use links\secciones\link_adm_sistema;
use models\adm_sistema;
use PDO;
use stdClass;


class controlador_adm_sistema extends system {

    public array $adm_secciones_pertenece = array();
    public stdClass|array $adm_sistema = array();
    public string $link_adm_seccion_pertenece_alta_bd = '';

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

        $link_adm_seccion_pertenece_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'adm_seccion_pertenece');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_seccion_pertenece_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_seccion_pertenece_alta_bd = $link_adm_seccion_pertenece_alta_bd;

    }

    public function secciones(bool $header = true, bool $ws = false): array|stdClass{

        if($this->registro_id<=0){
            return $this->errores->error(mensaje: 'Error this->registro_id debe ser mayor a 0',
                data:  $this->registro_id);
        }

        $adm_sistema = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener adm_sistema',data:  $adm_sistema);
        }

        $select_adm_sistema_id = (new adm_sistema_html(html: $this->html_base))->select_adm_sistema_id(
            cols:12,con_registros: true,id_selected:  $adm_sistema->adm_sistema_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_sistema_id',data:  $select_adm_sistema_id, header: $header,ws:  $ws);
        }

        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:6,con_registros: true,id_selected:  -1,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_menu_id',data:  $select_adm_menu_id, header: $header,ws:  $ws);
        }

        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:6,con_registros: false,id_selected:  -1,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_seccion_id',data:  $select_adm_seccion_id, header: $header,ws:  $ws);
        }

        $secciones_pertenece = (new adm_sistema($this->link))->secciones_pertenece(adm_sistema_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener secciones_pertenece',data:  $secciones_pertenece, header: $header,ws:  $ws);
        }

        $secciones_pertenece = $this->rows_con_permisos(key_id:  'adm_seccion_pertenece_id',
            rows:  $secciones_pertenece,seccion: 'adm_seccion_pertenece');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar link',data:  $secciones_pertenece, header: $header,ws:  $ws);
        }

        $hidden_adm_sistema_id = (new adm_sistema_html(html: $this->html_base))->hidden(name: 'adm_sistema_id', value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_adm_sistema_id',data:  $hidden_adm_sistema_id, header: $header,ws:  $ws);
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
        $this->inputs->select->adm_sistema_id = $select_adm_sistema_id;
        $this->inputs->hidden_adm_sistema_id = $hidden_adm_sistema_id;
        $this->adm_secciones_pertenece = $secciones_pertenece;
        $this->inputs->hidden_seccion_retorno = $retornos->hidden_seccion_retorno;
        $this->inputs->hidden_id_retorno = $retornos->hidden_id_retorno;



        return $adm_sistema;
    }


}

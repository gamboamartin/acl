<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_menu;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_menu_html;
use links\secciones\link_adm_menu;
use PDO;
use stdClass;


class controlador_adm_menu extends system {

    public array $secciones = array();
    public stdClass|array $adm_menu = array();
    public string $link_adm_seccion_alta_bd = '';


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_menu(link: $link);

        $html_ = new adm_menu_html(html: $html);
        $obj_link = new link_adm_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_menu_id']['titulo'] = 'Id';
        $datatables->columns['adm_menu_codigo']['titulo'] = 'Cod';
        $datatables->columns['adm_menu_descripcion']['titulo'] = 'Menu';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_menu.id';
        $datatables->filtro[] = 'adm_menu.codigo';
        $datatables->filtro[] = 'adm_menu.descripcion';

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

        $link_adm_seccion_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'adm_seccion');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_seccion_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_seccion_alta_bd = $link_adm_seccion_alta_bd;

    }

    private function inputs_secciones(int $adm_menu_id): array|stdClass
    {
        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:12,con_registros: true,id_selected:  $adm_menu_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_menu_id',data:  $select_adm_menu_id);
        }

        $adm_seccion_menu_descripcion = (new adm_menu_html(html: $this->html_base))->input_descripcion(
            cols:12,row_upd:  new stdClass(), value_vacio: true);
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener adm_seccion_menu_descripcion',data:  $adm_seccion_menu_descripcion);
        }

        $hidden_adm_menu_id = (new adm_menu_html(html: $this->html_base))->hidden(
            name: 'adm_menu_id', value: $adm_menu_id);
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener hidden_adm_menu_id',data:  $hidden_adm_menu_id);
        }


        $retornos = (new html_controler(html: $this->html_base))->retornos(registro_id: $this->registro_id,tabla:  $this->tabla);
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener retornos',data:  $retornos);
        }

        $inputs = new stdClass();
        $inputs->select = new stdClass();
        $inputs->select->adm_menu_id = $select_adm_menu_id;
        $inputs->adm_seccion_menu_descripcion = $adm_seccion_menu_descripcion;
        $inputs->hidden_adm_menu_id = $hidden_adm_menu_id;
        $inputs->hidden_seccion_retorno = $retornos->hidden_seccion_retorno;
        $inputs->hidden_id_retorno = $retornos->hidden_id_retorno;
        return $inputs;
    }

    public function secciones(bool $header = true, bool $ws = false): array|stdClass
    {

        $secciones = $this->secciones_data(adm_menu_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al integrar secciones',data:  $secciones, header: $header,ws:  $ws);
        }

        $this->secciones = $secciones;


        $inputs = $this->inputs_secciones(adm_menu_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        $this->inputs = $inputs;

        return $this->inputs;

    }

    /**
     * Obtiene los registros con botones de permiso
     * @param int $adm_menu_id Identificador
     * @return array
     * @version 0.35.0
     *
     */
    private function secciones_data(int $adm_menu_id): array
    {
        if($adm_menu_id <= 0){
            return $this->errores->error(mensaje: 'Error adm_menu_id debe ser mayor a 0',data:  $adm_menu_id);
        }
        $secciones = (new adm_menu($this->link))->secciones(adm_menu_id: $adm_menu_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener secciones',data:  $secciones);
        }

        $secciones = $this->rows_con_permisos(key_id:  'adm_seccion_id',rows:  $secciones,seccion: 'adm_seccion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar link',data:  $secciones);
        }
        return $secciones;
    }


}

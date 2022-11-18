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
use gamboamartin\template_1\html;
use html\adm_menu_html;
use html\adm_seccion_html;
use links\secciones\link_adm_menu;
use PDO;
use stdClass;


class controlador_adm_menu extends _ctl_parent {

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
        $datatables->columns['adm_menu_n_secciones']['titulo'] = 'Secciones';

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

    private function acciones_permitidas_por_registro(int $registro_id){

    }

    public function alta(bool $header, bool $ws = false): array|string
    {

        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }


        $keys_selects = array();
        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    protected function inputs_children(stdClass $registro): array|stdClass{
        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:12,con_registros: true,id_selected:  $registro->adm_menu_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_adm_menu_id',data:  $select_adm_menu_id);
        }

        $adm_seccion_descripcion = (new adm_seccion_html(html: $this->html_base))->input_descripcion(
            cols:12,row_upd:  new stdClass(), value_vacio: true, place_holder: 'Seccion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener adm_seccion_descripcion',
                data:  $adm_seccion_descripcion);
        }


        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_menu_id = $select_adm_menu_id;
        $this->inputs->adm_seccion_descripcion = $adm_seccion_descripcion;

        return $this->inputs;
    }


    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'codigo', keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Menu');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }


    public function secciones(bool $header = true, bool $ws = false): array|stdClass|string
    {

        $data_view = new stdClass();
        $data_view->names = array('Id','Seccion', 'N Acciones','Acciones');
        $data_view->keys_data = array('adm_seccion_id','adm_seccion_descripcion','adm_seccion_n_acciones');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\administrador\\models';
        $data_view->name_model_children = 'adm_seccion';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;



    }



}

<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_accion;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\template_1\html;
use html\adm_accion_html;
use html\adm_grupo_html;
use html\adm_menu_html;
use html\adm_seccion_html;
use links\secciones\link_adm_accion;
use PDO;
use stdClass;
use Throwable;


class controlador_adm_accion extends _ctl_base {

    public string $link_adm_accion_grupo_alta_bd = '';
    public array $childrens = array();

    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){

        $modelo = new adm_accion(link: $link);

        $html_ = new adm_accion_html(html: $html);
        $obj_link = new link_adm_accion(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_accion_id']['titulo'] = 'Id';
        $datatables->columns['adm_accion_descripcion']['titulo'] = 'Accion';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Seccion';
        $datatables->columns['adm_menu_descripcion']['titulo'] = 'Menu';
        $datatables->columns['adm_accion_n_permisos']['titulo'] = 'N Permisos';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_accion.id';
        $datatables->filtro[] = 'adm_accion.descripcion';
        $datatables->filtro[] = 'adm_seccion.descripcion';
        $datatables->filtro[] = 'adm_menu.descripcion';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables: $datatables, paths_conf: $paths_conf);



        $this->titulo_lista = 'Acciones';

        $link_adm_accion_grupo_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'adm_accion_grupo');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_accion_grupo_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_accion_grupo_alta_bd = $link_adm_accion_grupo_alta_bd;

        $this->lista_get_data = true;

    }

    public function alta(bool $header, bool $ws = false): array|string
    {


        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'adm_menu_id',
            keys_selects: array(), id_selected: -1, label: 'Menu');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: false,filtro: array(), key: 'adm_seccion_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Seccion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    /**
     * Da de alta una accion
     * @param bool $header
     * @param bool $ws
     * @return array|stdClass
     * @version 0.99.4
     */
    public function alta_bd(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();
        if(isset($_POST['adm_menu_id'])){
            unset($_POST['adm_menu_id']);
        }

        $result = $this->alta_bd_base();
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar registro',data:  $result, header: $header,ws: $ws);
        }

        $this->link->commit();

        $result = $this->out_alta_bd(header: $header,data_retorno:  $result->data_retorno, result: $result->r_alta_bd, ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al retornar', data: $result, header: $header,ws:  $ws);
        }

        return $result;

    }

    public function acciones_id_por_grupo(bool $header = true, bool $ws = false){


        $adm_grupo_id = $_GET['adm_grupo_id'];
        $adm_seccion_id = $_GET['adm_seccion_id'];

        $adm_acciones = (new adm_accion($this->link))->acciones_id_por_grupo(
            adm_grupo_id: $adm_grupo_id, adm_seccion_id: $adm_seccion_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener acciones', data: $adm_acciones, header: $header,ws:  $ws);
        }


        if($ws){
            ob_clean();
            header('Content-Type: application/json');
            try {
                echo json_encode($adm_acciones, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                print_r($e);
            }
            exit;
        }
        return $adm_acciones;

    }

    public function asigna_permiso(bool $header = true, bool $ws = false): array|stdClass|string{


        $data_view = new stdClass();
        $data_view->names = array('Id','Accion','Grupo','Seccion','Menu','Acciones');
        $data_view->keys_data = array('adm_accion_id','adm_accion_descripcion','adm_grupo_descripcion','adm_seccion_descripcion','adm_menu_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\administrador\\models';
        $data_view->name_model_children = 'adm_accion_grupo';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('css','codigo','descripcion','titulo');
        $keys->selects = array();

        $init_data = array();
        $init_data['adm_menu'] = "gamboamartin\\administrador";
        $init_data['adm_seccion'] = "gamboamartin\\administrador";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }

        return $campos_view;
    }

    public function es_lista(bool $header = true, bool $ws = false): array|stdClass
    {

        $upd = $this->row_upd(key: 'es_lista');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener row upd',data:  $upd, header: $header,ws:  $ws);
        }

        $_SESSION[$upd->salida][]['mensaje'] = $upd->mensaje.' del id '.$this->registro_id;
        $this->header_out(result: $upd, header: $header,ws:  $ws);

        return $upd;


    }

    public function es_status(bool $header = true, bool $ws = false): array|stdClass
    {

        $upd = $this->row_upd(key: 'es_status');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener row upd',data:  $upd, header: $header,ws:  $ws);
        }

        $_SESSION[$upd->salida][]['mensaje'] = $upd->mensaje.' del id '.$this->registro_id;
        $this->header_out(result: $upd, header: $header,ws:  $ws);

        return $upd;


    }

    public function es_view(bool $header = true, bool $ws = false): array|stdClass
    {

        $upd = $this->row_upd(key: 'es_view');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener row upd',data:  $upd, header: $header,ws:  $ws);
        }

        $_SESSION[$upd->salida][]['mensaje'] = $upd->mensaje.' del id '.$this->registro_id;
        $this->header_out(result: $upd, header: $header,ws:  $ws);

        return $upd;


    }

    /**
     *
     * @param bool $header
     * @param bool $ws
     * @return array|stdClass
     */
    public function get_adm_accion(bool $header, bool $ws = true): array|stdClass
    {

        $keys['adm_menu'] = array('id','descripcion','codigo','codigo_bis');
        $keys['adm_seccion'] = array('id','descripcion','codigo','codigo_bis');
        $keys['adm_accion'] = array('id','descripcion','codigo','codigo_bis');


        $salida = $this->get_out(header: $header,keys: $keys, ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar salida',data:  $salida,header: $header,ws: $ws);

        }


        return $salida;


    }

    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Accion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'titulo', keys_selects:$keys_selects, place_holder: 'Titulo');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    protected function inputs_children(stdClass $registro): array|stdClass
    {
        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:4,con_registros: true,id_selected:  $registro->adm_menu_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_menu_id',data:  $select_adm_menu_id);
        }

        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:4,con_registros: true,id_selected:  $registro->adm_seccion_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_seccion_id',data:  $select_adm_seccion_id);
        }

        $select_adm_accion_id = (new adm_accion_html(html: $this->html_base))->select_adm_accion_id(
            cols:4,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_accion_id);
        }

        $adm_grupos_ids = (new adm_accion(link: $this->link))->grupos_id_por_accion(adm_accion_id: $this->registro_id);
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener adm_grupos_ids',data:  $adm_grupos_ids);
        }

        $not_in['llave'] = 'adm_grupo.id';
        $not_in['values'] = $adm_grupos_ids;

        $select_adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->select_adm_grupo_id(
            cols:12,con_registros: true,id_selected:  -1,link:  $this->link, not_in: $not_in, required: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }


        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_menu_id = $select_adm_menu_id;
        $this->inputs->select->adm_seccion_id = $select_adm_seccion_id;
        $this->inputs->select->adm_accion_id = $select_adm_accion_id;
        $this->inputs->select->adm_grupo_id = $select_adm_grupo_id;

        return $this->inputs;
    }

    public function modifica(
        bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }


        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'adm_menu_id',
            keys_selects: array(), id_selected: $this->registro['adm_menu_id'], label: 'Menu');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro: array('adm_menu.id'=>$this->registro['adm_menu_id']), key: 'adm_seccion_id',
            keys_selects: $keys_selects, id_selected: $this->registro['adm_seccion_id'], label: 'Seccion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $base = $this->base_upd(keys_selects: $keys_selects, not_actions: array(__FUNCTION__), params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }


        return $r_modifica;
    }

    public function modifica_bd(bool $header, bool $ws = false): array|stdClass
    {
        if(isset($_POST['adm_menu_id'])){
            unset($_POST['adm_menu_id']);
        }

        $r_modifica_bd = parent::modifica_bd($header, $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar accion',data:  $r_modifica_bd, header: false,ws: false);
        }
        return $r_modifica_bd;

    }

}

<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_seccion;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\system\out_permisos;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_accion_html;
use html\adm_menu_html;
use html\adm_seccion_html;
use links\secciones\link_adm_seccion;
use PDO;
use stdClass;
use Throwable;


class controlador_adm_seccion extends _ctl_base {

    public string $link_adm_accion_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_seccion(link: $link);

        $html_ = new adm_seccion_html(html: $html);
        $obj_link = new link_adm_seccion(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_seccion_id']['titulo'] = 'Id';
        $datatables->columns['adm_seccion_codigo']['titulo'] = 'Cod';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Seccion';
        $datatables->columns['adm_menu_descripcion']['titulo'] = 'Menu';
        $datatables->columns['adm_seccion_n_acciones']['titulo'] = 'N Acciones';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables: $datatables, paths_conf: $paths_conf);



        $this->titulo_lista = 'Secciones';

        $link_adm_accion_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'adm_accion');
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

        $names = array('Id','Accion', 'Titulo','CSS','Acciones');
        $thead = (new html_controler(html: $this->html_base))->thead(names: $names);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener thead',data:  $thead, header: $header,ws:  $ws);
        }

        $this->thead = $thead;




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

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }


        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion');
        $keys->selects = array();

        $init_data = array();
        $init_data['adm_menu'] = "gamboamartin\\administrador";
        $selects = (new \base\controller\init())->select_key_input($init_data, selects: $keys->selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar select',data:  $selects);
        }

        $keys->selects = $selects;

        $campos_view = (new \base\controller\init())->model_init_campos_template(
            campos_view: array(),keys:  $keys, link: $this->link);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function elimina_bd(bool $header, bool $ws): array|stdClass
    {
        $this->link->beginTransaction();
        $r_elimina_bd = parent::elimina_bd(header: false,ws:  false); // TODO: Change the autogenerated stub
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al eliminar registro',data:  $r_elimina_bd,header: $header,ws: $ws);
        }
        $this->link->commit();

        $next_seccion = 'adm_seccion';
        $next_accion = 'lista';
        $id_retorno = -1;

        if(isset($_GET['next_seccion'])){
            $next_seccion = $_GET['next_seccion'];
        }
        if(isset($_GET['next_accion'])){
            $next_accion = $_GET['next_accion'];
        }
        if(isset($_GET['id_retorno'])){
            $id_retorno = $_GET['id_retorno'];
        }

        $header_retorno = "index.php?seccion=$next_seccion&accion=$next_accion&session_id=$this->session_id&registro_id=$id_retorno";

        if($header){
            header("Location: $header_retorno");
            exit;
        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($r_elimina_bd, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                $error = $this->errores->error(mensaje: 'Error al dar salida json', data: $e);
                print_r($error);
                exit;
            }
            exit;
        }

        return $r_elimina_bd;
    }

    public function get_adm_seccion(bool $header, bool $ws = true): array|stdClass
    {

        $keys['adm_menu'] = array('id','descripcion','codigo','codigo_bis');
        $keys['adm_seccion'] = array('id','descripcion','codigo','codigo_bis');


        $salida = $this->get_out(header: $header,keys: $keys, ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar salida',data:  $salida,header: $header,ws: $ws);

        }


        return $salida;


    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'codigo', keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Seccion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
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

        $base = $this->base_upd(keys_selects: $keys_selects, not_actions: array(__FUNCTION__));
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }




        return $r_modifica;
    }


}

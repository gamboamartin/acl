<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_accion_grupo;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\template_1\html;
use html\adm_accion_grupo_html;
use links\secciones\link_adm_accion_grupo;

use PDO;
use stdClass;
use Throwable;


class controlador_adm_accion_grupo extends _ctl_base {

    public array $secciones = array();
    public stdClass|array $adm_accion_grupo = array();
    public string $link_adm_accion_grupo_alta_bd = '';


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_accion_grupo(link: $link);

        $html_ = new adm_accion_grupo_html(html: $html);
        $obj_link = new link_adm_accion_grupo(link: $link,registro_id:  $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_accion_grupo_id']['titulo'] = 'Id';
        $datatables->columns['adm_accion_descripcion']['titulo'] = 'Accion';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Seccion';
        $datatables->columns['adm_menu_descripcion']['titulo'] = 'Menu';
        $datatables->columns['adm_grupo_descripcion']['titulo'] = 'Grupo';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_accion_grupo.id';
        $datatables->filtro[] = 'adm_accion.descripcion';
        $datatables->filtro[] = 'adm_seccion.descripcion';
        $datatables->filtro[] = 'adm_menu.descripcion';
        $datatables->filtro[] = 'adm_grupo.descripcion';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Permisos';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $adm_accion_grupo = (new adm_accion_grupo($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener adm_accion_grupo',data:  $adm_accion_grupo);
                print_r($error);
                exit;
            }
            $this->adm_accion_grupo = $adm_accion_grupo;
        }

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

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'adm_menu_id',
            keys_selects: array(), id_selected: -1, label: 'Menu');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: false,filtro:  array(), key: 'adm_seccion_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Seccion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: false,filtro:  array(), key: 'adm_accion_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Accion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'adm_grupo_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Grupo');
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

    public function alta_bd(bool $header, bool $ws = false): array|stdClass
    {

        $this->link->beginTransaction();
        if(isset($_POST['adm_menu_id'])){
            unset($_POST['adm_menu_id']);
        }
        if(isset($_POST['adm_seccion_id'])){
            unset($_POST['adm_seccion_id']);
        }

        $_POST['btn_action_next'] = 'lista';

        $data_retorno = $this->data_retorno_base();
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener datos de retorno', data: $data_retorno,
                header:  $header, ws: $ws);
        }

        $r_alta_bd = parent::alta_bd(header:false); // TODO: Change the autogenerated stub
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar accion',data:  $r_alta_bd, header: $header,ws: $ws);
        }

        $this->link->commit();

        if($header){

            if($data_retorno->id_retorno === -1) {
                $data_retorno->id_retorno = $r_alta_bd->registro_id;
            }
            $this->retorno_base(
                registro_id:$data_retorno->id_retorno, result: $r_alta_bd,
                siguiente_view: $data_retorno->siguiente_view, ws:  $ws,seccion_retorno: $data_retorno->seccion_retorno);


        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($r_alta_bd, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON' , data: $e);
                print_r($error);
            }
            exit;
        }
        $r_alta_bd->siguiente_view = $data_retorno->siguiente_view;

        return $r_alta_bd;

    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();

        $init_data = array();
        $init_data['adm_menu'] = "gamboamartin\\administrador";
        $init_data['adm_seccion'] = "gamboamartin\\administrador";
        $init_data['adm_accion'] = "gamboamartin\\administrador";
        $init_data['adm_grupo'] = "gamboamartin\\administrador";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }

        return $campos_view;
    }




}

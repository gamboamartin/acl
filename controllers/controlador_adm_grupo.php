<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_grupo;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_parent;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\template_1\html;
use html\adm_accion_html;
use html\adm_grupo_html;
use html\adm_menu_html;
use html\adm_seccion_html;
use html\adm_usuario_html;
use links\secciones\link_adm_grupo;
use PDO;
use stdClass;


class controlador_adm_grupo extends _ctl_parent_sin_codigo {

    public array $secciones = array();
    public stdClass|array $adm_grupo = array();
    public array $adm_acciones_grupo = array();
    public string $link_adm_usuario_alta_bd = '';
    public string $link_adm_accion_grupo_alta_bd = '';
    public array $adm_usuarios = array();

    public function __construct(PDO $link, html $html = new html(), array $datatables_custom_cols = array(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_grupo(link: $link);

        $html_ = new adm_grupo_html(html: $html);
        $obj_link = new link_adm_grupo(link: $link,registro_id:  $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_grupo_id']['titulo'] = 'Id';
        $datatables->columns['adm_grupo_descripcion']['titulo'] = 'Grupo';
        $datatables->columns['adm_grupo_n_permisos']['titulo'] = 'N Permisos';
        $datatables->columns['adm_grupo_n_usuarios']['titulo'] = 'N Usuarios';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_grupo.id';
        $datatables->filtro[] = 'adm_grupo.descripcion';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables_custom_cols: $datatables_custom_cols, datatables: $datatables, paths_conf: $paths_conf);

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

        $link_adm_usuario_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'adm_usuario');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_usuario_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_usuario_alta_bd = $link_adm_usuario_alta_bd;

        $link_adm_accion_grupo_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'adm_accion_grupo');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_accion_grupo_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_accion_grupo_alta_bd = $link_adm_accion_grupo_alta_bd;

    }

    public function asigna_permiso(bool $header = true, bool $ws = false): array|string{


        $contenido = (new _ctl_permiso())->asigna_permiso(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener contenido',data:  $contenido, header: $header,ws:  $ws);
        }


        return $contenido;
    }

    protected function inputs_children(stdClass $registro): stdClass|array
    {
        $select_adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->select_adm_grupo_id(
            cols:12,con_registros: true,id_selected: $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:6,con_registros: true,id_selected: -1,link:  $this->link);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:6,con_registros: false,id_selected: -1,link:  $this->link);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $select_adm_accion_id = (new adm_accion_html(html: $this->html_base))->select_adm_accion_id(
            cols:12,con_registros: false,id_selected: -1,link:  $this->link);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $adm_usuario_user = (new adm_usuario_html(html: $this->html_base))->input_user(6, new stdClass(), false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $adm_usuario_password = (new adm_usuario_html(html: $this->html_base))->input_password(6, new stdClass(), false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $adm_usuario_email = (new adm_usuario_html(html: $this->html_base))->input_email(6, new stdClass(), false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $adm_usuario_telefono = (new adm_usuario_html(html: $this->html_base))->input_telefono(6, new stdClass(), false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id);
        }

        $adm_usuario_nombre = (new adm_usuario_html(html: $this->html_base))->input_nombre(12, new stdClass(), false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener adm_usuario_nombre',data:  $adm_usuario_nombre);
        }

        $adm_usuario_ap = (new adm_usuario_html(html: $this->html_base))->input_ap(6, new stdClass(), false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener adm_usuario_ap',data:  $adm_usuario_ap);
        }

        $adm_usuario_am = (new adm_usuario_html(html: $this->html_base))->input_am(6, new stdClass(), false);

        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener adm_usuario_am',data:  $adm_usuario_am);
        }


        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();

        $this->inputs->select->adm_grupo_id = $select_adm_grupo_id;
        $this->inputs->select->adm_menu_id = $select_adm_menu_id;
        $this->inputs->select->adm_seccion_id = $select_adm_seccion_id;
        $this->inputs->select->adm_accion_id = $select_adm_accion_id;

        $this->inputs->adm_usuario_user = $adm_usuario_user;
        $this->inputs->adm_usuario_password = $adm_usuario_password;
        $this->inputs->adm_usuario_email = $adm_usuario_email;
        $this->inputs->adm_usuario_telefono = $adm_usuario_telefono;
        $this->inputs->adm_usuario_nombre = $adm_usuario_nombre;
        $this->inputs->adm_usuario_ap = $adm_usuario_ap;
        $this->inputs->adm_usuario_am = $adm_usuario_am;
        return $this->inputs;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'codigo', keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Grupo');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function usuarios(bool $header = true, bool $ws = false): array|string
    {
        $data_view = new stdClass();
        $data_view->names = array('Id','User','Email','Telefono','Grupo','Acciones');
        $data_view->keys_data = array('adm_usuario_id','adm_usuario_user','adm_usuario_email',
            'adm_usuario_telefono','adm_grupo_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\administrador\\models';
        $data_view->name_model_children = 'adm_usuario';

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;


    }


}

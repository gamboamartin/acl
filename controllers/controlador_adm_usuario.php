<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_usuario;

use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\template_1\html;
use html\adm_usuario_html;
use links\secciones\link_adm_usuario;
use PDO;
use stdClass;


class controlador_adm_usuario extends _ctl_base {

    public string $link_adm_accion_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_usuario(link: $link);

        $html_ = new adm_usuario_html(html: $html);
        $obj_link = new link_adm_usuario(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_usuario_id']['titulo'] = 'Id';
        $datatables->columns['adm_usuario_user']['titulo'] = 'User';
        $datatables->columns['adm_usuario_email']['titulo'] = 'Email';
        $datatables->columns['adm_usuario_telefono']['titulo'] = 'Telefono';
        $datatables->columns['adm_grupo_descripcion']['titulo'] = 'Grupo';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables: $datatables, paths_conf: $paths_conf);

        $this->titulo_lista = 'Usuarios';

    }

    public function alta(bool $header, bool $ws = false): array|string
    {


        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'adm_grupo_id',
            keys_selects: array(), id_selected: -1, label: 'Grupo');
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
        $keys->inputs = array('user','email','telefono');
        $keys->passwords = array('password');
        $keys->selects = array();

        $init_data = array();
        $init_data['adm_grupo'] = "gamboamartin\\administrador";
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

    public function modifica(
        bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'adm_grupo_id',
            keys_selects: array(), id_selected: $this->registro['adm_grupo_id'], label: 'Grupo');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }


        $base = $this->base_upd(keys_selects: $keys_selects, not_actions: array(__FUNCTION__), params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }


        return $r_modifica;
    }




}

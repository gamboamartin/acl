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
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_grupo_html;
use html\adm_usuario_html;
use links\secciones\link_adm_grupo;
use models\adm_grupo;
use models\adm_usuario;
use PDO;
use stdClass;


class controlador_adm_grupo extends system {

    public array $secciones = array();
    public stdClass|array $adm_grupo = array();
    public string $link_adm_usuario_alta_bd = '';
    public array $adm_usuarios = array();


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_grupo(link: $link);

        $html_ = new adm_grupo_html(html: $html);
        $obj_link = new link_adm_grupo($this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_grupo_id']['titulo'] = 'Id';
        $datatables->columns['adm_grupo_descripcion']['titulo'] = 'Descripcion';
        $datatables->columns['adm_grupo_codigo']['titulo'] = 'Codigo';
        $datatables->columns['adm_grupo_codigo_bis']['titulo'] = 'Codigo BIS';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

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

        $link_adm_usuario_alta_bd = $this->obj_link->link_alta_bd(seccion: 'adm_usuario');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_usuario_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_usuario_alta_bd = $link_adm_usuario_alta_bd;

    }

    public function usuarios(bool $header = true, bool $ws = false){
        if($this->registro_id<=0){
            return $this->errores->error(mensaje: 'Error this->registro_id debe ser mayor a 0',
                data:  $this->registro_id);
        }
        $adm_grupo = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener adm_grupo',data:  $adm_grupo);
        }

        $select_adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->select_adm_grupo_id(
            cols:12,con_registros: true,id_selected:  $adm_grupo->adm_grupo_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_grupo_id',data:  $select_adm_grupo_id, header: $header,ws:  $ws);
        }

        $adm_usuario_user = (new adm_usuario_html(html: $this->html_base))->input_user(
            cols:6,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_usuario_user',data:  $adm_usuario_user, header: $header,ws:  $ws);
        }

        $adm_usuario_password = (new adm_usuario_html(html: $this->html_base))->input_password(
            cols:6,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_usuario_password',data:  $adm_usuario_password, header: $header,ws:  $ws);
        }

        $adm_usuario_email = (new adm_usuario_html(html: $this->html_base))->input_email(
            cols:6,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_usuario_email',data:  $adm_usuario_email, header: $header,ws:  $ws);
        }

        $adm_usuario_telefono = (new adm_usuario_html(html: $this->html_base))->input_telefono(
            cols:6,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_usuario_telefono',data:  $adm_usuario_telefono, header: $header,ws:  $ws);
        }

        $hidden_adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->hidden(name: 'adm_grupo_id', value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_adm_seccion_id',data:  $hidden_adm_grupo_id, header: $header,ws:  $ws);
        }

        $hidden_seccion_retorno = (new adm_grupo_html(html: $this->html_base))->hidden(name: 'seccion_retorno', value: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_seccion_retorno',data:  $hidden_seccion_retorno, header: $header,ws:  $ws);
        }
        $hidden_id_retorno = (new adm_grupo_html(html: $this->html_base))->hidden(name: 'id_retorno', value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_id_retorno',data:  $hidden_id_retorno, header: $header,ws:  $ws);
        }

        $adm_usuarios = (new adm_usuario($this->link))->usuarios_por_grupo(adm_grupo_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_usuarios',data:  $adm_usuarios, header: $header,ws:  $ws);
        }

        $adm_usuarios = $this->rows_con_permisos(key_id:  'adm_usuario_id',rows:  $adm_usuarios,seccion: 'adm_usuario');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar link',data:  $adm_usuarios, header: $header,ws:  $ws);
        }

        $this->adm_usuarios = $adm_usuarios;

        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();
        $this->inputs->select->adm_grupo_id = $select_adm_grupo_id;
        $this->inputs->adm_usuario_user = $adm_usuario_user;
        $this->inputs->adm_usuario_password = $adm_usuario_password;
        $this->inputs->adm_usuario_email = $adm_usuario_email;
        $this->inputs->adm_usuario_telefono = $adm_usuario_telefono;
        $this->inputs->hidden_adm_grupo_id = $hidden_adm_grupo_id;
        $this->inputs->hidden_seccion_retorno = $hidden_seccion_retorno;
        $this->inputs->hidden_id_retorno = $hidden_id_retorno;


    }




}

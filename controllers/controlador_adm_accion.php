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
use gamboamartin\administrador\models\adm_accion_grupo;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_accion_html;
use html\adm_grupo_html;
use html\adm_menu_html;
use html\adm_seccion_html;
use links\secciones\link_adm_accion;
use PDO;
use stdClass;
use Throwable;


class controlador_adm_accion extends system {

    public string $link_adm_accion_grupo_alta_bd = '';
    public array $adm_acciones_grupo = array();

    public function __construct(PDO $link, html $html = new html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_accion(link: $link);

        $html_ = new adm_accion_html(html: $html);
        $obj_link = new link_adm_accion(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_accion_id']['titulo'] = 'Id';
        $datatables->columns['adm_accion_codigo']['titulo'] = 'Cod';
        $datatables->columns['adm_accion_descripcion']['titulo'] = 'Accion';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Seccion';
        $datatables->columns['adm_menu_descripcion']['titulo'] = 'Menu';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_accion.id';
        $datatables->filtro[] = 'adm_accion.codigo';
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

    public function asigna_permiso(bool $header = true, bool $ws = false): array|stdClass{

        if($this->registro_id<=0){
            return $this->errores->error(mensaje: 'Error this->registro_id debe ser mayor a 0',
                data:  $this->registro_id);
        }

        $adm_accion = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener adm_accion',data:  $adm_accion);
        }

        $select_adm_menu_id = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(
            cols:4,con_registros: true,id_selected:  $adm_accion->adm_menu_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_menu_id',data:  $select_adm_menu_id, header: $header,ws:  $ws);
        }

        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:4,con_registros: true,id_selected:  $adm_accion->adm_seccion_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_seccion_id',data:  $select_adm_seccion_id, header: $header,ws:  $ws);
        }

        $select_adm_accion_id = (new adm_accion_html(html: $this->html_base))->select_adm_accion_id(
            cols:4,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_accion_id, header: $header,ws:  $ws);
        }


        $adm_grupos_ids = (new adm_accion(link: $this->link))->grupos_id_por_accion(adm_accion_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener adm_grupos_ids',data:  $adm_grupos_ids, header: $header,ws:  $ws);
        }

        $not_in['llave'] = 'adm_grupo.id';
        $not_in['values'] = $adm_grupos_ids;

        $select_adm_grupo_id = (new adm_grupo_html(html: $this->html_base))->select_adm_grupo_id(
            cols:12,con_registros: true,id_selected:  -1,link:  $this->link, not_in: $not_in, required: true);

        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener select_adm_accion_id',data:  $select_adm_grupo_id, header: $header,ws:  $ws);
        }

        $hidden_adm_accion_id = (new adm_accion_html(html: $this->html_base))->hidden(name: 'adm_accion_id', value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener hidden_adm_menu_id',data:  $hidden_adm_accion_id, header: $header,ws:  $ws);
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
        $this->inputs->select->adm_accion_id = $select_adm_accion_id;
        $this->inputs->select->adm_grupo_id = $select_adm_grupo_id;
        $this->inputs->hidden_adm_accion_id = $hidden_adm_accion_id;
        $this->inputs->hidden_seccion_retorno = $retornos->hidden_seccion_retorno;
        $this->inputs->hidden_id_retorno = $retornos->hidden_id_retorno;


        $adm_acciones_grupo = (new adm_accion_grupo($this->link))->grupos_por_accion(adm_accion_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener adm_acciones_grupo',data:  $adm_acciones_grupo, header: $header,ws:  $ws);
        }

        $adm_acciones_grupo = $this->rows_con_permisos(key_id:  'adm_accion_grupo_id',rows:  $adm_acciones_grupo,seccion: 'adm_accion_grupo');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar links',data:  $adm_acciones_grupo, header: $header,ws:  $ws);
        }

        $this->adm_acciones_grupo = $adm_acciones_grupo;


        return $adm_accion;
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



}

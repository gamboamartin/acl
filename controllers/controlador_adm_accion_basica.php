<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\administrador\models\adm_accion_basica;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\template_1\html;
use html\adm_accion_basica_html;
use links\secciones\link_adm_accion_basica;
use PDO;
use stdClass;


class controlador_adm_accion_basica extends _ctl_parent_sin_codigo {


    public stdClass|array $adm_accion_basica = array();


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_accion_basica(link: $link);

        $html_ = new adm_accion_basica_html(html: $html);
        $obj_link = new link_adm_accion_basica(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_accion_basica_id']['titulo'] = 'Id';
        $datatables->columns['adm_accion_basica_descripcion']['titulo'] = 'Accion';
        $datatables->columns['adm_accion_basica_css']['titulo'] = 'CSS';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_accion_basica.id';
        $datatables->filtro[] = 'adm_accion_basica.descripcion';
        $datatables->filtro[] = 'adm_accion_basica.css';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Acciones Basicas';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $adm_accion_basica = (new adm_accion_basica($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener adm_accion_basica',data:  $adm_accion_basica);
                print_r($error);
                exit;
            }
            $this->adm_accion_basica = $adm_accion_basica;
        }

    }

    protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('css','descripcion');
        $keys->selects = array();



        $campos_view = (new \base\controller\init())->model_init_campos_template(
            campos_view: array(),keys:  $keys, link: $this->link);

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

    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Accion Base');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'css', keys_selects:$keys_selects, place_holder: 'CSS');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }



}

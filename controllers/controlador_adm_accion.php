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
use html\adm_accion_html;
use JsonException;
use links\secciones\link_adm_accion;
use models\adm_accion;
use PDO;
use stdClass;


class controlador_adm_accion extends system {

    public string $link_adm_accion_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_accion(link: $link);

        $html_ = new adm_accion_html(html: $html);
        $obj_link = new link_adm_accion($this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_accion_id']['titulo'] = 'Id';
        $datatables->columns['adm_accion_codigo']['titulo'] = 'Cod';
        $datatables->columns['adm_accion_descripcion']['titulo'] = 'Seccion';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Menu';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables: $datatables, paths_conf: $paths_conf);



        $this->titulo_lista = 'Acciones';


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



}

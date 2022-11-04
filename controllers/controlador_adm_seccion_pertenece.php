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
use gamboamartin\system\actions;
use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_seccion_pertenece_html;
use links\secciones\link_adm_seccion_pertenece;
use models\adm_accion_grupo;
use models\adm_seccion_pertenece;
use PDO;
use stdClass;


class controlador_adm_seccion_pertenece extends system {


    public  array|stdClass $adm_seccion_pertenece = array();
    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_seccion_pertenece(link: $link);

        $html_ = new adm_seccion_pertenece_html(html: $html);
        $obj_link = new link_adm_seccion_pertenece(link: $link,registro_id:  $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_seccion_pertenece_id']['titulo'] = 'Id';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Seccion';
        $datatables->columns['adm_sistema_descripcion']['titulo'] = 'Sistema';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Secciones de Sistema';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $adm_seccion_pertenece = (new adm_seccion_pertenece($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener adm_seccion_pertenece',data:  $adm_seccion_pertenece);
                print_r($error);
                exit;
            }
            $this->adm_seccion_pertenece = $adm_seccion_pertenece;
        }


    }

    public function alta_bd(bool $header, bool $ws = false): array|stdClass
    {

        $transaccion_previa = false;
        if($this->link->inTransaction()){
            $transaccion_previa = true;
        }
        if(!$transaccion_previa) {
            $this->link->beginTransaction();
        }
        $siguiente_view = (new actions())->init_alta_bd();
        if(errores::$error){
            if(!$transaccion_previa) {
                $this->link->rollBack();
            }
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header:  $header, ws: $ws);
        }


        $seccion_retorno = $this->tabla;
        if(isset($_POST['seccion_retorno'])){
            $seccion_retorno = $_POST['seccion_retorno'];
            unset($_POST['seccion_retorno']);
        }
        $id_retorno = -1;
        if(isset($_POST['id_retorno'])){
            $id_retorno = $_POST['id_retorno'];
            unset($_POST['id_retorno']);
        }

        if(isset($_POST['adm_menu_id'])){
            unset($_POST['adm_menu_id']);
        }

        $r_alta_bd = parent::alta_bd(header: false,ws: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            if(!$transaccion_previa) {
                $this->link->rollBack();
            }
            return $this->retorno_error(mensaje: 'Error al insertar seccion pertenece',data:  $r_alta_bd, header: $header,ws: $ws);
        }
        if(!$transaccion_previa) {
          //  $this->link->commit();
        }


        if($header){
            if($id_retorno === -1) {
                $id_retorno = $r_alta_bd->registro_id;
            }

            $this->retorno_base(registro_id:$id_retorno, result: $r_alta_bd, siguiente_view: $siguiente_view,
                ws:  $ws,seccion_retorno: $seccion_retorno);

        }
        if($ws){
            header('Content-Type: application/json');
            echo json_encode($r_alta_bd, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_alta_bd->siguiente_view = $siguiente_view;
        return $r_alta_bd;


    }


}

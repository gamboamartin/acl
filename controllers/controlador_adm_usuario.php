<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\system\system;
use gamboamartin\template_1\html;
use html\adm_usuario_html;
use links\secciones\link_adm_usuario;
use models\adm_usuario;
use PDO;
use stdClass;


class controlador_adm_usuario extends system {

    public string $link_adm_accion_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_usuario(link: $link);

        $html_ = new adm_usuario_html(html: $html);
        $obj_link = new link_adm_usuario($this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_usuario_id']['titulo'] = 'Id';
        $datatables->columns['adm_usuario_user']['titulo'] = 'User';
        $datatables->columns['adm_usuario_email']['titulo'] = 'Email';
        $datatables->columns['adm_usuario_password']['titulo'] = 'Password';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables: $datatables, paths_conf: $paths_conf);



        $this->titulo_lista = 'Usuarios';


    }




}

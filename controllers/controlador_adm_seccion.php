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
use html\adm_seccion_html;
use links\secciones\link_adm_seccion;
use models\adm_seccion;
use PDO;
use stdClass;


class controlador_adm_seccion extends system {


    public function __construct(PDO $link, html $html = new html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new adm_seccion(link: $link);

        $html_ = new adm_seccion_html(html: $html);
        $obj_link = new link_adm_seccion($this->registro_id);

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Secciones';

    }


}

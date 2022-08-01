<?php
namespace html;

use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use models\adm_menu;
use PDO;


class adm_menu_html extends html_controler {


    public function select_adm_menu_id(int $cols, bool $con_registros, int $id_selected, PDO $link): array|string
    {
        $modelo = new adm_menu($link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, key_descripcion_select: 'adm_menu_descripcion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}

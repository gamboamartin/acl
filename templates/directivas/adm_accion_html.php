<?php
namespace html;

use gamboamartin\controllers\controlador_adm_accion;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use models\adm_accion;
use PDO;
use stdClass;


class adm_accion_html extends html_controler {

    private function asigna_inputs(controlador_adm_accion $controler, stdClass $inputs): array|stdClass
    {
        $controler->inputs->select = new stdClass();

        $controler->inputs->select->adm_menu_id = $inputs->selects->adm_menu_id;


        return $controler->inputs;
    }



    public function genera_inputs_alta(controlador_adm_accion $controler, array $keys_selects,PDO $link): array|stdClass
    {
        $inputs = $this->init_alta(keys_selects: $keys_selects, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar inputs',data:  $inputs);

        }
        $inputs_asignados = $this->asigna_inputs(controler:$controler, inputs: $inputs);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar inputs',data:  $inputs_asignados);
        }

        return $inputs_asignados;
    }



    protected function init_alta(array $keys_selects, PDO $link): array|stdClass
    {
        $selects = $this->selects_alta(keys_selects: $keys_selects, link:  $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar selects',data:  $selects);
        }

        $alta_inputs = new stdClass();
        $alta_inputs->selects = $selects;

        return $alta_inputs;
    }

    public function input_titulo(int $cols, stdClass $row_upd, bool $value_vacio, bool $disabled = false,
                                      string $place_holder = 'Titulo'): array|string
    {

        if($cols<=0){
            return $this->error->error(mensaje: 'Error cold debe ser mayor a 0', data: $cols);
        }
        if($cols>=13){
            return $this->error->error(mensaje: 'Error cold debe ser menor o igual a  12', data: $cols);
        }

        $html =$this->directivas->input_text_required(disable: $disabled,name: 'titulo',place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }


    public function select_adm_accion_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                          bool $disabled = false): array|string
    {
        $modelo = new adm_accion($link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo, disabled: $disabled,label: 'Accion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


    /**
     * Genera los selectores de una seccion
     * @param array $keys_selects keys de select
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     * @version 0.18.0
     */
    protected function selects_alta(array $keys_selects, PDO $link): array|stdClass
    {
        $selects = new stdClass();

        $select = (new adm_menu_html(html: $this->html_base))->select_adm_menu_id(cols: 12,
            con_registros:true, id_selected:-1,link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);

        }
        $selects->adm_menu_id = $select;

        $select = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(cols: 12,
            con_registros:true, id_selected:-1,link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select',data:  $select);

        }
        $selects->adm_menu_id = $select;

        return $selects;
    }

}

<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\acl\controllers;

use gamboamartin\acl\controllers\_ctl_base\init;
use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\system\out_permisos;
use gamboamartin\system\system;
use gamboamartin\system\table;
use gamboamartin\validacion\validacion;
use stdClass;



class _ctl_base extends system{

    protected string $key_id_filter = '';
    protected string $key_id_row = '';
    public array $childrens;

    /**
     * Integra los campos view de una vista para alta y modifica Metodo para sobreescribir
     * @return array
     * @version 0.73.1
     */
    protected function campos_view(): array
    {
        return array();
    }

    /**
     * Integra los elementos base de una view
     * @return array|$this
     * @version 0.73.1
     */
    private function base(): array|static
    {

        $campos_view = $this->campos_view();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar campos_view',data:  $campos_view);
        }

        $this->modelo->campos_view = $campos_view;



        $this->inputs = new stdClass();
        $this->inputs->select = new stdClass();


        return $this;
    }

    protected function contenido_children(stdClass $data_view, string $next_accion): array|string
    {

        $params = array();
        $params['next_seccion'] = $this->tabla;
        $params['next_accion'] = $next_accion;
        $params['id_retorno'] = $this->registro_id;

        $childrens = $this->children_data(
            namespace_model: $data_view->namespace_model, name_model_children: $data_view->name_model_children, params: $params);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar inputs',data:  $childrens);
        }

        $class_css_table = array('table','table-striped');
        $id_css_table = array($data_view->name_model_children);

        $contenido_table = (new table())->table(childrens: $childrens, cols_actions: 4, data_view: $data_view,
            class_css_table: $class_css_table, id_css_table:$id_css_table );
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener tbody',data:  $contenido_table);
        }
        $this->contenido_table = $contenido_table;
        return $contenido_table;
    }

    protected function children_data(string $namespace_model, string $name_model_children, array $params): array
    {
        $inputs = $this->children_base();
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al generar inputs',data:  $inputs);
        }

        $childrens = $this->childrens(namespace_model: $namespace_model,
            name_model_children: $name_model_children, params: $params, registro_id: $this->registro_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar links',data:  $childrens);
        }

        $this->childrens = $childrens;
        return $this->childrens;
    }

    protected function children_base(): array|stdClass
    {
        $registro = $this->init_data_children();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar registro',data:  $registro);
        }

        $inputs = $this->inputs_children(registro: $registro);
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al generar inputs',data:  $inputs);
        }

        $retornos = $this->input_retornos();
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener retornos',data:  $retornos);
        }

        return $inputs;
    }

    protected function childrens(string $namespace_model, string $name_model_children, array $params, int $registro_id): array
    {
        $this->key_id_filter = $this->tabla.'.id';
        $filtro = array();
        $filtro[$this->key_id_filter] = $registro_id;

        $model_children = $this->modelo->genera_modelo(modelo: $name_model_children,namespace_model: $namespace_model);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar modelo',data:  $model_children);
        }

        $r_children = $model_children->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener r_children',data:  $r_children);
        }
        $childrens = $r_children->registros;

        $key_id = $name_model_children.'_id';
        $childrens = $this->rows_con_permisos(key_id:  $key_id, rows:  $childrens,seccion: $name_model_children, params: $params);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar link',data:  $childrens);
        }

        return $childrens;
    }

    protected function base_upd(array $keys_selects, array $not_actions, array $params, array $params_ajustados): array|stdClass
    {

        if(count($params) === 0){
            $params = (new init())->params(controler: $this,params:  $params);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al asignar params', data: $params);
            }
        }

        if(count($params_ajustados) === 0) {
            $params_ajustados['elimina_bd']['next_seccion'] = $this->tabla;
            $params_ajustados['elimina_bd']['next_accion'] = 'lista';
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $this->buttons = array();
        $buttons = (new out_permisos())->buttons_view(controler:$this, not_actions: $not_actions, params: $params, params_ajustados: $params_ajustados);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar botones',data:  $buttons);
        }

        $data = new stdClass();
        $data->buttons = $buttons;
        $data->inputs = $inputs;
        $this->buttons = $buttons;
        return $data;
    }

    /**
     * Inicializa loe elementos para un alta
     * @return array|stdClass|string
     * @version 0.73.1
     */
    protected function init_alta(): array|stdClass|string
    {

        $r_template = parent::alta(header:false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener template',data:  $r_template);
        }

        $base = $this->base();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera base',data:  $base);
        }

        return $r_template;
    }

    /**
     * Inicializa los elementos de datos de un children para una view
     * @return array|stdClass
     * @version 0.101.4
     */
    protected function init_data_children(): array|stdClass
    {
        if($this->registro_id<=0){
            return $this->errores->error(mensaje: 'Error this->registro_id debe ser mayor a 0',
                data:  $this->registro_id);
        }

        $registro = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener registro',data:  $registro);
        }

        $this->key_id_row = $this->tabla.'_id';

        return $registro;
    }

    /**
     * Inicializa upd base view
     * @return array|stdClass|string
     * @version 0.74.1
     */
    protected function init_modifica(): array|stdClass|string
    {
        if($this->registro_id<=0){
            return $this->errores->error(mensaje: 'Error registro_id debe ser mayor a 0', data: $this->registro_id);
        }

        $r_template = parent::modifica(header: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener template',data:  $r_template);
        }

        $base = $this->base();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera base',data:  $base);
        }
        return $r_template;
    }

    /**
     * Debe star sobreescrito en el controlador integrando todos los selects necesarios
     * @param stdClass $registro
     * @return stdClass|array
     * @version 0.103.5
     */
    protected function inputs_children(stdClass $registro): stdClass|array
    {

        return new stdClass();
    }

    protected function input_retornos(): array|stdClass
    {
        $retornos = (new html_controler(html: $this->html_base))->retornos(registro_id: $this->registro_id,tabla:  $this->tabla);
        if(errores::$error){
            return $this->errores->error(
                mensaje: 'Error al obtener retornos',data:  $retornos);
        }

        $hidden_input_id = (new html_controler(html: $this->html_base))->hidden(name: $this->key_id_row, value: $this->registro_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener hidden_input_id',data:  $hidden_input_id);
        }

        $this->inputs->hidden_row_id = $hidden_input_id;
        $this->inputs->hidden_seccion_retorno = $retornos->hidden_seccion_retorno;
        $this->inputs->hidden_id_retorno = $retornos->hidden_id_retorno;
        return $this->inputs;
    }

    /**
     * Integra los parametros de un key para select
     * @param int $cols N cols css
     * @param bool $con_registros integra rows en opciones si es true
     * @param array $filtro Filtro para result
     * @param string $key Name input
     * @param array $keys_selects keys precargados
     * @param int|null $id_selected Identificador para selected
     * @param string $label Etiqueta a mostrar
     * @return array
     * @version 0.78.1
     */
    protected function key_select(int $cols, bool $con_registros, array $filtro,string $key, array $keys_selects,
                                  int|null $id_selected, string $label): array
    {
        $key = trim($key);
        if($key === ''){
            return $this->errores->error(mensaje: 'Error key esta vacio',data:  $key);
        }
        $valida = (new validacion())->valida_cols_css(cols: $cols);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar cols',data:  $valida);
        }

        $label = trim($label);
        if($label === ''){
            $label = trim($key);
            $label = str_replace('_', ' ', $label);
            $label = ucwords($label);
        }

        $keys_selects[$key] = new stdClass();
        $keys_selects[$key]->cols = $cols;
        $keys_selects[$key]->con_registros = $con_registros;
        $keys_selects[$key]->label = $label;
        $keys_selects[$key]->id_selected = $id_selected;
        $keys_selects[$key]->filtro = $filtro;
        return $keys_selects;
    }

}

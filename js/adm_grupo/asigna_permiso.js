let url = getAbsolutePath();
let registro_id = getParameterByName('registro_id');
let session_id = getParameterByName('session_id');

let sl_adm_menu_id = $("#adm_menu_id");
let sl_adm_seccion_id = $("#adm_seccion_id");
let adm_menu_id = sl_adm_menu_id.val();
let adm_seccion_id = sl_adm_seccion_id.val();

sl_adm_menu_id.change(function(){
    adm_menu_id = $(this).val();
    adm_asigna_secciones(adm_menu_id);
});

sl_adm_seccion_id.change(function(){
    adm_seccion_id = $(this).val();
    adm_asigna_acciones(adm_seccion_id);
});



function adm_asigna_acciones(adm_seccion_id = ''){

    let url_acciones_id_por_grupo = "index.php?seccion=adm_accion&ws=1&accion=acciones_id_por_grupo&adm_grupo_id="+registro_id+"&adm_seccion_id="+adm_seccion_id+"&session_id="+session_id;
    let adm_acciones_id_aut = [];
    $.ajax({
        type: 'POST',  // Envío con método POST
        url: url_acciones_id_por_grupo,  // Fichero destino (el PHP que trata los datos)

    }).done(function( msg ) {  // Función que se ejecuta si todo ha ido bien
        console.log(msg)
        console.log(url_acciones_id_por_grupo)
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        // Mostramos en consola el mensaje con el error que se ha producido
        console.log("The following error occured: "+ textStatus +" "+ errorThrown);
        console.log(jqXHR);
        console.log(url_acciones_id_por_grupo);
    });



    let sl_adm_accion_id = $("#adm_accion_id");

    let url = "index.php?seccion=adm_accion&ws=1&accion=get_adm_accion&adm_seccion_id="+adm_seccion_id+"&session_id="+session_id;
    let data_envio = { not_in: {llave:'adm_accion.id', values:adm_acciones_id_aut}}
    $.post( url, data_envio)
        .done(function( data ) {
            console.log(data);
            sl_adm_accion_id.empty();

            integra_new_option("#adm_accion_id",'Seleccione una accion','-1');
            $.each(data.registros, function( index, adm_accion ) {

                integra_new_option("#adm_accion_id",adm_accion.adm_menu_descripcion+' '+adm_accion.adm_seccion_descripcion+' '+adm_accion.adm_accion_descripcion,adm_accion.adm_accion_id);
            });
            sl_adm_accion_id.val(adm_accion_id);
            sl_adm_accion_id.selectpicker('refresh');
        }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');

        console.log("The following error occured: "+ textStatus +" "+ errorThrown);
        console.log(jqXHR);
    });;


}

function adm_asigna_secciones(adm_menu_id = ''){
    let sl_adm_seccion_id = $("#adm_seccion_id");

    let url = "index.php?seccion=adm_seccion&ws=1&accion=get_adm_seccion&adm_menu_id="+adm_menu_id+"&session_id="+session_id;

    $.ajax({
        type: 'GET',
        url: url,
    }).done(function( data ) {  // Función que se ejecuta si todo ha ido bien
        sl_adm_seccion_id.empty();
        console.log(url);
        integra_new_option("#adm_seccion_id",'Seleccione una seccion','-1');
        $.each(data.registros, function( index, adm_seccion ) {
            integra_new_option("#adm_seccion_id",adm_seccion.adm_menu_descripcion+' '+adm_seccion.adm_seccion_descripcion,adm_seccion.adm_seccion_id);
        });
        sl_adm_seccion_id.val(adm_seccion_id);
        sl_adm_seccion_id.selectpicker('refresh');
    }).fail(function (jqXHR, textStatus, errorThrown){ // Función que se ejecuta si algo ha ido mal
        alert('Error al ejecutar');

        console.log("The following error occured: "+ textStatus +" "+ errorThrown);
    });

}






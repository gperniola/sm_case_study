/**
 * Funzioni per la visualizzazione dei textbox per motivo e careprovider
 * nel caso vengano scelti motivazione e careprovider non presenti in db
 */
function motivoChange(){
    var index = $(this).attr("id").replace('motivoIndagine','');
    var value = $(document.getElementById("motivoIndagine" + index)).val();
    if (value == ""){
        document.getElementById("motivoAltro" + index).style.display = "block";
    }
    else {
        document.getElementById("motivoAltro" + index).style.display = "none";
    }
}
function careproviderChange(){
    var index = $(this).attr("id").replace('careproviderIndagine','');
    var value = $(document.getElementById("careproviderIndagine" + index)).val();
    if (value == ""){
        document.getElementById("careproviderAltro" + index).style.display = "block";
    }
    else {
        document.getElementById("careproviderAltro" + index).style.display = "none";
    }
}


/**
 * Funzione per la visualizzazione dei campi appropriati in base allo stato dell'indagine
 */
function statoChange(){
    var index = $(this).attr("id").replace('statoIndagine','');
    switch ($(document.getElementById("statoIndagine" + index)).val()){
        case "0":
            document.getElementById("divCentro" + index).style.display = "none";
            document.getElementById("divData" + index).style.display = "none";
            document.getElementById("divReferto" + index).style.display = "none";
            document.getElementById("divAllegato" + index).style.display = "none";
            break;
        case "1":
            document.getElementById("divCentro" + index).style.display = "block";
            document.getElementById("divData" + index).style.display = "block";
            document.getElementById("divReferto" + index).style.display = "none";
            document.getElementById("divAllegato" + index).style.display = "none";
            break;
        case "2":
            document.getElementById("divCentro" + index).style.display = "block";
            document.getElementById("divData" + index).style.display = "block";
            document.getElementById("divReferto" + index).style.display = "block";
            document.getElementById("divAllegato" + index).style.display = "block";
            break;
    }
}


/**
 * Funzioni per la visualizzazione e rimozione del bordo rosso dei campi del form con errori
 * @param id : ID del campo da bordare
 */
function setError(id){
    $(id).closest(".form-group").addClass('has-error');
}
function unsetError(id){
    $(id).closest(".form-group").removeClass('has-error');
}


/**
 * Funzione di validazione di una nuova Indagine con stato "richiesta"
 * @param tipoId
 * @param motivoId
 * @param motivoAltroId
 * @param careproviderId
 * @param careproviderAltroId
 * @returns {boolean} : TRUE se le informazioni inserite sono valide
 */
function validateRichiesta(tipoId, motivoId, motivoAltroId, careproviderId, careproviderAltroId) {

    var isValid = true;
    var tipo = $(tipoId).val().trim();
    var motivo = $(motivoId).val().trim();
    var motivoAltro = $(motivoAltroId).val().trim();
    var careprovider = $(careproviderId).val().trim();
    var careproviderAltro = $(careproviderAltroId).val().trim();

    if (tipo == ''){
        isValid = false; //tipo vuoto
        setError(tipoId);
    } else unsetError(tipoId);

    if (motivo == "placeholder" || (motivo == '' && motivoAltro == '')){
        isValid = false; //motivo placeholder or empty
        setError(motivoId);
    } else unsetError(motivoId);

    if (careprovider == "placeholder" || (careprovider == '' && careproviderAltro == '')){
        isValid = false; //care placeholder or empty
        setError(careproviderId);
    } else unsetError(careproviderId);

    return isValid;
}


/**
 * Funzione di validazione di una nuova Indagine con stato "programmata"
 * @param tipoId
 * @param motivoId
 * @param motivoAltroId
 * @param careproviderId
 * @param careproviderAltroId
 * @param dataId
 * @param centroId
 * @returns {boolean}
 */
function validateProgrammata(tipoId, motivoId, motivoAltroId, careproviderId, careproviderAltroId, dataId, centroId){

    var isValid = validateRichiesta(tipoId, motivoId, motivoAltroId, careproviderId, careproviderAltroId);
    var centro = $(centroId).val().trim();
    var dataMoment = $j(dataId).data("DateTimePicker").date();

    if(dataMoment == null && $(dataId).val() == ""){
        isValid = false;  //data is empty
        setError(dataId);
    } else unsetError(dataId);

    if(centro == "placeholder" || centro == "") {
        isValid = false; //centro is empty
        setError(centroId);
    } else unsetError(centroId);

    return isValid;
}


/**
 * Funzione di validazione di una nuova Indagine con stato "completata"
 * @param tipo
 * @param motivo
 * @param motivoAltro
 * @param careprovider
 * @param careproviderAltro
 * @param data
 * @param centro
 * @param referto
 * @param allegato
 * @returns {boolean}
 */
function validateCompletata(tipo, motivo, motivoAltro, careprovider, careproviderAltro, data, centro, referto, allegato){
    var isValid = validateProgrammata(tipo, motivo, motivoAltro, careprovider, careproviderAltro, data, centro);
    // NESSUN VINCOLO
    return isValid;
}


/**
 * Operazioni da svolgere a pagina caricata
 */
$(document).ready(function(){

    $(window).load(function() {
        $("[id^=motivoIndagine]").each(motivoChange);
        $("[id^=statoIndagine]").each(statoChange);
        $("[id^=careproviderIndagine]").each(careproviderChange);
    });
    $("[id^=motivoIndagine]").change(motivoChange);
    $("[id^=statoIndagine]").change(statoChange);
    $("[id^=careproviderIndagine]").change(careproviderChange);

    //se l'accesso alla pagina è eseguito dal menu, abilita i pulsanti per l'inserimento di nuove indagini
    //se l'accesso è eseguito da un indagine, disabilita i pulsanti
    var menu = document.getElementById("menu_mode").getAttribute('data-menu');
    if(menu){
        $('#nuovoFile').prop('disabled',false);
        $('#concludi').prop('disabled',true);
        $('#annulla').prop('disabled',true);
    }
    else{
        $('#nuovoFile').prop('disabled',true);
        $('#concludi').prop('disabled',true);
        $('#annulla').prop('disabled',true);
        $("#collapse1").collapse('show');
    }

    $("#nuovoFile").click(function(){
		
        $("#formIndagini").show(200);
		$('#nuovoFile').prop('disabled',true);
		$('#concludi').prop('disabled',false);
		$('#annulla').prop('disabled',false);
    });


    /**
     * Funzione per click sul pulsante "Concludi indagine": controlla il form e se corretto invia i dati per
     * l'inserimento in db
     */
    $("#concludi").click(function(){
	    var idPaziente = $("#idPaziente").val().trim();
	    var idCareprovider = $("#cpId").val().trim();
		var tipoValue = $("#tipoIndagine").val().trim();
		var motivoValue = $("#motivoIndagine_new").val().trim();
		var motivoAltroValue = $("#motivoAltro_new").val().trim();
        var careproviderValue = $("#careproviderIndagine_new").val().trim();
        var careproviderAltroValue = $("#careproviderAltro_new").val().trim();
        var statoValue = $("#statoIndagine_new").val().trim();
        var centroValue = $("#centroIndagine_new").val().trim();
		var refertoValue = $("#refertoIndagine_new").val().trim();
		var allegatoValue = $("#allegatoIndagine_new").val().trim();

        var dataMoment = $j("#data").data("DateTimePicker").date();
        if(dataMoment != null && $("#data").val() != "")
            var dataValue = dataMoment.format("YYYY-MM-DD HH:mm:ss").toString();
        else
            var dataValue = "";

        //CONTROLLO DEL FORM
        var formIsValid = false;
        switch(statoValue){
            case "0":
                formIsValid = validateRichiesta("#tipoIndagine", "#motivoIndagine_new", "#motivoAltro_new", "#careproviderIndagine_new", "#careproviderAltro_new");
                break;
            case "1":
                formIsValid = validateProgrammata("#tipoIndagine", "#motivoIndagine_new", "#motivoAltro_new", "#careproviderIndagine_new", "#careproviderAltro_new", "#data", "#centroIndagine_new");
                break;
            case "2":

                formIsValid = validateCompletata("#tipoIndagine", "#motivoIndagine_new", "#motivoAltro_new", "#careproviderIndagine_new", "#careproviderAltro_new", "#data", "#centroIndagine_new", "#refertoIndagine_new", "#allegatoIndagine_new");
                break;
        }
        if(formIsValid){
            $("#formAlert_new").collapse();
            $.post("formscripts/nuovaIndagine.php",
                {
                    idPaziente:     idPaziente,
                    idCare:         idCareprovider,
                    tipo:           tipoValue,
                    idMotivo:       motivoValue,
                    motivoAltro:    motivoAltroValue,
                    careprovider:   careproviderValue,
                    careproviderAltro: careproviderAltroValue,
                    stato:          statoValue,
                    centro:         centroValue,
                    data:           dataValue,
                    referto:        refertoValue,
                    allegato:       allegatoValue
                },
                function(status){
                    $('#formIndagini')[0].reset();
                    window.location.reload();   //RICARICO PAGINA PER AGGIORNARE VALORI
                });
        }
        else{
            $("#formAlert_new").show();
        }
    });


    /**
     * Funzione per click sul pulsante "Annulla indagine": nasconde il form.
     */
    $("#annulla").click(function(){
		
        $("#formIndagini").hide(200);
		$('#nuovoFile').prop('disabled',false);
		$('#concludi').prop('disabled',true);
		$('#annulla').prop('disabled',true);
    });


    /**
     * Funzione per click sul pulsante "Modifica indagine": presente su ogni riga:
     * apre un form sotto di esso per la modifica dei dati
     */
    $(document).on('click', "button.modifica", function () {
        $(this).prop('disabled', true);
        $('#'+$(this).attr('id')+'.elimina').prop('disabled', true);
        var id = '#riga'+$(this).attr('id');
        $(id).show(200);
    });


    /**
     * Funzione per click sul pulsante "Elimina indagine": presente su ogni riga:
     * chiede conferma all'utente e in caso affermativo provvede a cancellare l'indagine
     */
    $(document).on('click', "button.elimina", function () {
        if (confirm("Sei sicuro di voler eliminare l'indagine?")){
            $.post("formscripts/eliminaIndagine.php",
                {
                    idIndagine: $(this).attr('id')
                },
                function(status){
                    window.location.reload();
                });
        }
    });


    /**
     * Funzione per click sul pulsante "Invia messaggio privato" della tabella centri indagini:
     * apre una finestra di messaggio inserendo il nome del responsabile nel centro nel campo destinatario
     */
    $(document).on('click', "a.a-messaggio", function () {
        var id = $(this).attr('id');
        var careprovider = document.getElementById("careproviderStudio" + id).getAttribute('data-nome');
        $('#sendToUser').val(careprovider);
    });


    /**
     * Funzione per click sul pulsante "Annulla modifiche" presente in ogni form di modifica indagine:
     * nasconde il form di modifica
     */
    $(document).on('click', "a.annulla", function () {
        var but = '#'+$(this).attr('id');
        $(but+'.modifica').prop('disabled', false);
        $('#'+$(this).attr('id')+'.elimina').prop('disabled', false);
        var id = '#riga'+$(this).attr('id');
        $(id).hide(200);
    });

    /**
     * Funzione per click sul pulsante "Conferma modifiche" presente in ogni form di modifica indagine:
     * controlla che i dati del form siano corretti e li invia per la modifica dei dati nel db
     */
    $(document).on('click', "a.conferma", function () {

        var id = $(this).attr('id');
        var idPaziente = $("#idPaziente").val().trim();
        var idCareprovider = $("#cpId").val().trim();
        var tipoValue = $("#tipoIndagine" + id).val().trim();
        var motivoValue = $("#motivoIndagine_" + id).val().trim();
        var motivoAltroValue = $("#motivoAltro_" + id).val().trim();
        var careproviderValue = $("#careproviderIndagine_" + id).val().trim();
        var careproviderAltroValue = $("#careproviderAltro_" + id).val().trim();
        var statoValue = $("#statoIndagine_" + id).val().trim();
        var centroValue = $("#centroIndagine" + id).val().trim();
        var refertoValue = $("#refertoIndagine_" + id).val().trim();
        var allegatoValue = $("#allegatoIndagine_" + id).val().trim();

        var dataMoment = $j("#data" + id).data("DateTimePicker").date();
        if(dataMoment != null && $("#data" + id).val() != "")
            var dataValue = dataMoment.format("YYYY-MM-DD HH:mm:ss").toString();
        else
            var dataValue = "";

        //CONTROLLO DATI
        var formIsValid = false;
        switch(statoValue){
            case "0":
                formIsValid = validateRichiesta("#tipoIndagine"+id, "#motivoIndagine_"+id, "#motivoAltro_"+id,"#careproviderIndagine_"+id,"#careproviderAltro_"+id);
                break;
            case "1":
                formIsValid = validateProgrammata("#tipoIndagine"+id,"#motivoIndagine_"+id,"#motivoAltro_"+id,"#careproviderIndagine_"+id,"#careproviderAltro_"+id,"#data"+id,"#centroIndagine"+id);
                break;
            case "2":
                formIsValid = validateCompletata("#tipoIndagine"+id,"#motivoIndagine_"+id,"#motivoAltro_"+id,"#careproviderIndagine_"+id,"#careproviderAltro_"+id,"#data"+id,"#centroIndagine"+id,"#refertoIndagine_"+id,"#allegatoIndagine_"+id);
                break;
        }
        if(formIsValid){
            $("#formAlert_"+id).collapse();
            $.post("formscripts/modificaIndagine.php",
                {
                    idIndagine:     id,
                    idPaziente:     idPaziente,
                    idCare:         idCareprovider,
                    tipo:           tipoValue,
                    idMotivo:       motivoValue,
                    motivoAltro:    motivoAltroValue,
                    careprovider:   careproviderValue,
                    careproviderAltro: careproviderAltroValue,
                    stato:          statoValue,
                    centro:         centroValue,
                    data:           dataValue,
                    referto:        refertoValue,
                    allegato:       allegatoValue
                },
                function(status){
                    $('#formIndagini')[0].reset();
                    window.location.reload();
                });
        }
        else{
            $("#formAlert_"+id).show();
        }
    });
});


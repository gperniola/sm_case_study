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


function validateRichiesta(tipo, motivo, motivoAltro, careprovider, careproviderAltro){
    var isValid = true;
    if (tipo == '') isValid = false; //tipo vuoto
    if (motivo == "placeholder" ||
        (motivo == '' && motivoAltro == '')) isValid = false; //motivo placeholder or empty
    if(careprovider == "placeholder" ||
        (careprovider == '' && careproviderAltro == '')) isValid = false; //care placeholder or empty
    return isValid;
}

function validateProgrammata(tipo, motivo, motivoAltro, careprovider, careproviderAltro, data, centro){
    var isValid = validateRichiesta(tipo, motivo, motivoAltro, careprovider, careproviderAltro);
    //alert("date: " + data);
    if(data == "") isValid = false;  //data is empty
    if(centro == "placeholder" || centro == "") isValid = false; //centro is empty
    return isValid;
}

function validateCompletata(tipo, motivo, motivoAltro, careprovider, careproviderAltro, data, centro, referto, allegato){
    var isValid = validateProgrammata(tipo, motivo, motivoAltro, careprovider, careproviderAltro, data, centro);
    if(referto == '') isValid = false; //referto is empty
    return isValid;
}

$(document).ready(function(){

    $(window).load(function() {
        $("[id^=motivoIndagine]").each(motivoChange);
        $("[id^=statoIndagine]").each(statoChange);
        $("[id^=careproviderIndagine]").each(careproviderChange);
    });
    $("[id^=motivoIndagine]").change(motivoChange);
    $("[id^=statoIndagine]").change(statoChange);
    $("[id^=careproviderIndagine]").change(careproviderChange);

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



	
	//$('#nomeCp').prop('disabled',true);
	//$('#cognomeCp').prop('disabled',true);



    $("#nuovoFile").click(function(){
		
        $("#formIndagini").show(200);
		$('#nuovoFile').prop('disabled',true);
		$('#concludi').prop('disabled',false);
		$('#annulla').prop('disabled',false);
    });
	
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
		var refertoValue = $("#referto").val().trim();
		var allegatoValue = $("#allegato").val().trim();

        var dataMoment = $j("#data").data("DateTimePicker").date();
        if(dataMoment != null && $("#data").val() != "")
            var dataValue = dataMoment.format("YYYY-MM-DD HH:mm:ss").toString();
        else
            var dataValue = "";


        var formIsValid = false;
        switch(statoValue){
            case "0":
                //alert("is 0");
                formIsValid = validateRichiesta(tipoValue, motivoValue, motivoAltroValue, careproviderValue, careproviderAltroValue);
                break;
            case "1":
                //alert("is 1");
                formIsValid = validateProgrammata(tipoValue, motivoValue, motivoAltroValue, careproviderValue, careproviderAltroValue, dataValue, centroValue);
                break;
            case "2":
                //alert("is 2");
                formIsValid = validateCompletata(tipoValue, motivoValue, motivoAltroValue, careproviderValue, careproviderAltroValue, dataValue, centroValue, refertoValue, allegatoValue);
                break;
        }
        if(formIsValid){
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
                    //alert("Status: " + status);
                    window.location.reload();
                    //$("#collapse1").collapse('show');
                    //$('#tableIndagini').append('<tr><td>'+data+'</td><td>'+tipo+'</td><td>'+referto+'</td><td>'+allegato+'</td></tr>');
                });
        }
        else{
            alert("ATTENZIONE: Compilare correttamente tutti i campi.");
        }
        /*
		
		
		if(tipo.trim()=='' || data.trim()=='' || referto.trim()=='' || allegato.trim()==''){
			alert('Tutti i campi sono obbligatori.');
		}else{
		
	$("#formIndagini").hide(200);
		$('#nuovoFile').prop('disabled',false);
		$('#concludi').prop('disabled',true);
		$('#annulla').prop('disabled',true);
		
		
		
		$.post("formscripts/nuovaIndagine.php",
			{	
			
				idPaziente:  $("#idPaziente").val(),
				idDiagnosi:  $("#idDiagnosi").val(),
				motivo:      $("#motivoIndagine").val(),
				tipo:        $("#tipoIndagine").val(),
				data:        $("#data").val(),
				referto:     $("#referto").val(),
				allegato:    $("#allegato").val(),
				
			},
			function(status){
				$('#formIndagini')[0].reset();
    			//alert("Status: " + status);
				$('#tableIndagini').append('<tr><td>'+data+'</td><td>'+tipo+'</td><td>'+referto+'</td><td>'+allegato+'</td></tr>');
  			});
		
		
		}*/
		


        
    });
	
	$("#annulla").click(function(){
		
        $("#formIndagini").hide(200);
		$('#nuovoFile').prop('disabled',false);
		$('#concludi').prop('disabled',true);
		$('#annulla').prop('disabled',true);
    });


	/* PULSANTE "MODIFICA" DI OGNI RIGA DELLE TABELLE */
    $(document).on('click', "button.modifica", function () {
        $(this).prop('disabled', true);
        var id = '#riga'+$(this).attr('id');
        $(id).show(200);
    });

    $(document).on('click', "button.elimina", function () {
        if (confirm("Eliminare l'indagine?")){
            $.post("formscripts/eliminaIndagine.php",
                {
                    idIndagine: $(this).attr('id')
                },
                function(status){
                    //$('#formD')[0].reset();
                    //alert("Status: " + status);
                    window.location.reload();
                });

            //var id = $(this).attr('id');
            //var riga = "#r"+id;
            //$(riga).hide(250);
            //riga = "#riga"+id;
            //$(riga).hide(250);
        }


    });

    $(document).on('click', "a.a-messaggio", function () {
        var id = $(this).attr('id');
        var careprovider = document.getElementById("careproviderStudio" + id).getAttribute('data-nome');
        $('#sendToUser').val(careprovider);
    });



	/* PULSANTE "[annulla]" PRESENTE IN OGNI FORM DI RIGA */
    $(document).on('click', "a.annulla", function () {
        var but = '#'+$(this).attr('id');
        $(but+'.modifica').prop('disabled', false);

        var id = '#riga'+$(this).attr('id');
        $(id).hide(200);
    });

    /* PULSANTE [conferma] PRESENTE IN OGNI FORM DI RIGA */
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
        var refertoValue = $("#referto" + id).val().trim();
        var allegatoValue = $("#allegato" + id).val().trim();

        var dataMoment = $j("#data" + id).data("DateTimePicker").date();
        if(dataMoment != null && $("#data" + id).val() != "")
            var dataValue = dataMoment.format("YYYY-MM-DD HH:mm:ss").toString();
        else
            var dataValue = "";


       /* alert(
            "id indagine: " + id + ", " +
            "idpaz: " + idPaziente + ", " +
            "idcp: " + idCareprovider + ", " +
            "tipo: " + tipoValue + ", " +
            "motivo: " + motivoValue + ", " +
            "motivoAltro: " + motivoAltroValue + ", " +
            "careprovider: " + careproviderValue + ", " +
            "careproviderAltro: " + careproviderAltroValue + ", " +
            "stato: " + statoValue + ", " +
            "centro: " + centroValue + ", " +
            "data: " + dataValue + ", " +
            "referto: " + refertoValue + ", " +
            "allegato: " + allegatoValue + ", "
        );*/

        var formIsValid = false;
        switch(statoValue){
            case "0":
                //alert("is 0");
                formIsValid = validateRichiesta(tipoValue, motivoValue, motivoAltroValue, careproviderValue, careproviderAltroValue);
                break;
            case "1":
                //alert("is 1");
                formIsValid = validateProgrammata(tipoValue, motivoValue, motivoAltroValue, careproviderValue, careproviderAltroValue, dataValue, centroValue);
                break;
            case "2":
                //alert("is 2");
                formIsValid = validateCompletata(tipoValue, motivoValue, motivoAltroValue, careproviderValue, careproviderAltroValue, dataValue, centroValue, refertoValue, allegatoValue);
                break;
        }

        if(formIsValid){
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
                    //alert("Status: " + status);
                    //html5.append(status);
                    window.location.reload();
                    //$("#collapse1").collapse('show');
                    //$('#tableIndagini').append('<tr><td>'+data+'</td><td>'+tipo+'</td><td>'+referto+'</td><td>'+allegato+'</td></tr>');
                });
        }
        else{
            alert("ATTENZIONE: Compilare correttamente tutti i campi.");
        }

    });



});


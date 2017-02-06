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

$(document).ready(function(){

    $(window).load(function() {
        $("[id^=motivoIndagine]").each(motivoChange);
        $("[id^=statoIndagine]").each(statoChange);
    });

    $("[id^=motivoIndagine]").change(motivoChange);
    $("[id^=statoIndagine]").change(statoChange);


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
	
		var tipo = $("#tipoIndagine").val();
		var data = $("#data").val();
		var referto = $("#referto").val();
		var allegato = $("#allegato").val();
		
		
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
		
		
		}
		


        
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


	/* PULSANTE "[annulla]" PRESENTE IN OGNI FORM DI RIGA */
    $(document).on('click', "a.annulla", function () {
        var but = '#'+$(this).attr('id');
        $(but+'.modifica').prop('disabled', false);

        var id = '#riga'+$(this).attr('id');
        $(id).hide(200);
    });


});





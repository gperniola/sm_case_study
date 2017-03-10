<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$idPaziente 	= $_POST['idPaziente'];
	$idCare  		= $_POST['idCare'];
	$tipo 	 		= $_POST['tipo'];
	$idMotivo   	= $_POST['idMotivo'];
	$motivoAltro	= $_POST['motivoAltro'];
	$careprovider	= $_POST['careprovider'];
	$careproviderAltro = $_POST['careproviderAltro'];
	$stato			= $_POST['stato'];
	$centro			= $_POST['centro'];
	$data			= $_POST['data'];
	$idReferto    	= $_POST['referto'];
	$idAllegato   	= $_POST['allegato'];

	session_start(); 
	$id_Pz = $_SESSION['pz_Id'];

    // Partendo dall'id utente ($id_Pz) ottengo il relativo id paziente
    $idPazienteConnesso = getInfo('id', 'pazienti', 'idutente = '.$id_Pz);
	if (isset ($_SESSION['cp_Id']))
		{
			$id_Cp = $_SESSION['cp_Id'];
			$id_prop = $id_Cp;
		}
		else 
			$id_prop = $id_Pz;

	//Se l'idPaziente passato per la modifica è lo stesso del paziente connesso...
	if ($idPazienteConnesso == $idPaziente){
        if($careprovider != '') 	//se ho un ID per un careprovider registrato...
        	//usando l'ID ne estraggo nome e cognome per l'inserimento in tabella
			$careproviderNome = getInfo('nome', 'careproviderpersona', 'id='.$careprovider) . ' ' .
            	getInfo('cognome', 'careproviderpersona', 'id='.$careprovider);
    	else{
    		//altrimenti utilizzo il nome passato in input
    		$careprovider = "NULL";
        	$careproviderNome = $careproviderAltro;
		}
		//se ho un ID diagnosi per il motivo, ne estraggo il nome
		if($idMotivo != '') $motivo = getInfo('patologia', 'diagnosi','id='.$idMotivo);
		else{
			//altrimenti utilizzo la motivazione passata in input
        	$idMotivo = "NULL";
        	$motivo = $motivoAltro;
		}
		if($idReferto == '') $idReferto = "NULL";
        if($idAllegato == '') $idAllegato = "NULL";
		if($stato == "0"){
        	echo nuovaIndagineRichiesta($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, 0, $tipo);
			}else if ($stato == "1"){
        		echo nuovaIndagineProgrammata($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, 1, $tipo, $data, $centro);
    			}else if  ($stato == "2") {
        			echo nuovaIndagineCompletata($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, 2, $tipo, $data, $centro, $idReferto, $idAllegato);
    			}
    }
    else
        echo'<script>alert("Errore: il paziente in modifica non corrisponde al paziente connesso");</script>';
}
?>

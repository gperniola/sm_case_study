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
	$referto    	= $_POST['referto'];
	$allegato   	= $_POST['allegato'];

	session_start(); 
	$id_Pz = $_SESSION['pz_Id'];
	if (isset ($_SESSION['cp_Id']))
		{
			$id_Cp = $_SESSION['cp_Id'];
			$id_prop = $id_Cp;
		}
		else 
			$id_prop = $id_Pz;


	//TODO: check if the ids are ok?
	//TODO: check data again?
	//TODO: get careprovider's name and diagnosi with the id if not nulls, then call the db function to make insertion
	//TODO: change datains and dataagg to timestamp format

	if($careprovider != '')
		$careproviderNome = getInfo('nome', 'careproviderpersona', 'id='.$careprovider) . ' ' .
            getInfo('cognome', 'careproviderpersona', 'id='.$careprovider);
    else{
    	$careprovider = "NULL";
        $careproviderNome = $careproviderAltro;
	}

	if($idMotivo != '') $motivo = getInfo('patologia', 'diagnosi','id='.$idMotivo);
	else{
        $idMotivo = "NULL";
        $motivo = $motivoAltro;
	}

	if($stato == "0"){
        echo nuovaIndagineRichiesta($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, "richiesta", $tipo);
	}else if ($stato == "1"){
        echo nuovaIndagineProgrammata($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, "programmata", $tipo, $data, $centro);
    }else if  ($stato == "2") {
        echo nuovaIndagineCompletata($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, "conclusa", $tipo, $data, $centro, $referto, $allegato);
    }






}

?>

<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
	
	$idPaziente = $_POST['idPaziente'];
	$idDiagnosi = $_POST['idDiagnosi'];
	$motivo     = $_POST['motivo'];
	$tipo       = $_POST['tipo'];
	$data       = $_POST['data'];
	$referto    = $_POST['referto'];
	$allegato   = $_POST['allegato'];

	session_start(); 
	$id_Pz = $_SESSION['pz_Id'];
	if (isset ($_SESSION['cp_Id']))
		{
			$id_Cp = $_SESSION['cp_Id'];
			$id_prop = $id_Cp;
		}
		else 
			$id_prop = $id_Pz;

		
			
	
	echo nuovaIndagine($idPaziente,$idDiagnosi,$motivo,$tipo,$data,$referto,$allegato);
	
	

}

?>

<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
	
	$pz_id      = $_POST['idpaziente'];
	$codice     = $_POST['codice'];
	$dataVaccino= $_POST['dataVaccino'];
	$reazioni    = $_POST['reazioni'];

	session_start(); 
	$id_Pz = $_SESSION['pz_Id'];
	if (isset ($_SESSION['cp_Id']))
		{
			$id_Cp = $_SESSION['cp_Id'];
			$id_prop = $id_Cp;
		}
		else 
			$id_prop = $id_Pz;

		
			
	
	echo nuovaVaccinazione($pz_id,$codice,$dataVaccino,$reazioni);
	
	

}

?>

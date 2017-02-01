<?php
if ( isset ($_GET["cp_Id"]))
	{
		$cp_id = $_GET["cp_Id"]; //inizializzo $cp_id col valore passato con GET
		$myRole = getRole($cp_id);
	}
	else
		$myRole = getRole(getMyID());
	
	if ( isset ($_GET["pz_Id"]))
			$pz_id = $_GET["pz_Id"]; //inizializzo $cp_id col valore passato con GET
	else	
			$pz_id  = getMyID();

$pag_vaccinazioni= new templatemanager('templates/');

/* 	"vaccinazione_n_codice"  		codice del vaccino
**	"vaccinazione_n"  				nome del vaccino
**	"vaccinazione_n_descrizione"	descrizione testuale
**	"vaccinazione_n_inizio" 		data del giorno della vaccinazione espressa in 'aaaa-mm-gg'
**	"vaccinazione_n_fine" 			data della fine della copertura del vaccino espressa in 'aaaa-mm-gg'
**	"vaccinazione_n_reazioni"		descrizione testuale delle reazioni
**	"vaccinazione_n_CP"				nome Care Provider che ha fatto il vaccino - TODO collegamento alla scheda CP
**	"vaccinazione_n_alert"			Indicatore del colore con cui evidenziare il vaccino. Facoltativo.
**									Valori ammessi: "danger" (rosso), "warning" (giallo), "success" (verde).
**									Se non definito per default il rigo è bianco.								
*/


//$arrayVaccinazioni = getArray('id', 'vaccinazioni', 'idutente = ' . getCurrentID());
$arrayVaccinazioni = getArray('id', 'vaccinazioni', 'idpaziente = ' . $pz_id);

if (count($arrayVaccinazioni) > 0)
{
	$index = 0;
	$pag_vaccinazioni->set_var('nvaccinazioni', count($arrayVaccinazioni));

	foreach($arrayVaccinazioni as $id){
		
		$codice = getInfo('codice', 'vaccinazioni', 'id = ' . $id);
		$idCpp = getInfo('idcpp', 'vaccinazioni', 'id = ' . $id);
		$reazioni = getInfo('reazioni', 'vaccinazioni', 'id = ' . $id);
		$dataVaccino = getInfo('datavaccino', 'vaccinazioni', 'id = ' . $id);
		
		$copertura = getInfo('copertura', 'vaccini', 'codice = "' . $codice . '"');
		
		$annoVaccino = (int)substr($dataVaccino, 0, 4);
		$meseGiorno = substr($dataVaccino, 5);
		$annoScadenza = $annoVaccino + $copertura;
		$dataScadenza = $annoScadenza . "-".$meseGiorno;
		
		$nome = getInfo('nome', 'vaccini', 'codice = "' . $codice . '"');
		$descrizione = getInfo('descrizione', 'vaccini', 'codice = "' . $codice . '"');
		
		$pag_vaccinazioni->set_var('vaccinazione_' . ++$index . '_codice', $codice);
		$pag_vaccinazioni->set_var('vaccinazione_' . $index, $nome);
		$pag_vaccinazioni->set_var('vaccinazione_' . $index . '_descrizione', $descrizione);
		$pag_vaccinazioni->set_var('vaccinazione_' . $index . '_inizio', $dataVaccino);
		$pag_vaccinazioni->set_var('vaccinazione_' . $index . '_fine',$dataScadenza);
		$pag_vaccinazioni->set_var('vaccinazione_' . $index . '_reazioni', $reazioni);
		$pag_vaccinazioni->set_var('vaccinazione_' . $index . '_CP','Dr. ' . getCredentials($idCpp)); 
		$pag_vaccinazioni->set_var('vaccinazione_' . $index . '_alert','success'); 
		
		$days = getDays($dataScadenza);
		if ($days < 1) 
			updateInfo('stato = "Scaduto"', 'vaccinazioni', 'id = ' . $id);
	}

}


$pag_vaccinazioni->out('template_page_vaccinazioni');
?>
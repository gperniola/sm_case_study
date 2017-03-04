<!--carica i files inseriti in template_page_files-->
<?php
require_once ('configFiles.php');
require_once ('funzioniFiles.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
require_once($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Session.php');

/*
// commento ripreso da upload.php E' possibile ricevere le informazioni aggiuntive relative al file
// Modificare 'modvisita.js' in formscripts per inserire le informazioni aggiuntive da trasmettere
$userid = empty($_POST['userid']) ? '' : $_POST['userid'];
$username = empty($_POST['username']) ? '' : $_POST['username'];
["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
} 
*/

	if ( $_FILES["nomefile"]["size"] > 15000000) 
	{
		echo "Il file ha dimensioni superiori a 15 Mb e non pu&o essere caricato";
    } 
	else{
		$tmp_nome = $_FILES["nomefile"] ["tmp_name"]; //tmp_name percorso temporaneo in cui è stato memorizzato il file
		$tipo =	    $_FILES["nomefile"] ["type"];	// type tipo MIME
		$nome =	    $_FILES["nomefile"] ["name"];	// name nome del file
		$formatoErrato = FALSE;
		$uploadPath ="uploads/";
		
		if ( isset ($_POST["idPaz"]))
		{
			$idPaz = $_POST["idPaz"]; // setto l'id del paziente
		// $idProp viene settata solo se è stata settata l 
			
		}
		if ( isset ($_POST["id_prop"]))
				$id_prop = $_POST["id_prop"]; // setto l'id del propritario del file 
			
		// con le istruzioni seguenti è inviabile un file alla volta
		//viene settata la variabile $fileclass utilizzata nel switch per contollare il formato
		if ( isset ($_POST["fileClass1"]))
			$fileClass = 1;
		if ( isset ($_POST["fileClass2"]))
			$fileClass = 2;
		if ( isset ($_POST["fileClass3"]))
			$fileClass = 3;
		if ( isset ($_POST["fileClass4"]))
			$fileClass = 4;
		if ( isset ($_POST["fileClass5"]))
			$fileClass = 5;	
		if ( isset ($_POST["fileClass6"]))
			$fileClass = 6;
		
		if ( isset ($_POST["conf_1"])) 
			$conf = $_POST["conf_1"]; // viene settata la riservatezza con le stringhe passate col post
		
		$data = date("Y/m/d") ; // viene settata la data
		
		if ( isset ($_POST["comm"]))
			$commento = $_POST["comm"];
		else
			$commento = "";
			
		//utilizzo l'istruzione switch  con $fileClass per modificare uploadpath e caricare il file nella cartella corrispondente
		//la funzione controllaFormato verifica che l'estensione del file corrisponda al tipo dichiarato 
		// ad ogni caso andrebbero aggiunti dei controlli per verificare che il file corrisponda alla classe dichiarata e di sicurezza
		switch ($fileClass){
			
			case 1 :
			
			if (controllaFormato($nome, 1) )
				$uploadPath = $uploadPath . "foto/";
			else $formatoErrato = TRUE;
			break;
			
			case 2 :
			if (controllaFormato($nome, 2))
				$uploadPath = $uploadPath . "videoPz/";
			else $formatoErrato = TRUE;
			break;
			
			case 3 :
			if (controllaFormato($nome, 3))
				$uploadPath = $uploadPath . "registrazioni/";
			else $formatoErrato = TRUE;
			break;
			
			case 4 :
			if ( controllaFormato($nome, 4))
				$uploadPath = $uploadPath . "dicom/";
			else $formatoErrato = TRUE;
			break;
			
			case 5 :
			if (controllaFormato($nome, 5) )
				$uploadPath = $uploadPath . "videoStrum/";
			else $formatoErrato = TRUE;
			break;
			
			case 6 :
			if ( controllaFormato($nome, 6) )
				$uploadPath = $uploadPath . "scansioni/";
			else $formatoErrato = TRUE;
			break;
			
		}//fine del switch $fileClass
		
		/*switch ( $conf_1){
			
			case "nessuna" :
			  $conf = 1;
			  break;
			  
			case "basso" :
			  $conf = 2;
			  break;

			case "moderato" :
			  $conf = 3;
			  break;
			  
			case "normale" :
			  $conf = 4;
			  break;

			case "riservato" :
			  $conf = 5;
			  break;
			  
			case "strettamente" :
			  $conf = 6;
			  break;
			
			default :
			break;
		}*/ //fine del switch per la riservatezza
		
		//if (move_uploaded_file($tmp_nome, DIR_IMMAGINI . "/" . $nome))
		$target = $uploadPath . $nome;
		if (file_exists( $target))
			echo "<p>E' gi&agrave presente un file con lo stesso nome.</p>\n";

		else if ($formatoErrato)	
			echo "<p>Il formato del file non &egrave del tipo dichiarato.</p>\n" . $formatoErrato;
		else if	(move_uploaded_file($tmp_nome, $uploadPath . $nome))
		{
			
			$extension = file_extension( $nome);
			echo insertFilesData( $idPaz , $id_prop ,$nome, $data, $uploadPath, $extension,  $conf, $commento);
		
			echo "<p>Inserimento del file effettuato.</p>\n";
			
		
			if ($nome){
				echo "E' stato inviato il file: ".	$nome ."<br> uploadPath = " . $uploadPath . "<br> fileclass = " . $fileClass . "<br>".
				"La visibilit&agrave impostata &egrave di : " . $conf . "<br> idPaz = " . $idPaz .
				"<br> Il file &egrave stato inserito il " . $data . "<br> Il proprietario e :" . $id_prop;
				return;
			}
		}
		else
		echo json_encode(['error'=>'sei in uploadFiles.php.']); 
			return;
	
	 
	if ($success === true) {
		$output = [];

	} elseif ($success === false) {
		$output = ['error'=>'Errore nell\'upload.'];
		// delete any uploaded files
		foreach ($paths as $file) {
			unlink($file);
		}
	} else {
		$output = ['error'=>'Nessun file caricato.'];
	}
 
// restituisce la risposta json per il plugin
	}
echo json_encode($output);
?>
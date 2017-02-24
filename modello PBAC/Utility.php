<?php

include ('Account.php');
include ('Data.php');
include ('pdfLib/mem_image.php');

/**
* UTILITY contenenti funzioni utilizzabili nella progettazione del FSEM, funzioni accessorie
* @author Francesco Faggiani - Mat. 512320
*/


 

 
function decryptData($data){
	
	/**
	* Decripta un dato precedentemente criptato
	* 
	* @param $data
	* @return String (decrypted data)
	*/ 


          $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
          $ks = mcrypt_enc_get_key_size($td);
          $key = substr(md5('la mia super chiave privata'), 0, $ks);
		 
		 
          $iv = $data -> getIV();
          $value = $data -> getValue();
		 
          mcrypt_generic_init($td, $key, $iv);

          $decrypted = mdecrypt_generic($td, $value);

          mcrypt_generic_deinit($td);
          mcrypt_module_close($td);

          return trim($decrypted);

}

function defaultPermissions($id){
	
	/**
	* Verifica i permessi di default (proprietario account o careprovider associato)
	* 
	* @param $id
	* @return boolean
	*/ 
	
	$myRole = getRole(getMyID());
	
	if($myRole == 'ass' or $myRole == 'emg'
	or getInfo('idcpp', 'careproviderpaziente', 'idutente = ' . $id) == getMyID()) //restituiscetrue se l' id dell'utente loggato corrisponde a quella di uno dei careproviders dell' $id passato come parametro 
		return true;
		
	return false;
}


function deleteFromServer($target){
	
	/**
	* Cancella un file dal server
	* 
	* @param $name
	* @return deleted file (true), false
	*/ 

	if (file_exists($target)) {
		unlink($target); 
		echo "esiste";
          	return true;
	}
	 else {
	 	echo "non esiste";
	 	return false;
	 }
   		

}


function createPDFRequest($credenziali, 
                                                       $ruolo, 
                                                       $CF, 
                                                       $infoAlbo, 
                                                       $date,
                                                       $iscrizioneAlbo, 
                                                       $comune, 
                                                       $ospedale, 
                                                       $email, 
                                                       $pec,
                                                       $recapito){
	
	
	/**
	* Crea una richiesta di iscrizione sottoforma di PDF
	* 
	*/ 
	
	// estrazione informazioni
	
	if ($ruolo == 'ass') 
		$role = 'Assistito';
	else
		$role = getInfo('descrizione', 'codici', 'codice = "' . $ruolo . '"'); 
	
	$title = 'Richiesta di iscrizione';
	$date =  italianFormat($date);
	
	$pdf = new PDF_MemImage();
	$pdf -> AddPage();
	$pdf -> AliasNbPages();
	$pdf -> SetMargins(15, 15);
	
          $pdf -> SetFont('courier','B',15);
          $pdf -> Ln(10);
    
          // INFORMAZIONI AMMINISTRATORE
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(180,8,"All'amministrazione FSEM",0,1,"R");
          $pdf -> Cell(180,8,"Viale Unita'' d'Italia 20 - Roma",0,1,"R");
          $pdf -> Cell(180,8,"Cap 00118",0,1,"R");
    
          $pdf -> Ln(15);
    
          // TITOLO
          $pdf -> SetFont('courier','B',15);
          $pdf -> Cell(70,10, $title);
          $pdf -> Ln(20);
    
          // NOME
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,'Nome ',0,0,"L");
    
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$credenziali,0,1,"L");
    
	// RUOLO
	$pdf -> SetFont('courier','',8);
	$pdf -> Cell(120,5,'Ruolo ',0,0,"L");
	
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$role,0,1,"L");
    
          // CODICE FISCALE O PARTITA IVA
          $pdf -> SetFont('courier','',8);
	
	if ($ruolo != 'lab'){
		$pdf -> Cell(120,5,'Codice Fiscale ',0,0,"L");
		$pdf -> SetFont('courier','B',8);
		$pdf -> Cell(120,5,$CF,0,1,"L");
	}
		
	else{
		$pdf -> Cell(120,5,'Partita IVA: ',0,0,"L");
		$pdf -> SetFont('courier','B',8);
		$pdf -> Cell(120,5,$CF,0,1,"L");
	}
		
	// OSPEDALE
	$pdf -> SetFont('courier','',8);	
          $pdf -> Cell(120,5,'Operante presso',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$ospedale . ' - ' . $comune,0,1,"L");
    
          // ALBO
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,"Iscritto all'Albo dell'Ordine dei medici chirurghi ed odontoiatri di",0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$infoAlbo,0,1,"L");
    
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,'Data iscrizione albo ',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$date,0,1,"L");
    
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,'N° iscrizione ',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$iscrizioneAlbo,0,1,"L");
    
          $pdf -> Ln(7);
    
          // INDIRIZZO EMAIL
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,'Indirizzo Email ',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$email,0,1,"L");
    
          // INDIRIZZO PEC
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,'Indirizzo Posta Elettronica Certificata ',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$pec,0,1,"L");
    
         // RECAPITO TELEFONICO
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,'Recapito telefonico ',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,$recapito,0,1,"L");
    
    
          $pdf -> SetFont('courier','',8);
          $pdf -> Ln(15);
    
          // DATA DI INVIO
          $pdf -> Cell(120,5,'Data di invio ',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,date('d-m-Y', strtotime(getTime('date'))),0,1,"L");
    
          // DATA DI SCADENZA
          $pdf -> SetFont('courier','',8);
          $pdf -> Cell(120,5,'Scadenza richiesta ',0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(120,5,date('d-m-Y', strtotime(generateDate(15))),0,1,"L");
    
          // CONSENSO INFORMATO
          $pdf -> Ln(20);
          $consenso = file_get_contents('Consenso.txt');
          $pdf -> MultiCell(0,7,$consenso);
    
          // IMMAGINE TESSERINO
          $pdf -> Ln(15);
          $image = getInfo('contenuto', 'data', 'tipologia = "Tesserino albo" and idproprietario = 6');
          $pdf -> MemImage($image,15,150,'', 50);
    
          $pdf -> Ln(70);
    
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(135,5,"Li',_____________________",0,0,"L");
          $pdf -> SetFont('courier','B',8);
          $pdf -> Cell(135,5,'Firma del responsabile',0,1,"L");
          $pdf -> Ln(5);
          $pdf -> Cell(135,5,'',0,0,"L");
          $pdf -> Cell(135,5,'________________________',0,0,"L");

 
          $pdf -> Output();
	
	return true;

}


function italianFormat($data) {
 
	/**
	* Imposta la data nel formato GG - MM - AAAA
	* 
	* @param $data
	* @return Date
	*/ 
	
	if (is_null($data) or $data == 0) return null;
	return date('d-m-Y', strtotime($data));

    
}


function updateData($data) {
 
	/**
	* Aggiorna un documento precedentemente salvato
	* 
	* @param $data
	* @return Boolean
	*/ 
	
	
	global $database;
	
          $idArray = getArray('id', 'data', 'idproprietario = ' . $data -> getOwner() . ' and tipologia = "' . 
                        $data -> getLabel() . '"');
	
	$str = serialize($data);
          $strenc = urlencode($str);
    
          foreach ($idArray as $id){
              $q1 = 'DELETE FROM data WHERE id = ' . $id;
              executeQuery($q1);
          }
    
            
	$q2 = 'INSERT INTO data (idproprietario, idsoggetto, contenuto, data, tipologia, stato) VALUES ('. $data -> getOwner() . 
	           ', ' . $data -> getSubject() . ', "' .  $strenc . '", "' . getTime('date') . '", "' . $data -> getLabel() . 
	           '", Updated")'; 
		
          return executeQuery($q2);
    
}



function createSubject($name, $value){
	
	/**
	* Crea un soggetto da associare a una policy
	* ogni soggetto è un collegamento tra id utente e risorsa
	* 
	* @param $name
	* @param $value
	* 
	* @return Subject
	*/ 
	
          $subject = new Subject();
          $subject -> addAttribute( new Attribute($name, $value) );
        
          return $subject;
}


function getCredentials($id){
	
	
	/**
	* Tramite l'ID ottengo nome e cognome utente oppure il nome dell'ente o laboratorio
	* 
	* @param $id
	* 
	* @return $name_$surname or $name, boolean
	*/ 
	
          global $database;
    
          if (!is_numeric($id)) return false;
    
          $role = getRole($id);
    
          if ($role == 'ass')
              $q = 'SELECT nome, cognome FROM pazienti WHERE idutente = ' . $id;
          elseif ($role == 'lab')
                    $q = 'SELECT nome FROM entistrutture WHERE idutente = ' . $id;
              elseif ($role == 'amm')
                   	$q = 'SELECT nome, cognome FROM amministratore WHERE idutente = ' . $id;
            else
            	$q = 'SELECT nome, cognome FROM careproviderpersona WHERE idutente = ' . $id;
    
          $result = $database -> conn -> query($q);
          $data = mysqli_fetch_assoc($result);

          $nome = $data['nome'];
    
          if ($role == 'lab') return $nome;
    
          if ($role == 'ass'){
    	      $nome = decryptData(deserializeData($nome));
          	$cognome = decryptData(deserializeData($data['cognome']));
          }else 
          	$cognome = $data['cognome'];	
   
          return $nome . " " . $cognome;
   	
}


function deleteAccount($id){
	
	
	/**
	* Funzione che cancella definitivamente l'account di un paziente
	* 
	* @param $id
	* @return account deleted
	*/ 
	
	// cancellazione paziente da DATABASE
	
	$idArray = getArray('id', 'altrifamiliari', 'idutente = ' . $id);
	
	foreach($idArray as $ida)
		deleteInfo('altrifamiliaripatologie', 'idfamiliari = ' . $ida);
	
	foreach($idArray as $ida)	
		deleteInfo('altrifamiliari', 'id = ' . $ida);
		
	deleteInfo('auditlog', 'idvisitatore = ' . $id . ' or idvisitato = ' . $id);
	deleteInfo('anamnesifisiologica', 'idpaziente = ' . $id);
	deleteInfo('careproviderpaziente', 'idutente = ' . $id);
	deleteInfo('data', 'idproprietario = ' . $id);
	deleteInfo('data', 'idsoggetto = ' . $id);
   	deleteInfo('dispositivi', 'idutente = ' . $id);
   	deleteInfo('effetticollaterali', 'idutente = ' . $id);
   	deleteInfo('familiarita', 'idpaziente = ' . $id . ' or idparente = ' . $id);
   	deleteInfo('farmacivietati', 'idpaziente = ' . $id);
   	deleteInfo('fattoririschio', 'idutente = ' . $id);
   	deleteInfo('gravidanze', 'idpaziente = ' . $id);
   	
   	$idEnte = getInfo('id', 'entistrutture', 'idutente = ' . $id);
   	deleteInfo('indagini', 'idente = ' . $idEnte);
   	
   	deleteInfo('entistrutture', 'idutente = ' . $id);
   	deleteInfo('messaggi', 'idsorgente = ' . $id);
	deleteInfo('messaggi', 'iddestinatario = ' . $id);
	deleteInfo('effetticollaterali', 'idutente = ' . $id);
	deleteInfo('parametrivitali', 'idpaziente = ' . $id);
	deleteInfo('procedureterapeutiche', 'idpaziente = ' . $id);
	deleteInfo('stilevita', 'idpaziente = ' . $id);
	deleteInfo('stiliconfidenziali', 'idutente = ' . $id);
	deleteInfo('pazienti', 'idutente = ' . $id);
	
	$idTerapie = getArray('id', 'terapiefarmacologiche', 'idpaziente = ' . $id);
	
	foreach($idTerapie as $idt)
		deleteInfo('terapie_farmacologiche_farmaci', 'idterapia = ' . $idt);
	
	foreach($idTerapie as $idt)
		deleteInfo('terapiefarmacologiche', 'id = ' . $idt);
		
	deleteInfo('motivazionesospensione', 'idutente = ' . $id);	
	deleteInfo('diagnosi', 'idPaziente = ' . $id);	
	deleteInfo('utentiospedali', 'idutente = ' . $id);
   	deleteInfo('vaccinazioni', 'idpaziente = ' . $id);
   	deleteInfo('visite', 'idpaziente = ' . $id);   	
   	
   	// CANCELLAZIONE PAZIENTE DA FILE Users.xml
   	$username = getInfo('username', 'utenti', 'id = ' . $id);
   	
	$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Users.xml'); 
    
          $elementsToRemove = array();
    
          foreach($xml -> Utente as $utente)
              	if ($utente -> username == $username)
                        	$elementsToRemove[] = $utente; 
            	
          foreach ($elementsToRemove as $utente) 
          	unset($utente[0]);
	    
	    	
          $xml->asXML($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Users.xml');  
    	
            	
          if (getRole($id) == 'ass') 	
              deleteInfo('utenti', 'id = ' . $id); 
    	 
          else {
		$a = new Account('Deleted Care Provider ' . $id, 'dcpp', 'no cf', 'out', 'no email');
		registerUser($a, 'id = ' . $id);
	}

}



function generateRandomString($length) {
	
          /**
          * Funzione accessoria che genera una stringa di caratteri alfanumerici
          * la cui lunghezza è indicata da length
          * 
          * @param $length
          * @return String ($randomString)
	*/  

          $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
          $charactersLength = strlen($characters);
          $randomString = '';

          for ($i = 0; $i < $length; $i++)
              $randomString .= $characters[rand(0, $charactersLength - 1)];

          return $randomString;
}


function regeneratePassword($id, $newPassword = null) {
	
	/**
          * Funzione che rigenera una password scaduta
          * o rigenerata sotto volere dell'utente
          * 
          * @param $id
          * @param $newPassword
          * 
          * @return Boolean
	*/  
	
	if (!is_numeric($id)) return false;
	
	global $database;
	$userChange = 1;	
	
	if ($newPassword == null){
             $newPassword = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
             $userChange = 0;
          }
    
          //viene calcolata la scadenza della nuova password
          $dataNuova = date('Y-m-d', strtotime(date("Y-m-d") . " + 90 days"));
    
    
          $account = new Account('new', $newPassword, 'new', 'new');
          $criptedPassword = $account -> getPassword();
          $salt = $account -> getSalt();
       
          $username = getInfo('username', 'utenti', 'id = ' . $id);
      
          //I SALT vengono salvati in locale in un file xml piuttosto che nel database per motivi di sicurezza   
          $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Users.xml');
       
	foreach($xml->Utente as $utente)
              if ($utente -> username == $username){
                   $utente -> salt = $salt;
          } 
       
          $xml->asXML($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Users.xml');
      
          $database -> conn -> query('UPDATE utenti SET password = "' . $criptedPassword . '", scadenza = "' . $dataNuova .
                               '"  WHERE id = ' . $id);  
                               
          if ($userChange == 0)  return true;                        
                               
          //ottengo l'indirizzo EMAIL dell'utente
          $to = getInfo('email', 'utenti', 'id = ' . $id);

	return true;
       
    
}



function generateDate($days){
	
          /**
          * Funzione accessoria che genera la data a partire da quella attuale + i giorni indicati da date
          * esempio: oggi -> 1/1/2015
          * generateDate(15) -> 16/1/2015
          * 
          * @param $days
          * @return date, false
	*/
	
	if (is_numeric($days))
                 return date('Y-m-d', strtotime(date("Y-m-d") . " + " . $days . " days"));
         
          return false;
}





function getExtremeDay($month, $year){
	
	/**
          * Funzione accessoria che restituisce l'ultimo giorno di un mese
          * 
          * @param $month
          * @return int, false
	*/
	
	switch($month){
		
		case 1: case 3: case 5: case 7:
		case 8: case 10: case 12:{
			
			return 31;
			break;
		}
		
		case 2: {
			
			if (isLeapYear($year) === true)
				return 29;
			else
				return 28;
				
			break;
		}
		
		case 4: case 6:
		case 9: case 11: {
			return 30;
			break;
		}

		default: return false;
	}	
}


function getAverageValues($id){
	
	
	/**
          * Funzione accessoria che calcola la media di ogni parametro vitale raccolto nell'arco di un mese
          * 
          * @param $id
          * @return array of values
	*/
	
	$arrayParam = getArray('id', 'parametrivitali', 'idpaziente = ' .  $id .  ' ORDER BY data DESC LIMIT  100');
				
	$month =  date("m");
	$year = (int)date("Y");
	$lastDay = getExtremeDay($month, $year);
		
	if (count($arrayParam) < $lastDay) return $arrayParam;

	for ($i = 1; $i < 13; $i++){
		
		$extreme = getExtremeDay($i, $year);
		$dateA = $year . '-' . $i . '-' . '01';
		$dateB = $year . '-' . $i . '-' . $extreme;
		$condition = ' and (data BETWEEN "'. $dateA .  '" AND "' . $dateB . '" )';
			
		$arrayParam = getArray('id', 'parametrivitali', 'idpaziente = ' . $id . $condition);

		$count = count($arrayParam);

		if ($count > 0)	{
			
			$altezzaM = 0;
			$pesoM = 0;
			$PA_maxM = 0;
			$PA_minM = 0;
			$FCM = 0;
			$dolore = 0;
			
			foreach($arrayParam as $idp){
				$altezzaM = $altezzaM + getInfo('altezza', 'parametrivitali', 'id = ' . $idp);
				$pesoM = $pesoM + getInfo('peso', 'parametrivitali', 'id = '  . $idp);
				$PA_minM = $PA_minM + getInfo('pressioneminima', 'parametrivitali', 'id = '  . $idp);
				$PA_maxM = $PA_maxM + getInfo('pressionemassima', 'parametrivitali', 'id = '  . $idp);
				$FCM = $FCM + getInfo('frequenzacardiaca', 'parametrivitali', 'id = '  . $idp);
				$dolore = $dolore + getInfo('dolore', 'parametrivitali', 'id = '  . $idp);
					
				deleteInfo('parametrivitali', 'id = ' . $idp);
			}
				
			$altezzaM = round($altezzaM/ $count, 2);
			$pesoM = intval($pesoM/ $count);
			
			$PA_minM = intval($PA_minM/ $count);
			$PA_maxM = intval($PA_maxM / $count);
			$FCM = intval( $FCM / $count);
			$dolore = intval($dolore / $count);
			
			$q = 'INSERT INTO parametrivitali ( idpaziente, 
									 altezza, 
									 peso, 
								         	 pressioneminima, 
									 pressionemassima, 
									 frequenzacardiaca, 
									 data, 
									 dolore)
				VALUES ( ' . $id . ', ' . $altezzaM . ', ' .  $pesoM . ', ' . $PA_minM . ', ' . $PA_maxM . ', ' . $FCM . ', "' . $dateA . '", ' . $dolore . ')';
			executeQuery($q); 
		}	
	
	}
		
	return getArray('id', 'parametrivitali', 'idpaziente = ' .  $id .  ' ORDER BY data DESC LIMIT  100');
}


function isLeapYear($year){
	
	/**
          * Funzione accessoria che stabilisce se un anno è bisestile
          * 
          * @param $year
          * @return boolean
	*/
	
	if (!is_numeric($year)) return false;
	
	return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0)));
}


function deleteInfo($table, $condition){
	
	
          /**
          * Funzione generica che cancella un dato dalla table 
          * sotto una determinata condizione (solitamente specifica l'id)
          * 
          * @param $table
          * @param $condition
          * 
          * @return Boolean
	*/

          global $database;

          $q = "DELETE FROM $table WHERE $condition";

          executeQuery($q);
    
          $q = "ALTER TABLE $table AUTO_INCREMENT = " .getNextID($table);
    
          executeQuery($q);

}


function updateInfo($element, $table, $condition){

          /**
          * Funzione accessoria che aggiorna un elemento in una table sotto determinate condizioni
          * 
          * @param $element
          * @param $table
          * @param $condition
          * 
          * @return Boolean
	*/
	
          global $database;
  
          $q = "UPDATE $table SET $element WHERE $condition";
    
	//echo "-------------------updateinfo--------------------";
          return executeQuery($q);

}

function sessionExpired(){
	
	/**
          * Funzione che verifica se la sessione di login è scaduta 
          * 
          * @return $isExpired: true, false
	*/ 
	
	global $session;  
          $isExpired = false;

	// 60 * 60 = 1h di sessione, dopodiché scade
	// Per cambiare la durata della sessione, modificare uno dei due parametri (indica i minuti)
          if (isset($_SESSION['Login time']) && (time() - $_SESSION['Login time'] > 60 * 60)) {

              session_unset();      
              session_destroy();   

              $isExpired = true;
          }
          else $isExpired = false;

	// Contrasta l'attacco 'Session Fixation'
	// Se la sessione è scaduta, ne rigenera una completamente nuova
	
          if (!isset($_SESSION['Created']))
              $_SESSION['Created'] = time();

          else if (time() - $_SESSION['Created'] > 60 * 60) {

              session_regenerate_id(true);     
              $_SESSION['Created'] = time();   
          }

          return $isExpired;

}



function expiredDate($date){
	
	/**
          * Verifica se la data indicata da date è precedente a quella attuale
          * 
          * @param $date
          * @return Boolean
	*/ 
	

	if (getDays($date) < 1) return true;
		 else return false;
		
}


function externalPermission($id, $section, $action, $element = null){
	
	/**
	* Estrae le informazioni di un determinato permesso
	* 
	* @param $id, $section (del FSEM), $action, $element (obblighi / visibilità)
	* @return Array of strings or  String or Int
	*/ 
	
	deleteExpiredPermissions();
	
	if (isset($_GET["pz_Id"]))
		$pz_id = $_GET["pz_Id"];
	else
		$pz_id = getMyID();
	
   $policy = getInfo('contenuto','data','nome = "' . $section . '" and idproprietario = ' . $id . ' and idsoggetto = ' . $pz_id);

	if (!$policy)
	      $policy = getInfo('contenuto','data', 'nome = "Fascicolo completo" and idproprietario = ' . $id . ' and idsoggetto = ' . $pz_id);
                      
          if (!$policy) return false;              

          $resource = new Resource();
    
          $policy = unserialize(urldecode($policy));
          $resource -> addPolicy($policy); 
   
	$policySet = $resource -> getPolicies();
          $policies = $policySet -> getPolicies();
    
          $actionPolicy = $policy -> getAction();
          $actionType = $actionPolicy -> getType();
	
	if ($action != $actionType and $actionType != 'Qualsiasi azione') return false;	

	$confidentiality = $policy -> getConfidentiality();
	$obligations = $policy -> getObligations();
	
	if ($element == 'Confidentiality') return $confidentiality;
	if ($element == 'Obligations') return $obligations;
	
	return 'Permit';
}

//$data è un oggetto con metodo getValue
function encryptData($data){
	

	/**
          * Funzione che cripta un dato tramite algoritmo RIJNDAEL a 256 bit
          * calcola l'IV associato 
          * La codifica del dato viene calcolata sulla base del vettore IV + chiave privata
          * 
          * @param $data
          * @return instance of Data
	*/  
	
	$value = $data -> getValue();

	if ($value == null) return false;

          $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
          $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	$ks = mcrypt_enc_get_key_size($td);
          $key = substr(md5('la mia super chiave privata'), 0, $ks);

          mcrypt_generic_init($td, $key, $iv);

          $encrypted = mcrypt_generic($td, $value);

          mcrypt_generic_deinit($td);
          mcrypt_generic_init($td, $key, $iv);

          mcrypt_generic_deinit($td);
          mcrypt_module_close($td);

          $data -> setValue($encrypted);
          $data -> setIV($iv);
    
          return $data;
   
}

function filterInfo($string) {
	
	/**
          * Filtra ogni dato di input digitato da tastiera
          * Contrasta SQLInjection, HTMLInjection
          * 
          * @param $string
          * @return $string
	*/ 

          $string = stripslashes($string);
          $string = mysql_real_escape_string($string);
          $string = htmlentities($string, ENT_COMPAT, 'UTF-8');

          return $string;
}

function existingEmail($email){

	/**
          * Funzione che verifica se la mail non è già stata scelta o associata ad un account
          * 
          * @param $email
          * @return Boolean
	*/  

          $arrayEmail = getArray('email', 'utenti', 'id > 0');
          
          if (count($arrayEmail) > 0)
          	foreach($arrayEmail as $id){
			$email2 = decryptData(deserializeData(getInfo('email', 'utenti', 'id = ' . $id)));
			
			if ($email2 == $email)
				return true;
		}
		
	return false;

}


function existingUsername($username){

	/**
          * Funzione che verifica se l'username scelto è disponibile
          * 
          * @param $username
          * @return Boolean
	*/  

          $test = getInfo('username', 'utenti', 'username = "' . $username . '"');
        
          if ($test == $username) return true;
          return false;

}


function existingElement($element, $type){

          /**
          * Funzione che verifica se l'elemento di tipo type è già presente nel database
          * 
          * @param $element, $type
          * @return Boolean
          */  

          $arrayID = getArray('id', 'utenti', 'id > 0');
          
          if (count($arrayID) > 0)
		foreach($arrayID as $id){
			$email = decryptData(deserializeData(getInfo('email', 'utenti', 'id = ' . $id)));
			$cf = decryptData(deserializeData(getInfo('codicefiscale', 'utenti', 'id = ' . $id)));
			$pIVA = decryptData(deserializeData(getInfo('partitaiva', 'utenti', 'id = ' . $id)));
			$pec = decryptData(deserializeData(getInfo('pec', 'utenti', 'id = ' . $id)));
		}
          
          switch($type){
   	
   		case 'email':{
   			if ($email == $element)
				return true;	
		}
		
		
		case 'username':{
			
			 $test = getInfo('username', 'utenti', 'username = "' . $element . '"');
        
         			 if ($test == $element) 
         			 	return true;
		}
		
		
		case 'cf':{
			if ($cf == $element)
				return true;	
		}
		
		case 'pIVA':{
			if ($pIVA == $element)
				return true;	
		}
		
		
		case 'PEC':{
		          if ($pec == $element)
				return true;	
		}
		
		default: return false;
   	}

}

function getExistingElementId($element, $type){

          /**
          * Funzione che verifica se l'elemento di tipo type è già presente nel database
          * 
          * @param $element, $type
          * @return Boolean
          */  
		$ret = false;
        $arrayID = getArray('id', 'utenti', 'id > 0');
          
        if (count($arrayID) > 0)
		foreach($arrayID as $id){
			$email = decryptData(deserializeData(getInfo('email', 'utenti', 'id = ' . $id)));
			$cf = decryptData(deserializeData(getInfo('codicefiscale', 'utenti', 'id = ' . $id)));
			//$pIVA = decryptData(deserializeData(getInfo('partitaiva', 'utenti', 'id = ' . $id)));
			//$pec = decryptData(deserializeData(getInfo('pec', 'utenti', 'id = ' . $id)));
		
          
			switch($type){
   	
				case 'email':{
					if ($email == $element)
						return $id;	
				}
				
				
				case 'username':{
					
					 $test = getInfo('username', 'utenti', 'username = "' . $element . '"');
				
							 if ($test == $element) 
								return $id;	
				}
				
				
				case 'cf':{
					if ($cf == $element)
						return $id;	
				}
				
				case 'pIVA':{
					if ($pIVA == $element)
						return $id;		
				}
				
				
				case 'PEC':{
						if ($pec == $element)
							return $id;		
				}
				
				default: 
					$ret = false;
			}
		}
		return $ret;

}

function registerUser($account, $condition = null) {
	
	/**
          * Funzione che registra un utente. Restituisce true se i parametri indicati sono validi
          * 
          * @param $account
          * @return Boolean
	*/  
	
	global $database;
	
	//genero un account la cui password vale 90 giorni
          $date = generateDate(90);
    
          //cripto il codice fiscale e la mail
          $cf = new Data($account -> getCF());
          $cf = serializeData(encryptData($cf));
    
          $email = new Data($account -> getEmail());
          $email = serializeData(encryptData($email));

	if ($condition == null)
                $q = 'INSERT INTO utenti (username, password, pin, codicefiscale, stato, scadenza, codiceruolo, email) VALUES ("' . 
                $account -> getUsername() . '", "' . $account -> getPassword() . '", "' . $account -> getPin() . '", "' . $cf . '", 0, "' . 
                $date . '", "' . $account -> getRole() . '", "' . $email . '")';
         
    	else
               $q = 'UPDATE utenti SET username = "' . $account -> getUsername() . '", password = "' . $account -> getPassword() 
               .  '", codicefiscale = "' . $cf . '", stato = 0, scadenza = "' . $date . '", codiceruolo = "' . $account -> getRole() 
               . '", email = "' . $email . '" WHERE ' . $condition;
         
          executeQuery($q);  
         
          //salvo il Salt in locale nel file Users.xml 
          $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT']."modello PBAC/Users.xml");

          $newSalt = $xml->addChild('Utente');
          $newSalt->addChild('username',$account -> getUsername());
          $newSalt->addChild('salt', $account -> getSalt());
   

          $dom = new DOMDocument;
          $dom->preserveWhiteSpace = false;
          $dom->formatOutput   = true;
          $dom->loadXML($xml->asXML());

          $f = fopen($_SERVER['DOCUMENT_ROOT']."modello PBAC/Users.xml","w");
          fwrite($f,$dom->saveXML());
          fclose($f);    
    
      
}  



function setEmptyTable($table){
	
	/**
          * Funzione accessoria che svuota una tabella
          * 
          * @param $table
          * @return Boolean
	*/  

          global $database;

          $insertedID = getArray('id', $table, 1);
    
          if ($insertedID == 0) return false;

          foreach ($insertedID as $id)
              deleteInfo($table, 'id = ' . $id);

          $q = 'ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1';
    
          return executeQuery($q);
}



function getAge($data, $end = null){
	
          /**
          * Funzione accessoria che calcola gli anni di un utente in base alla data di nascita
          * 
          * @param $data
          * @return Int ($age), false
	*/ 
	
	if ($end == null)
          	$age = date_diff(date_create($data), date_create('today'))->y;
          else
          	$age = date_diff(date_create($data), date_create($end))->y;
    
          if (is_numeric($age)) return $age;
    	      else return false;
}



function getDays($date){
	
          /**
          * Funzione accessoria che calcola i giorni che intercorrono tra la data in input e quella odierna
          * 
          * @param $data
          * @return Int ($days), false
	*/ 
	
	$days = (strtotime($date) - strtotime(date("Y-m-d"))) / (24 * 60 * 60 );
    
         if (is_numeric($days)) return intval($days);
    	      else return false;
}

function getArrayAssociative($element, $table, $condition){
 
    	global $database;
    	$dbarray = array();

    	$q = "SELECT $element from $table where $condition";

		$result = mysqli_query($database -> conn, $q);
	
    	if(!$result)
        		return false;

	for ($set = array (); $res = mysqli_fetch_assoc($result); $set[] = $res);
	$dbarray = $set;
	
	
	return $dbarray;
}

function getArrayNoCondition($element, $table){

    /**
     * Funzione accessoria che restituisce un insieme di elementi estratti dalla table
     *
     * @param $element
     * @param $table
     *
     * @return array (dbarray), false
     */

    global $database;
    $dbarray = array();

    $q = "SELECT $element from $table";

    $result = mysqli_query($database -> conn, $q);

    if(!$result)
        return false;

    while ($res = mysqli_fetch_assoc($result)) {
        array_push($dbarray, $res[$element]);
    }

    return $dbarray;
}


function getArray($element, $table, $condition){
 
	/**
          * Funzione accessoria che restituisce un insieme di elementi estratti dalla table sotto condition
          * 
          * @param $element
          * @param $table
          * @param $condition
          * 
          * @return array (dbarray), false
	*/ 

          global $database;
          $dbarray = array();

          $q = "SELECT $element from $table where $condition";

	      $result = mysqli_query($database -> conn, $q);
	
          if(!$result)
              return false;

		while ($res = mysqli_fetch_assoc($result)) {
			array_push($dbarray, $res[$element]);
		}
	
	return $dbarray;
}


function getInfo($element, $table, $condition){
	
	
	/**
          * Funzione accessoria che restituisce un singolo elemento da una table sotto condition
          * 
          * @param $element
          * @param $table
          * @param $condition
          * 
          * @return Int ($ris[$element]), false
	*/
	
          global $database;

          $q = "SELECT $element FROM $table WHERE $condition"; 
          $result = $database -> conn -> query($q); 

          //verifica che ci siano elementi
          if($result == false)
              return false;
          else{
              $res = mysqli_fetch_assoc($result);
              return $res[$element];

          }

}


function openImage($name = null, $type = null, $owner = null){
	
	
	/**
          * Funzione che restituisce il riferimento ad un'immagine
          * 
          * @param $name  nome del file
          * @param $type  tipo di dato
          * 
          * @return image
	*/
	
	global $database;
	
	$exists = false;
	
	if (isset ($_GET["pz_Id"]))
	{
		$pz_Id = $_GET["pz_Id"];
		if ($owner == null) 
			$owner =  $pz_Id;
	}
	else
		if ($owner == null) $owner = getMyID();
	
	if ($name == null)
		$q = "SELECT * FROM data WHERE idproprietario = " . $owner . " and tipologia = '$type'";
	else
		$q = "SELECT * FROM data WHERE idproprietario = " . $owner . " and tipologia = '$type' and nome = '$name'";
		
	$result = $database -> conn -> query($q);

	while($row = mysqli_fetch_assoc($result)){
		$b64Src = "data:" . $mime . ";base64," . base64_encode($row['contenuto']);
		$exists = true;
	}
	
	if ($exists == false) return false;
	
	return $b64Src;

}

//Get diary front and back images
function openImageDiary($id, $type){
	
	global $database;
	$b64Src = "";
	$q = "SELECT * FROM taccuino WHERE id = ". $id;
		
	$result = $database -> conn -> query($q);

	while($row = mysqli_fetch_assoc($result)){
		if($type = "front")
			$b64Src = "data:" . $mime . ";base64," . base64_encode($row['imgfront']);
		else
			$b64Src = "data:" . $mime . ";base64," . base64_encode($row['imgfront']);
	}
	
	return $b64Src;

}


function getNextID($table){
	
	/**
          * Funzione accessoria che restituisce l'ultimo ID inserito + 1.
          * 
          * @param $table
          * @return Int ($ris['MaximumID'] + 1), false
	*/

          global $database;

          $q = "SELECT MAX(id) as MaximumID FROM $table";
          $result = $database -> conn -> query($q);

          //verifica che ci siano elementi
          if(!$result)
              return false;
   
          while ($res = mysqli_fetch_assoc($result)) {

              return $res['MaximumID'] + 1;
          }
    
}


function getTime($param){
	
	/**
          * Funzione accessoria che restituisce la data attuale specificando date in input
          * o il time stamp attuale specificanto time in input 
          * 
          * @param $param
          * @return date ($date), false
	*/

         $date = new DateTime();

         if ($param == 'time')
              $date = $date->format('Y-m-d H:i:s');
         elseif ($param == 'date')
              $date = $date->format('Y-m-d');
              else return false;

         return $date;
}


function isLogged(){
	
	/**
          * Funzione che stabilisce se l'utente è loggato
          * @return Boolean
	*/
	
          return $_SESSION['loginStatus'];
}


function kinship($id){
	
	
	/**
	* Verifica che il paziente identificato da $id abbia acconsentito alla creazione della rete familiare
	* 
	* @param $id
	* 
	* @return int ($flag) or false
	*/ 
	
	if ($id == null) return false;
	
          $flag = getInfo('id', 'data', 'idproprietario = ' . $id . ' and nome = "Parentela"');
          return $flag;
}



function getMyID(){
	
	/**
          * Funzione che restituisce il proprio ID
          * @return Int (userid)
	*/

          return $_SESSION['id'];
}

function getMyIP(){
	
	/**
          * Funzione che restituisce il proprio IP
          * @return Int (ip)
	*/
	
          return $_SERVER['REMOTE_ADDR'];
}


function setCurrentID($id){
	
	/**
          * Funzione che salva l'id del paziente visitato
          * @param $id
	*/
	
          $_SERVER['Current patient'] = $id;
}


function getCurrentID(){
	
	/**
          * Funzione che restituisce l'id del paziente visitato
          * 
          * @return id
	*/
	
	$myRole = getRole(getMyID());
	
	if ($myRole == 'ass' or $myRole == 'amm')
		return getMyID();
	else
   		//return $_SERVER['Current patient']; 
   		return 1;
}


function getRole($id, $description = null){
	
	/**
          * Funzione che restituisce il ruolo associato all'id fornito in input
          * 
          * @param $id
          * @return Int ($role), false
	*/
	
	
          $role = getInfo('codiceruolo', 'utenti', 'id = ' . $id);
		  
          if ($description == null){   
              if($role != null) 
                  return $role;
          }else
    	      return  getInfo('descrizione', 'ruoli', 'codice = "' . $role . '"');       
       
          return false;
}


function getCareProvider(){
	
	/**
          * Funzione che restituisce il proprio care provider associato
          * @return Int (id careprovider), false
	*/

	$id = getMyID();
	$role = getRole($id);
	
	if ($role == 'ass')
             return getInfo('idcpp', 'careproviderpaziente', 'idutente = ' . $id);
    
          return false;
}



function setBannedDrugs($id, $drugsCode, $reason){
	
	/**
          * Funzione accessoria che imposta i farmaci da non somministrare al paziente associato all'ID in input
          * 
          * @param $id
          * @param $drugsCode
          * @param $reason
          * 
          * @return Boolean
	*/

          global $database;

          $q = "SELECT id from farmacivietati where codiceATC= '$drugsCode' and idpaziente = $id";
          $result = $database -> conn -> query($q);

          if($result -> num_rows == 1){
              $q2 = "INSERT INTO farmacivietati (idpaziente, codiceATC, motivo) VALUES ($id, '$drugsCode', '$reason')";
              $database -> conn -> query($q2);
              return true;
          }
    
          return false;
}




function setAuditAction($idVisitatore = null, $action, $error = null){
	
	/**
          * Funzione che registra l'azione effettuata da action
          * 
          * @param $idVisitatore
          * @param $idVisitato
          * @param $action
          * 
          * @return Boolean
	*/

         global $database;
    
          $role = getRole(getMyID()); 
    
          if ($idVisitatore == null) $idVisitatore = -1;
    
          if ($error != null or ($role == 'ass')) 
    	      $idVisitato = $idVisitatore;
          else $idVisitato = 1; // implementare la funzione in cui si prende l'ID del paziente collegato al CPP

    	 
          $q = 'INSERT into auditlog (ip, idvisitatore, idvisitato, operazione, dispositivo, datalog) VALUES("' 
    	       . getMyIP() . '", ' 
    	       . $idVisitatore . ', '  
    	       . $idVisitato . ',"' 
    	       . $action . '", "' 
    	       . $_SERVER['HTTP_USER_AGENT'] . '", "' 
    	       . getTime('time') . '")'; 	  
    
          return executeQuery($q);

}



function sendMessage($data){
	
	/**
          * Funzione che invia un messaggio a subject
          * 
          * @param $data
          * 
          * @return Boolean
	*/

          global $database;

          $q = "INSERT INTO messaggi(messaggio, data, stato) VALUES ('" . $data . "', '" . getTime('time') . "', 0)";
   
          return executeQuery($q);

}

function sendPrivateMessage($data,$idsorgente, $iddestinatario,$idconversazione, $firstsecond){

        global $database;

        $lastId = 0;
		  
		if(getInfo('id','conversazioni','id='.$idconversazione) == false){
			$q2 = "INSERT INTO conversazioni(idutente_first, idutente_second,data,stato_first) VALUES ('".$idsorgente."','".$iddestinatario."','".getTime('time')."','1')";
			//executeQuery($q2);
			if ($database->conn->query($q2) == true)
				$lastId = $database->conn->insert_id;
		}else{		
			if($firstsecond == 1){
				updateInfo('stato_second = 0','conversazioni','id='.$idconversazione);
			}else
				updateInfo('stato_first = 0','conversazioni','id='.$idconversazione);
		}
			
		$mexTime = getTime('time');
		if($idconversazione == -1)
			$q = "INSERT INTO messaggi(idsorgente, iddestinatario,messaggio, data, stato, idconversazione) VALUES ('".$idsorgente."','".$iddestinatario."','".$data."', '" . $mexTime . "', 0,'".$lastId."')";			
		else
			$q = "INSERT INTO messaggi(idsorgente, iddestinatario,messaggio, data, stato, idconversazione) VALUES ('".$idsorgente."','".$iddestinatario."','".$data."', '" . $mexTime . "', 0,'".$idconversazione."')";
        
		executeQuery($q);
		if($idconversazione == -1)
			return array("id" => $lastId,"time" =>$mexTime);
		else
			return array("id" => $idconversazione,"time" =>$mexTime);

}


function getStatusMessage($id){
	
	/**
          * Funzione che restituisce lo stato di un messaggio (Letto / Non letto)
          * 
          * @return string
	*/
	
	return getInfo('stato', 'messaggi', 'id = ' . $id);

}

function updateStatusMessage($idMex){
	
	/**
          * Funzione che imposta a Letto lo stato di un messaggio
          * 
          * @param id 
	*/
	
	
	updateInfo('stato = 1', 'messaggi', 'id = ' . $idMex);
}


function generatePin($id){
	
	/**
          * Funzione accessoria che genera un pin associato a un utente identificato da id
          * il pin viene utilizzato nelle funzioni critiche come accesso e salvataggio di dati sensibili
          * 
          * @param $id
          * @return Boolean
	*/
	
	$pin = generateRandomString(6);
	// il pin salvato nel database è criptato (e non è possibile decriptarlo)
	
	$pin = hash('sha512', $pin);
	$q = 'INSERT INTO utenti (pin) VALUES("'.$pin.'") WHERE id = ' . $id;
	
	return executeQuery($q);

}


function verifyPin($id, $pin1){
	
	/**
          * Funzione che verifica se il pin digitato è corretto
          * 
          * @param $id 
          * @param $pin1
          * 
          * @return Boolean
	*/
	
	global $database;
	
	$q = 'SELECT pin FROM utenti WHERE id = ' . $id;
	
	$result = $database -> conn -> query($q);
	
	if(!$result) return false;
	
	$d = $result -> fetch_all(MYSQLI_ASSOC);
	
	foreach($d as $ris)
              $pin2 = $ris['pin'];
        
          if (codeData($pin1) == $pin2) return true;
    	      else
          return false;

}


function executeQuery($q){
	
	/**
           * Funzione generica che esegue la query fornita in input
           * 
           * @param $q
           * @return Boolean
	*/

	global $database;
	
	try{
	   $database -> conn -> query($q);
	   return true;
	}
	catch (Exception $e){
		return false;
	}	

	
}




function getHospital($id){
	
	/**
          * Funzione che restituisce la struttura sanitaria associata all'utente riconosciuto tramite l'id in input
          * 
          * @param $id
          * @return Int ($ASL), false
	*/

          $ASL = getInfo('idospedale', 'utentiospedali', 'idutente = ' . $id);

          if(!$ASL) return false;
        
          return $ASL;

}

function getNewMessages($id = null){
	
	/**
          * Funzione che restituisce i messaggi non letti rivolti a $source ordinati per data
          * Svolge l'operazione di cancellazione messaggi scaduti (letti da più di 6 giorni)
          * 
          * @return array 
	*/
	

          global $database;
          $dbarray = array();
    
          if ($id == null) $id = getMyID();
    
          // epurazione dei messaggi letti scaduti
	
	//$q = 'SELECT id, data FROM messaggi WHERE stato = 1';
	$q = 'SELECT id, data FROM messaggi WHERE stato = 1 and iddestinatario='.$id;
	
	$result = $database -> conn -> query($q);
	
	if($result){
		 while ($res = mysqli_fetch_assoc($result)) {
			$id = $res['id'];
        	                    $data = $res['data'];
        
        	                    $days = getDays($data);
        
        	                  if ($days < 6) 
								  deleteInfo('messaggi', 'id = ' . $id);
                    }
    }
	

          //$q = 'SELECT * FROM messaggi WHERE stato = 0 and id <> 12 and id <> 13 ORDER BY data DESC';
		  $q = 'SELECT * FROM messaggi WHERE stato = 0 and iddestinatario= '.$id.' ORDER BY data DESC';

          $result = $database -> conn -> query($q);

        if(!$result)
			return false;
		else 
    	    while ($res = mysqli_fetch_assoc($result)) {
        	    $data = deserializeData($res['messaggio']);
        	    $destinatario = $data -> getSubject();
        	
        	    if ($destinatario == $id)
					array_push($dbarray, $res);
			}
         
    
        return $dbarray;

}

function getOtherMessages(){
	
	/**
          * Funzione che restituisce tutti i messaggi non scaduti e letti rivolti al destinatario, ordinati per data decrescente
          * 
          * @return array 
	*/
	
	global $database;
	$dbarray = array();
	
	$q = 'SELECT * FROM messaggi WHERE stato = 1 ORDER BY data DESC';
          $result = $database -> conn -> query($q);

          if(!$result)
                    return false;
          else{
                    while ($res = mysqli_fetch_assoc($result)) {
        	                  $data = deserializeData($res['messaggio']);
        	                  $destinatario = $data -> getSubject();
        	
        	            if ($destinatario == getMyID())
				array_push($dbarray, $res);
		}
          }
    
          return $dbarray;

}

function getAllReceivedSentMessages(){
	
	global $database;
	$dbarray = array();
	
	$q = 'SELECT * FROM messaggi WHERE iddestinatario = '.getMyID().' OR idsorgente = '.getMyID().' ORDER BY data DESC';
          $result = $database -> conn -> query($q);

          if(!$result)
                    return false;
          else{
                while ($res = mysqli_fetch_assoc($result)) {
        	        $data = deserializeData($res['messaggio']);        	
					array_push($dbarray, $res);
				}
          }
    
    return $dbarray;

}

function getConversationMessages($idConv){
	
	global $database;
	$dbarray = array();
	
	$q = 'SELECT * FROM messaggi WHERE idconversazione= '.$idConv.' ORDER BY data ASC';
          $result = $database -> conn -> query($q);

          if(!$result)
                    return false;
          else{
                while ($res = mysqli_fetch_assoc($result)) {
					array_push($dbarray, $res['id']);
				}
          }
    
    return $dbarray;

}


function getConversations(){
	
	global $database;
	$dbarray = array();
	
	$q = 'SELECT * FROM conversazioni WHERE idutente_first = '.getMyID().' OR idutente_second = '.getMyID().' ORDER BY data DESC';
          $result = $database -> conn -> query($q);

          if(!$result)
                    return false;
          else{
                while ($res = mysqli_fetch_assoc($result)) {        	        
					array_push($dbarray, $res['id']);
				}
          }
    
    return $dbarray;

}




function loadPolicies($what, $flag = 0){
	
          /**
	* Elimina tutte le policy scadute (se il tempo non è stato impostato a 'indeterminato')
	* Carica le policy sulla condizione espressa dalla variabile $what 
	* Se è numerica, rappresenta l'ID utente, altrimenti il nome della policy (che coincide per semplicità
	* col nome del documento su cui è stata stipulata una policy)
	* 
	* @return Resource, false
	*/
	
	deleteExpiredPermissions();
   
	$loadedPolicies = array();

	if (is_numeric($what)){
		if (getRole(getMyID()) == 'ass' or $flag == 1)
        	$storedPolicies = getArray('contenuto', 'data', 
                                   	   'tipologia = "policy" and idproprietario = ' . $what);
        else
            $storedPolicies = getArray('contenuto', 'data', 
                                   	   'tipologia = "policy" and idsoggetto = ' . $what);	
          }
          else{
    	
    	// verifica se c'è una policy di tipo 'Fascicolo completo' associata al richiedente
    	$idF = getInfo('id', 'data', 'nome = "Fascicolo completo" and idsoggetto = ' .getMyID());
	                                
	    if ($idF > 0) 
	        $storedPolicies = getArray('contenuto', 'data', 
                                       'tipologia = "policy" and nome = "Fascicolo completo" and idsoggetto = ' . getMyID());
        else
        	$storedPolicies = getArray('contenuto', 'data', 
                                       'tipologia = "policy" and nome = "' . $what  . 
                                       '" and idsoggetto = ' . getMyID());                  
          }
    
          $resource = new Resource();

          if (!empty($storedPolicies)){
                    foreach($storedPolicies as $policy){
                        $pol = unserialize(urldecode($policy));
                        $resource -> addPolicy($pol); 
                    }    
              return $resource;     
          }
    
          return false;
}

//contenuta in modelloPBAC/utility descritta a pg 116 della tesi di Faggiani
function policyInfo($id, $parameter = null){
	
	/**
	* Restituisce le informazioni delle policy identificate da $identifier
	* 
	* @param $id
	* @param $section
	* @param $parameter
	* @return mixed values
	*/ 
	
	if (is_numeric($id))
		$policy = getInfo('contenuto', 'data', 'id = ' . $id);	
	else
		$policy = getInfo('contenuto', 'data', 'idproprietario = ' . getMyID() . ' and nome = "' . $id . '"');	
       
          $resource = new Resource();

          if ($policy == false) return false;
    
          $policy = unserialize(urldecode($policy));
          $actionPolicy = $policy -> getAction();
	$action = $actionPolicy -> getType();	
   
	$environment = $policy -> getEnvironment();
	$timeLimit = $environment -> getDate();
	$confidentiality = $policy -> getConfidentiality();
	$obligations = $policy -> getObligations();
    
    
          /**  restituzione valori:
	* 
	*   0: cpp
	*   1: risorsa
	*   2: azioni
	*   3: scadenza
	*   4: confidenzialita
	*   5: obblighi
	*/
    
          $arrayResults = array();
    
          if ($parameter == null){
		array_push($arrayResults, getInfo('idsoggetto', 'data', 'id = ' . $id));	
   		array_push($arrayResults, getInfo('nome', 'data', 'id = ' . $id));	
    	          array_push($arrayResults, $action);
    	          array_push($arrayResults, $timeLimit);
    	          array_push($arrayResults, $confidentiality);
    	          array_push($arrayResults, $obligations);
    
    	         return $arrayResults;
	}
	elseif ($parameter == 'Confidenzialita') return $confidentiality;
	elseif ($parameter == 'Obblighi') return $obblighi;
 
}



function deleteExpiredPermissions() {
	

	/**
          * Cancella tutte le policy scadute
          * 
          * @return Boolean
	*/  
	
          $storedPolicies = getArray('contenuto', 'data', 'tipologia = "policy"');
          $idPolicy = getArray('id', 'data', 'tipologia = "policy"');
    
    	if (empty($storedPolicies)) return false;
    	
          foreach($storedPolicies as $policy){
                    $pol = unserialize(urldecode($policy));
        
                    $env = $pol -> getEnvironment();
    	          $date = $env -> getDate();
    	          $id = $pol -> getID();
       		
    	      if (expiredDate($date) == true)
    		deleteInfo('data', 'id = ' . $id); 
    }
    
    return true;
  
}


function setAccess($name = null,
                   $subject = null,
                   $action = null,
                   $confidentiality = null, 
                   $obligations = null, 
                   $timeLimit = null, 
                   $effect = null,
                   $owner = null){
	
          /**
	* Imposta un permesso su un soggetto e su una risorsa se ho l'autorizzazione a farlo
	* Si stabiliscono le azioni permesse, la descrizione della policy, il livello massimo di visibilità dei dati, 
	* l'ambiente (tempo = timeLimit) in cui vive la policy
	* se non si stabilisce nessun effetto, per default è 'Permit', altrimenti è 'Deny'
	* Il parametro owner serve per il caricamento di policy da file XML dal quale si estrae il proprietario di ogni policy
	* 
	* @param $name
	* @param $subject
	* @param $action
	* @param $confidentiality 
	* @param $timeLimit
	* @param $effect
	* @param $owner
	* 
	* @return Boolean
	*/


	if (getRole(getMyID()) != 'ass' and !$owner) return false;

	                     
	
	if ($owner == null)
	    $owner = getCurrentID();
	
	if ($effect == null) $effect = 'Permit';
	if ($timeLimit == null) $timeLimit = 60;
	
	// verifica che non ci sia già una policy per la stessa risorsa rivolta allo stesso CPP
	$id = getInfo('id', 'data', 'nome = "' . $name . '" and idproprietario = ' 
	                    . $owner . ' and idsoggetto = ' . $subject);
	if ($id > 0){ 
		  deleteInfo('data', 'id = ' . $id);
		  $operation = 'Modify';
	}
	else $operation = 'Create';	  
    
   
          if ($name == 'Fascicolo completo'){
    	
    	      $idArray = getArray('id', 'data', 'nome <> "Fascicolo completo" and idproprietario = ' 
	                                           . $owner . ' and idsoggetto = ' . $subject);

	    if ($idArray != false)
	    	foreach($idArray as $id)
	    		if ($id > 0)
						deleteInfo('data', 'id = ' . $id);
	} 
          else{
    	      $idF = getInfo('id', 'data', 'nome = "Fascicolo completo" and idproprietario = ' 
	                    . $owner . ' and idsoggetto = ' . $subject);
	                                
	          if ($idF > 0)
				return false;
          }
    
	$arrayMatch = array();
	$target = new Target();
	$rule = new Rule();
	$action = new Action($action);
	$policy = new Policy();

	$environment = new Environment(generateDate($timeLimit));
          $resource = new Data(null, $name, $subject);
	
	$policyID = getNextID('data');

	/**
	* 
	* l'ID del match è sempre 1 perché la policy è stipulata su una singola risorsa o su una sezione del FSE 
	* la quale da sola raccoglie un insieme di risorseStesso ragionamento per le regole. l'ID è sempre 
	* 1 in quanto l'esito del match se positivo ha sempre effetto Permit
	* Ai pazienti non è stata data l'opportunità infatti di stipulare policy in cui si nega l'accesso a risorse
	* Per default se non vi è una policy su un dato, l'effetto è Deny. 
	* Questo evita la stipulazione di policy contraddittorie (due policy sulla stessa risorsa, stessa azione, 
	* con effetti diversi)
	*/ 
	
          $match = new Match(1, $subject, $resource);         
          $target -> addMatches(array($match));
	$rule -> setTarget($target)
                    -> setId(1)
                    -> setEffect($effect)
                    -> setAlgorithm( new OnlyOneApplicable() );
          
          $policy -> setAlgorithm( new OnlyOneApplicable() )
                        -> setEnvironment($environment) 
                        -> setId($policyID)
                        -> addRule($rule)
                        -> addAction($action);
                       
          $policy -> setObligations($obligations);
                
          if ($confidentiality > -1)
              $policy -> setConfidentiality($confidentiality);
          else
              $policy -> setConfidentiality(3);                 
    
    
         savePolicy($name, $policy, $subject, $operation, $owner); 
   
         return true;      
}


function grantKinshipNetwork(){
	
	
	/**
	* Funzione che autorizza alla realizzazione di una rete familiare
	* 
	* @return boolean
	*/ 
	
	
	// verifica che il permesso non sia mai stato concesso
	
	$flag = kinship(getMyID());
	if ($flag > 0) return false;
	
          global $database;
    
          $q = 'INSERT into data (nome, idproprietario, data) VALUES ("Parentela", ' . getCurrentID() . ', "' . getTime('time') . '")';
    
          return executeQuery($q);
}

function insertDesease($type, $description = null){
	
	/**
          * Funzione che inserisce una patologia nel database
          * 
          * @param $type
          * @param $description
          * 
          * @return boolean
	*/
	
	global $database;
	
	$flag = getInfo('id', 'patologie', 'tipologia = "' . $type . '"');
	if ($flag > 0) return false;
	
	if ($description != null)
		$q = 'INSERT INTO patologie (tipologia, descrizione) VALUES ("' . $type . '", "' . $description . '")';
	else
		$q = 'INSERT INTO patologie (tipologia) VALUES ("' . $type . '")';
		
	return executeQuery($q);

}

function cercaCp($term){
	$a_json = array();
	$a_json_row = array();
	$query="select nome from careproviderpersona where nome LIKE '%$term%'";
if ($data = executeQuery($query)) {
    while($row = mysqli_fetch_array($data)) {
        $name = htmlentities(stripslashes($row['nome']));
        $a_json_row["value"] = $name;
        $a_json_row["label"] = $name;
        array_push($a_json, $a_json_row);
    }
}
echo json_encode($a_json);
flush();
}



function inserisciDiagnosi($idPaziente,$idCareProviderP,$nomePatologia,$stato,$conf,$nomeNuovoCp,$cognomeNuovoCp){
	global $database;
	if($idCareProviderP==-1){ // Vuol dire che il care provider è stato inserito manualmente e non selezionato
		inserisciCp(-1,$nomeNuovoCp,$cognomeNuovoCp); // Lo aggiungo alla tabella careproviderpersona
		//Selezionando max(id) ottengo l'ultimo id inserito, ovvero quello del cp appena aggiunto
		$idCareProviderP = getInfo('max(id)', 'careproviderpersona', '1');
	}
	$data=time();
	$q = 'insert into diagnosi_innodb (idPaziente, idCareProviderP, nomePatologia, dataIns, stato, conf) values ('.
			$idPaziente.','.$idCareProviderP.',"'.$nomePatologia.'",CURRENT_TIMESTAMP,'.$stato.','.$conf.')';
	
	executeQuery($q);
	return ($q);
}

function modificaDiagnosi($id, $idCareProviderP, $stato, $conf){
	global $database;
	$q = 'update diagnosi_innodb set idCareProviderP='.$idCareProviderP.', conf='.$conf.',stato='.$stato.' where id='.$id;
	executeQuery($q);
	return $q;
}

function eliminaDiagnosi($id, $idutente){
	global $database;
	modificaStatoDiagnosi($id,3);
	$q = 'insert into diagnosiEliminate (idutente, diagnosi_id) values ("'.$idutente.'","'.$id.'")';
	executeQuery($q);
	return $q;
}

function inserisciCp($idUtente,$nome,$cognome){
	global $database;
	$q = 'insert into careproviderpersona (idutente, nome, cognome) values (-1,"'.$nome.'","'.$cognome.'")';
	executeQuery($q);
	return getInfo('max(id)', 'careproviderpersona', '1');
}

function getCpId($nome, $cognome, $pzId){
	$mieiCp = array();
	$mieiCp = getArray('idcpp', 'careproviderpaziente', 'idutente='.$pzId);
	$n = count($mieiCp);

	for($i=0; $i<$n; $i++){
		
		$nomeCp = getInfo('nome', 'careproviderpersona', 'idutente='.$mieiCp[$i]);
		$cognomeCp = getInfo('cognome', 'careproviderpersona', 'idutente='.$mieiCp[$i]);
	
		if(strcmp($nomeCp,$nome)==0 AND strcmp($cognomeCp,$cognome)==0){
			$idCp = getInfo('id', 'careproviderpersona', 'idutente='.$mieiCp[$i]);
			$i=$n;
		}
	}
	if(is_null($idCp)) $idCp=-1;
	return $idCp;
}

function nuovaIndagineRichiesta($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, $stato, $tipo){
	global $database;
	$query = 'insert into indagini (idpaziente, idcpp, careprovider, idDiagnosi, motivo, stato, tipoIndagine, dataInserimento)
              values ('.$idPaziente.','.$careprovider.',"'.$careproviderNome.'",'.$idMotivo.',"'.$motivo.'","'.$stato.'","'.$tipo.'",CURRENT_TIMESTAMP)';
    executeQuery($query);
	 return $query;
}

function nuovaIndagineProgrammata($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, $stato, $tipo, $data, $centro){
    global $database;
    $query = 'insert into indagini (idpaziente, idcpp, careprovider, idDiagnosi, motivo, stato, tipoIndagine, dataInserimento, dataIndagine, idStudioIndagini)
              values ('.$idPaziente.','.$careprovider.',"'.$careproviderNome.'",'.$idMotivo.',"'.$motivo.'","'.$stato.'","'.$tipo.'",CURRENT_TIMESTAMP,\''.$data.'\','.$centro.')';
    executeQuery($query);
    return $query;
}

function nuovaIndagineCompletata($idPaziente, $careprovider, $careproviderNome, $idMotivo, $motivo, $stato, $tipo, $data, $centro, $referto, $allegato){
    global $database;
    $query = 'insert into indagini (idpaziente, idcpp, careprovider, idDiagnosi, motivo, stato, tipoIndagine, dataInserimento, dataIndagine, idStudioIndagini, referto, allegato)
              values ('.$idPaziente.','.$careprovider.',"'.$careproviderNome.'",'.$idMotivo.',"'.$motivo.'","'.$stato.'","'.$tipo.'",CURRENT_TIMESTAMP,\''.$data.'\','.$centro.',"'.$referto.'","'.$allegato.'")';
    executeQuery($query);
    return $query;
}

function modificaIndagineRichiesta($idIndagine, $careprovider, $careproviderNome, $idMotivo, $motivo, $stato, $tipo){
    global $database;
    $query = 'UPDATE indagini SET idcpp='.$careprovider.', careprovider="'.$careproviderNome.'",idDiagnosi='.$idMotivo.',motivo="'.$motivo.'",
    stato="'.$stato.'",tipoIndagine="'.$tipo.'" WHERE id='.$idIndagine;
    executeQuery($query);
    //$result = mysqli_query($query) or trigger_error(mysqli_error()." ".$query);
    return $query;
}

function modificaIndagineProgrammata($idIndagine, $careprovider, $careproviderNome, $idMotivo, $motivo, $stato, $tipo, $data, $centro){
    global $database;
    $query = 'update indagini set idcpp='.$careprovider.', careprovider="'.$careproviderNome.'",idDiagnosi='.$idMotivo.',motivo="'.$motivo.'",
    stato="'.$stato.'",tipoIndagine="'.$tipo.'",dataAggiornamento=CURRENT_TIMESTAMP,dataIndagine=\''.$data.'\',idStudioIndagini='.$centro.' where id='.$idIndagine;
    executeQuery($query);
    return $query;
}

function modificaIndagineCompletata($idIndagine, $careprovider, $careproviderNome, $idMotivo, $motivo, $stato, $tipo, $data, $centro, $referto, $allegato){
    global $database;
    $query = 'update indagini set idcpp='.$careprovider.', careprovider="'.$careproviderNome.'",idDiagnosi='.$idMotivo.',motivo="'.$motivo.'",
    stato="'.$stato.'",tipoIndagine="'.$tipo.'",dataAggiornamento=CURRENT_TIMESTAMP,dataIndagine=\''.$data.'\',idStudioIndagini='.$centro.',referto="'.$referto.'",allegato="'.$allegato.'" where id='.$idIndagine;
    executeQuery($query);
    return $query;
}

function modificaStatoIndagine($id,$stato){
    global $database;
    $q = 'update indagini set stato="'.$stato.'" where id='.$id;
    executeQuery($q);
}

function eliminaIndagine($id, $idutente){
    global $database;
    $query = 'insert into indaginiEliminate (idutente, indagine_id) values ('.$idutente.','.$id.')';
    executeQuery($query);
    return $query;
}





function nuovaDiagnosi($idPaziente,$patologia,$stato,$conf,$idCareProvider, $careProvider){
	global $database;

	$q = 'insert into diagnosi (idPaziente, dataIns, patologia, stato, conf) values ('.
			$idPaziente.', CURRENT_TIMESTAMP, "'.$patologia.'", '.$stato.','.$conf.')';
			
	
			
	executeQuery($q);
	
	$idDiagnosi = getMaxId('diagnosi');
	
	
	if($idCareProvider==-1){ // Non è stato possibile recuperare l'id del cp (non è iscritto o  non è associato al paziente)
		$q = 'insert into careProviderDiagnosi (diagnosi_id, statoDiagnosi, careprovider) values ('.
				$idDiagnosi.', '.$stato.', "'.$careProvider.'")';
	}else{
		$q = 'insert into careProviderDiagnosi (diagnosi_id, statoDiagnosi, careprovider, idcpp) values ('.
				$idDiagnosi.', '.$stato.', "'.$careProvider.'",'.$idCareProvider.')';
		
	}
	
	//$q = 'insert into diagnosi_innodb (idPaziente, idCareProviderP, nomePatologia, dataIns, stato, conf) values ('.
	//		$idPaziente.','.$idCareProviderP.',"'.$nomePatologia.'",CURRENT_TIMESTAMP,'.$stato.','.$conf.')';
	
	executeQuery($q);
	return $idDiagnosi;
	
}

function getStatoDiagnosi($id){
	global $database;
	return getInfo('stato','diagnosi','id='.$id);
}

function getConfDiagnosi($id){
	global $database;
	return getInfo('conf','diagnosi','id='.$id);
}


function modificaConfDiagnosi($id,$conf){
	global $database;
	$q = 'update diagnosi set conf='.$conf.' where id='.$id;
	executeQuery($q);
}

function modificaStatoDiagnosi($id,$stato){
	global $database;
	$q = 'update diagnosi set stato='.$stato.' where id='.$id;
	executeQuery($q);
}

function inserisciCareProviderDiagnosi($idDiagnosi, $stato, $careprovider, $id){
	global $database;
	if($id==-1) $id="NULL";
	$q = 'insert into careProviderDiagnosi (diagnosi_id, statoDiagnosi, careprovider, idcpp) values('.
			$idDiagnosi.', '.$stato.' ,"'.$careprovider.'", '.$id.')';
	executeQuery($q);
	return $q;
}

function getMaxId($table){
	global $database;
	return getInfo('max(id)', $table, '1');
}
function insertDiagnosis($id, 
                         $idcpp, 
                         $desease, 
                         $status, 
                         $conditions, 
                         $beginningDate, 
                         $confidentiality = null,
                         $healingDate = null, 
                         $notes = null){
	
	/**
          * Funzione che inserisce una diagnosi per un paziente
          * 
          * @param $id  (id paziente)
          * @param $idcpp  (id care provider che ha effettuato la visita)
          * @param $status  (stato della malattia)
          * @param $examinationDate  (data di visita)
          * @param $conditions  (condizioni)
          * @param $beginningDate  (data di principio)
          * @param $beginningDate  (data di guarigione)
          * @param $notes (eventuali note)
          * 
          * @return boolean
	*/
	
	global $database;
	
	$desease = getInfo('id', 'patologie', 'tipologia = "' . $desease . '"');
	
	// verifica che la diagnosi non sia già presente nel database
	
	$check = getInfo('id', 'diagnosi', 'idutente = ' . $id . ' and patologia = ' . $desease . ' and stato <> "guarita"');
	
	if ($check > 0) deleteInfo('diagnosi', 'id = ' . $check);
	if ($confidentiality == null) $confidentiality = 3;
	
          if ($healingDate != null){
		$values = 'idutente,idcpp,patologia,stato,datavisita,condizioni,confidenzialita,commenti,dataprincipio,dataguarigione';
		$q = 'INSERT INTO diagnosi(' . $values . ') VALUES (' . $id . ', ' 
		                                                      . $idcpp . ', ' 
		                                                      . $desease . ', "' 
		                                                      . $status . '", "' 
		                                                      . getTime('date') . '", "' 
		                                                      . $conditions . '", "' 
		                                                      . $confidentiality . '", "' 
		                                                      . $notes . '", "' 
		                                                      . $beginningDate . '", "' 
		                                                      . $healingDate . '")';
		}
			
          else{
		$values = 'idutente,idcpp,patologia,stato,datavisita,condizioni,confidenzialita,commenti,dataprincipio';
		$q = 'INSERT INTO diagnosi(' . $values . ') VALUES (' . $id . ', ' 
		                                                      . $idcpp . ', ' 
		                                                      . $desease . ', "' 
		                                                      . $status . '", "' 
		                                                      . getTime('date') . '", "' 
		                                                      . $conditions . '", "' 
		                                                      . $confidentiality . '", "' 
		                                                      . $notes . '", "' 
		                                                      . $beginningDate . '")';
		}
    	    
	
	return executeQuery($q);

}

function insertRelative($relationship, $id = null, $dataNascita = null){
	
	/**
	* Crea una relazione di parentela tra il proprio nodo e il paziente identificato da $id
	* 
	* @param $relationship
	* @param $id
	* @param $dataNascita
	*/ 
	
          global $database;
   
          // verifica che la relazione non sia mai stata creata
          $flag = getInfo('id', 'familiarita', 'idutente = ' . getMyID() . ' and idparente = ' . $id);
          if ($flag > 0) return false;
    
          $flag = getInfo('id', 'altrifamiliari', 'idutente = ' . getMyID() . ' and parentela = "' . $relationship . '"');
          if ($flag > 0 and $id == null) return false;
    
          if (!is_string($relationship) or (!is_null($id) and !is_numeric($id))) return false;
    
          $oppositeRelationship = getRelationship($relationship);
    
    
          if (kinship($id) > 0){
    	      // relazione N -> Familiare
    	      $q = 'INSERT INTO familiarita (idutente, idparente, parentela, aggiornamento) VALUES (' . getMyID() . ', ' . $id . ', "'  
    	      . $relationship . '", "' . getTime('time') . '")';
    	      executeQuery($q);
    	
    	      // relazione Familiare -> N
    	      $q = 'INSERT INTO familiarita (idutente, idparente, parentela, aggiornamento) VALUES (' . $id . ', ' . getMyID() . ', "' 
    	      . $oppositeRelationship . '", "' . getTime('time') . '")';
    	      executeQuery($q);

    	      // verifica che la relazione non fosse precedentemente stata inserita in 'altrifamiliari'
    	      $flagArray = getArray('id', 'altrifamiliari', 'parentela = "' . $relationship . '" and idutente = ' . getMyID());

    	      foreach($flagArray as $flag)
    		if ($flag > 0) {
    			deleteInfo('altrifamiliaripatologie', 'idfamiliari = ' . $flag);
				deleteInfo('altrifamiliari', 'id = ' . $flag);
	          }
			

    	      $flagArray = getArray('id', 'altrifamiliari', 'parentela = "' . $oppositeRelationship . '" and idutente = ' . $id);
    	
    	      foreach($flagArray as $flag)
    		if ($flag > 0) {
    			deleteInfo('altrifamiliaripatologie', 'idfamiliari = ' . $flag);
				deleteInfo('altrifamiliari', 'id = ' . $flag);
			}
    		

                }
          else{
    	
    	      // verifica che la relazione non sia mai stata creata
    	      $flag = getInfo('idutente', 'altrifamiliari', 'idutente = ' . getMyID() . ' and parentela = "' . $relationship . '"');
    	      if ($flag > 0) return false;
    	
    	      // relazione N -> Familiare
    	      $q = 'INSERT INTO altrifamiliari (idutente, parentela, datanascita, aggiornamento) VALUES (' . getMyID() . ', "' . $relationship . '", "' 
    	      . $dataNascita . '", "' . getTime('time') . '")';
    	      executeQuery($q);
    	
          } 
}


function breakRelationship($idRelative){
	
	/**
	* Rompe la relazione di parentela con un paziente FSEM, rendendo la relazione anonima
	* Si invia un messaggio di notifica al paziente
	* 
	* @param $idRelative
	* 
	* @return boolean
	*/ 
	
	global $database;
	
	$id = getInfo('id', 'familiarita', 'idutente = ' . getMyID() . ' and idparente = ' . $idRelative);
	if (!$id) return false;
	
	// Rompo la relazione Paziente -> Parente
	$relationship = getInfo('parentela', 'familiarita', 'id = ' . $id);
	$dataNascita = decryptData(deserializeData(getInfo('datanascita', 'pazienti', 'idutente = ' . $idRelative)));
			
	// cancello la relazione dalla tabella Familiarita
	deleteInfo('familiarita', 'id = ' . $id);		
			
	// sposto la relazione nella tabella altrifamiliari estraendo solo la data di nascita del parente
	$q = 'INSERT INTO altrifamiliari (idutente, parentela, datanascita, aggiornamento) 
		  VALUES (' . getMyID() . ', "' . $relationship . '", "' . $dataNascita . '", "' . getTime('time') . '")';
    		
          executeQuery($q);
    
    
          $id = getInfo('id', 'familiarita', 'idutente = ' . $idRelative . ' and idparente = ' . getMyID());
	if (!$id) return false;
	
	// Rompo la relazione Parente -> Paziente
	$relationship = getInfo('parentela', 'familiarita', 'id = ' . $id);
	$dataNascita = decryptData(deserializeData(getInfo('datanascita', 'pazienti', 'idutente = ' . getMyID())));
	
	// cancello la relazione dalla tabella Familiarita
	deleteInfo('familiarita', 'id = ' . $id);	
			
	// sposto la relazione nella tabella altrifamiliari estraendo solo la data di nascita del parente
	$q = 'INSERT INTO altrifamiliari (idutente, parentela, datanascita, aggiornamento) 
		  VALUES (' . $id . ', "' . $relationship . '", "' . $dataNascita . '", "' . getTime('time') . '")';		
          executeQuery($q);
    
          createMessage(getMyID(), $idRelative, 'Relazione di parentela interrotta', 'Relazione di parentela interrotta');
    
          return true;

}


function createMessage($owner, $subject, $title, $content){
	
	/**
	* Funzione che crea un messaggio
	* 
	* @param $owner
	* @param $subject
	* @param $content
	* 
	*/ 
	
	$mex = new Data($content, 
	                getCredentials($owner), 
	                $subject, 
	                null, 
	                $title);
	                
          $mex = serializeData(encryptData($mex));
          sendMessage($mex);  
    
}


function startPBAC($subject, $action) {
	
	
          /**
          * Determina il tipo di accesso sulla risorsa
	* E' la funzione che modella il framework di sicurezza sull'accesso alle risorse
	* 
	* @param $subject
	* @param $action
	* 
	* @return Int 
	*/
	
	
	$PEP = new PolicyEnforcementPoint();
          $PDP = new PolicyDecisionPoint();

          $PEP -> setPDP($PDP);
    
          $resource = loadPolicies(getMyID());

          if ($resource == false) return 'Indeterminate';
	
	return $PEP -> callPDP($subject, $resource, $action);
	
  
}


function savePolicy($name, $policy, $subject, $operation = null, $owner = null){
	   
	/**
	* Salva una policy nella Table data  
	* ogni policy ha l'id di chi l'ha stipulata, il soggetto coinvolto, la data in cui è stata creata 
	* contenuto: contiene la policy
	* tipologia: indica il dato di tipo 'policy' 
	* operation: indica se la policy è nuova o è stata aggiornata
	* owner: indica il proprietario della policy
	* 
	* @param $name
	* @param $policy
	* @param $subject 
	* @param $operation
	* @param $owner
	* 
	* @return Boolean
	*/ 
	    
	if ($operation == 'Create') 
		$status = 'new';
	else
		$status = 'updated';
		
	if ($owner == null) 
		$owner = getMyID();	
		    
          $str = serialize($policy);
          $strenc = urlencode($str);
    
          global $database;
    
          $q = 'INSERT INTO data(nome, idproprietario, idsoggetto, contenuto, data, tipologia, stato) VALUES("' . $name . 
                   '", '. $owner . ', ' . $subject . ', "'. $strenc . '","' . getTime('date') . '", "policy", "' . $status . '")';
          
          return executeQuery($q);
}


function fillPatientSummary($idPaziente, $name, $surname, 
                                                 $born, $address, $country, 
                                                 $actualCountry, $sex, $bloodType, $telephone){
	
	/**
	* Cripta i dati principali di un paziente
	* 
	* @param $idPaziente
	* @param $name
	* @param $surname 
	* @param $born
	* @param $address
	* @param $country
	* @param $actualCountry
	* @param $sex
	* @param $bloodType
	* @param $telephone
	* 
	* @return Boolean
	*/ 
	
	
		
	//	echo "dati in fill" .$name .$surname .$born .$sex  .$country  .$actualCountry  .$bloodType .$address .$telephone ." ";
	
	  $name = 		serializeData(encryptData($name));
	  $surname = 	serializeData(encryptData($surname));
	  $born = 		serializeData(encryptData($born));
	  $sex = 		serializeData(encryptData($sex));
	  $country = 	serializeData(encryptData($country));
	  $actualCountry = serializeData(encryptData($actualCountry));
	  $bloodType = 	serializeData(encryptData($bloodType));
	  $address = 	serializeData(encryptData($address));
	  $telephone = 	serializeData(encryptData($telephone));
    
	
	$q = 'INSERT INTO pazienti (idutente, nome, cognome,' . 
	                            ' datanascita, indirizzo,' .
	                            ' comunenascita, comuneresidenza,' .
	                            ' sesso, grupposanguigno, telefono, donatoreorgani)' .
		 'VALUES (' . $idPaziente . ', "'  . $name . '", "' . $surname . '", "' . $born . 
		          '", "'. $address . '", "' . $country . '", "' . $actualCountry . '", "' . 
		          $sex . '", "' . $bloodType . '", "' . $telephone . '", "0")';
			          
	return (executeQuery($q));
	
}

//funzione per aggiornare i dati anaggrafici del paziente
function updatePatientAnag( $idPaziente, $name, $surname, $born, $address,
							$country, $actualCountry, $sex, $telephone, $email, $cf)
		{
	
		$name = serializeData(encryptData($name));
        $surname = serializeData(encryptData($surname));
        $born = serializeData(encryptData($born));
        $sex = serializeData(encryptData($sex));
        $country = serializeData(encryptData($country));
        $actualCountry = serializeData(encryptData($actualCountry));
		$address = serializeData(encryptData($address));
        $telephone = serializeData(encryptData($telephone));
		$email = serializeData(encryptData($email));
		$cf = serializeData(encryptData($cf));
   
	updateInfo('sesso = "'. $sex.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('nome = "'. $name.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('cognome = "'. $surname.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('datanascita = "'. $born.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('comunenascita = "'. $country.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('comuneresidenza = "'. $actualCountry.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('indirizzo = "'. $address.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('telefono = "'. $telephone.'"' ,'pazienti','idutente ='. $idPaziente);
	updateInfo('email = "'. $email.'"' ,'utenti','id ='. $idPaziente);
	updateInfo('codicefiscale = "'. $cf.'"' ,'utenti','id ='. $idPaziente);
	
}
//inserisce nella tabella files le informazione relative ai files uploadati
function insertFilesData( $idPaziente, $idProprietario,$nomeFile,$dataCreazione, $path, $extension, $codConf, $commento){
	
	$q = 'INSERT INTO files ( idPaziente, idProprietario, dataCreazione,' . 
	                            ' nomeFile, path,' .
	                            ' extension, codConfidenzialita, commento)' .
		 'VALUES (' . $idPaziente . ', "'  . $idProprietario . '", "' . $dataCreazione  . '", "' . $nomeFile . 
		          '", "'. $path . '", "' . $extension . '", "' . $codConf . '" , "' . $commento . '")';
			          
	
	return (executeQuery($q));
	
}



function serializeData($data){
	
	/**
	* Serializza un oggetto
	* 
	* @return Serialized object
	*/ 
	
          return urlencode(serialize($data));
	
}


function deserializeData($data){
	
	/**
	* Deserializza un oggetto
	* 
	* @return Deerialized object
	*/ 
	
          return unserialize(urldecode($data));
	
}

function getRelationship($grade){
	
	/**
          * Funzione che restituisce la relazione che intercorre tra due pazienti in relazione di parentela
          * 
          * @param $grade
          * 
          * @return String
	*/


	$sesso = decryptData(deserializeData(getInfo('sesso', 'pazienti', 'idutente = ' . getMyID())));
	
	if ($grade == 'Madre' or $grade == 'Padre')
		if ($sesso == 'M') return 'figlio'; else return 'Figlia';
		
	if ($grade == 'Figlio' or $grade == 'Figlia')
			if ($sesso == 'M') return 'Padre'; else return 'Madre';
			
          if ($grade == 'Fratello' or $grade == 'Sorella')
		if ($sesso == 'M') return 'Fratello'; else return 'Sorella';
			
	if ($grade == 'Nonno' or $grade == 'Nonna') return 'Nipote';
	
	// nipote crea ambiguità. Se sono nipote, posso essere in relazione con ZIO e NONNO.
	// Fare in modo che quando si dichiara di essere "Nipote di", si specifichi se si è Zio/a o Nonno/a
	
	
	if ($grade == 'Cugino' or $grade == 'Cugina')
		if ($sesso == 'M') return 'Cugino'; else return 'Cugina';
			
}


function getResponse($data, $action){
	
	/**
          * Funzione che dà il consenso o meno all'apertura di un documento tramite l'azione specificata in input
          * 
          * @param $data 
          * @param $action
          * 
          * @return String
	*/
	
	$data = new Data(null, $data, getMyID());                                                
	$action = new Action($action);
	$newData = new Data(null, $data -> getName(), $data -> getSubject());

	
          $subject = createSubject(getMyID(), $newData);
          $result = startPBAC($subject, $action);
	
	if ($result == 1) return 'Permit';
	if ($result == 0) return 'Deny';
	if ($result == 2) return 'NotApplicable';
	if ($result == -1) return 'Indeterminate'; 

}
/*se il parametro passato come argomento è il ruolo di un care
provider restituisce true altrimenti false*/
function isCareProvider($role){	
	$isCpp = getInfo('tipologia', 'ruoli', 'codice = "'.$role.'"');
	
	if($isCpp == "cpp")
		return true;
	else 
		return false;
	
}


?>
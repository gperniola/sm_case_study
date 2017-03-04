<?php

//funzioni accessorie per uploadFiles nella directory Files
// e per template_page_files nella directry template
//function caricaDirectory ($dir , $idPz = 0, $fileClass = 0,  $idProp = 0, $date = 0, $conf = 0 ){
/* restituisce un array contenente i files contenuti nella directory $dir il cui formato corrisponda a $fileClass
	*/


function caricaDirectory ($dir , $fileClass = 0, $idPz = 0, $idProp = 0, $date = 0, $conf = 0 ){
/* restituisce un array contenente i files contenuti nella directory $dir il cui formato corrisponda a $fileClass
	*/
	$dh = opendir($dir)
		or die ( "errore nell'apertura della directory". $dir);
	$contenuto = array();
	//readdir legge un singolo elemento della directory, restitutisce FALSE, quando gli elementi sono terminati
	while (($file = readdir($dh)) !== FALSE )
	{
		if ( !is_dir ($file) && controllaFormato ( $file,$fileClass ))
		{
			$idpaziente= getInfo("idPaziente","files","nomeFile=".$file);
			if($idpaziente==$idpz)
				$contenuto[] = $file;
		}
	}
	closedir ($dh);
	return $contenuto;
}

function controllaFormato ( $nomeFile,$fileClass ){
	// utilizzo gli array definiti in configFiles.php per verificare che il file sia del formato richiesto con $fileclass
	global $formato_class1;
	global $formato_class2;
	global $formato_class3;
	global $formato_class4;
	global $formato_class5;
	global $formato_class6;
	global $formato_class7; // referti indagini diagnostiche
	
	switch ( $fileClass)
	{
		case 0 :
			return TRUE;
		
		case 1 :
			foreach ( $formato_class1 as $formato)
			{
				if (strrpos($nomeFile, $formato))
					return TRUE;
			}	
				return FALSE;
			
		case 2 :
			foreach ( $formato_class2 as $formato)
			{
				if (strrpos($nomeFile, $formato))
					return TRUE;
			}	 
				return FALSE;
				
		case 3 :
			foreach ( $formato_class3 as $formato)
			{
				if (strrpos($nomeFile, $formato))
					return TRUE;
			}	 
				return FALSE;
			
		case 4 :
			foreach ( $formato_class4 as $formato)
			{
				if (strrpos($nomeFile, $formato))
					// sono esclusi i file con estensione .exe e .bat
					return FALSE;
			}	 
				return TRUE;
			
		case 5 :
			foreach ( $formato_class5 as $formato)
			{
				if (strrpos($nomeFile, $formato))
					return TRUE;
			}	 
				return FALSE;
			
		case 6 :
			foreach ( $formato_class6 as $formato)
			{
				if (strrpos($nomeFile, $formato))
					return TRUE;
			}	 
				return FALSE;
        /**
         * Added 04/03/17 per modulo indagini: controllo file per referti indagini
         */
        case 7 :
            foreach ( $formato_class7 as $formato)
            {
                if (strrpos($nomeFile, $formato))
                    return TRUE;
            }
            return FALSE;
	}
	
}//fine di controllaFormato

function controllaTipo ( $nomeFile){
	global $tipo_immagine;
	foreach ( $tipo_immagine as $tipo)
		if (strrpos($nomeFile, $tipo))
			return TRUE;
		return FALSE;
}

function generaLinkImmagine( $indice_immagine , $file){
	//?immagine=$indice_immagine è passato come Get a visualizza.php
	return "<a href = \"visualizza.php?immagine="
	. $indice_immagine . "\" > "
	. "<img src=\"" . DIR_IMMAGINI . "/"
	. $file . "\"  width = \"80\" height =  \" 60\"/>"
	. "</a>";
}

//function generaLinkTestuale( $indice_immagine , $testo){
	function generaLink( $indice_immagine , $testo){
	 return "<a href = \"visualizza.php?immagine="
	. $indice_immagine . "\" > "
	. $testo
	. "</a>";
}

function file_extension($filename) {
  $ext = explode(".", $filename);
  return $ext[count($ext)-1];  
}
?>
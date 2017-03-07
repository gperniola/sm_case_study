<?php
	//define ( "DIR_FOTO" ,"/uploads/foto");
	define ( "DIR_FOTO" ,$_SERVER['DOCUMENT_ROOT'].'assets/img/');
	define ( 'DIR_REGISTRAZIONI' , "/uploads/registrazioni");
	define ( 'DIR_VIDEOPZ' , "/uploads/videoPz");
	define ( 'DIR_DICOM' , "/uploads/dicom");
	define ( 'DIR_SCANSIONI' , "/uploads/scansioni");
	define ( 'DIR_VIDEOSTRUM' , "/uploads/videoStrum");
	//$immagini_per_riga = 5;
	
	/*class1 foto del pz; class2 video del pz; class3 registrazioni; class4 dicom;
	class5 video diagnostici; class6 scansioni referti e lettere di dimissione*/
	$formato_class1 = array ( "jpeg", "JPG", "gif" ,"png", "PNG", "jpg");
	$formato_class2 = array ( ".3gp", ".DivX", ".MPEG", ".MOV", ".wlmp", ".wmv");
	$formato_class3 = array ( ".mp3", ".wav", ".ogg", ".m4a",);
	$formato_class4 = array ( ".exe",".bat" );//da verificare non avendo trovato un'estensione per dicom si escludono le estensione .exe e .bat
	$formato_class5 = array (".3gp", ".DivX", ".MPEG", ".MOV", ".wlmp",".wmv" );
	$formato_class6 = array ( ".pdf", ".doc", ".docx" ,".txt", ".odt");
/**
 * Added 04/03/17 per modulo indagini: class7 per referti indagini
 */
    $formato_class7 = array ( ".pdf", ".doc", ".docx" ,".txt", ".odt");
	
	$tipo_immagine = array ("jpeg","gif", "png" , "JPG" , ".jpg" );
?>
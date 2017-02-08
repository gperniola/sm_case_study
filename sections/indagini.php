<?php

//$pag_indagini->set_var('','');
//$var = $_POST['a'];
//$pag_indagini->set_var('ciao', $var);
//$pag_indagini->out('template_page_indagini');

if ( isset ($_GET["cp_Id"]))
{
    $cp_id = $_GET["cp_Id"]; //inizializzo $cp_id col valore passato con GET
    $myRole = getRole($cp_id);
}
else
    $myRole = getRole(getMyID());

if ( isset ($_GET["pz_Id"]))
    $pz_id = $_GET["pz_Id"]; //inizializzo $pz_id col valore passato con GET
else
    $pz_id  = getMyID();


$pag_indagini= new templatemanager('templates/');

// Salvo l'id utente
$pag_indagini -> set_var('idUtentePaz', $pz_id);

// Partendo dall'id utente ($pz_id) ottengo il relativo id paziente
$idPaziente = getInfo('id', 'pazienti', 'idutente = '.$pz_id);
$pag_indagini -> set_var('idPaz',$idPaziente);

// Faccio la stessa cosa per il cp, ottentendo il suo id careproviderpersona
$idCpp = getInfo('id','careproviderpersona','idutente='.$cp_id);
$pag_indagini -> set_var('idUtenteCp', $idCpp);

//Estraggo il livello di confidenzialita' tra careprovider e paziente
$confidenzialita = getInfo('confidenzialita', 'careproviderpaziente', 'idutente='.$pz_id.' AND idcpp='.$cp_id);
$pag_indagini -> set_var('confidenzialita', $confidenzialita);

//Inoltre, estraggo il mio nome e cognome (mi serve per le operazioni di inserimento/modifica diagnosi)
$cpNome = getInfo('nome', 'careproviderpersona', 'idutente='.$cp_id);
$cpCognome = getInfo('cognome', 'careproviderpersona', 'idutente='.$cp_id);
$pag_indagini -> set_var('mioCpNome', $cpNome);
$pag_indagini -> set_var('mioCpCognome', $cpCognome);


/******************************************************************************
 * ESTRAGGO LE INDAGINI E LE INFORMAZIONI COLLEGATE:
 * - diagnosi collegata all'indagine
 * - careprovider che ha richiesto l'indagine
 * - centro in cui si effettua l'indagine
 ******************************************************************************/
$indaginiId = getArray('id', 'indagini', 'idPaziente='.$idPaziente);
$indaginiTipo = getArray('tipoIndagine', 'indagini', 'idPaziente='.$idPaziente);
$indaginiData = getArray('DATE(data)', 'indagini', 'idPaziente='.$idPaziente);
$indaginiReferto = getArray('referto', 'indagini', 'idPaziente='.$idPaziente);
$indaginiAllegato = getArray('allegato', 'indagini', 'idPaziente='.$idPaziente);
$indaginiCp = getArray('idcpp', 'indagini', 'idPaziente='.$idPaziente);
$indaginiCentro = getArray('idStudioIndagini', 'indagini', 'idPaziente='.$idPaziente);
$indaginiMotivo = getArray('motivo', 'indagini', 'idPaziente='.$idPaziente);
$indaginiStato = getArray('stato', 'indagini', 'idPaziente='.$idPaziente);
$indaginiIdDiagnosi = getArray('idDiagnosi', 'indagini','idPaziente='.$idPaziente);
$indaginiCareprovider = getArray('careprovider', 'indagini','idPaziente='.$idPaziente);
$n = count($indaginiId);
for($i=0; $i<$n; $i++){
    $pag_indagini -> set_var('ind.id.'.$i, $indaginiId[$i]);
    $pag_indagini -> set_var('ind.tipo.'.$i, $indaginiTipo[$i]);
    $pag_indagini -> set_var('ind.data.'.$i, italianFormat($indaginiData[$i]));
    $pag_indagini -> set_var('ind.referto.'.$i, $indaginiReferto[$i]);
    $pag_indagini -> set_var('ind.allegato.'.$i, $indaginiAllegato[$i]);
    $pag_indagini -> set_var('ind.cpId.'.$i, $indaginiCp[$i]);
    $pag_indagini -> set_var('ind.careprovider.'.$i, $indaginiCareprovider[$i]);
    $careproviderNome = getInfo('nome', 'careproviderpersona', 'idutente='.$indaginiCp[$i]);
    $careproviderCognome = getInfo('cognome', 'careproviderpersona', 'idutente='.$indaginiCp[$i]);
    $careproviderRep = getInfo('reperibilita', 'careproviderpersona', 'idutente='.$indaginiCp[$i]);
    $pag_indagini -> set_var('ind.cpNome.'.$i, $careproviderNome);
    $pag_indagini -> set_var('ind.cpCognome.'.$i, $careproviderCognome);
    $pag_indagini -> set_var('ind.cpRep.'.$i, $careproviderRep);
    $pag_indagini -> set_var('ind.centroId.'.$i, $indaginiCentro[$i]);
    $centroNome = getInfo('nomeStudio', 'centriindagini', 'id='.$indaginiCentro[$i]);
    $centroVia = getInfo('via', 'centriindagini', 'id='.$indaginiCentro[$i]);
    $centroCitta = getInfo('citta', 'centriindagini', 'id='.$indaginiCentro[$i]);
    $pag_indagini -> set_var('ind.centroNome.'.$i, $centroNome);
    $pag_indagini -> set_var('ind.centroVia.'.$i, $centroVia);
    $pag_indagini -> set_var('ind.centroCitta.'.$i, $centroCitta);
    $pag_indagini -> set_var('ind.motivo.'.$i, $indaginiMotivo[$i]);
    $pag_indagini -> set_var('ind.stato.'.$i, $indaginiStato[$i]);
    $pag_indagini -> set_var('ind.idDiagno.'.$i, $indaginiIdDiagnosi[$i]);
    $conf = getInfo('conf', 'diagnosi', 'id='.$indaginiIdDiagnosi[$i]);
    $pag_indagini -> set_var('ind.conf.'.$i, $conf);
}
$pag_indagini -> set_var('indaginiNum', $n);


/******************************************************************************
 * ESTRAGGO I CAREPROVIDER COLLEGATI ALL'UTENTE
 ******************************************************************************/
$cpRegistratiId = getArray('idcpp', 'careproviderpaziente', 'idutente='.$idPaziente);
$v = count($cpRegistratiId);
for($i=0; $i<$v; $i++){
    $cpRegistratoNome = getInfo('nome', 'careproviderpersona', 'idutente='.$cpRegistratiId[$i]);
    $cpRegistratoCognome = getInfo('cognome', 'careproviderpersona', 'idutente='.$cpRegistratiId[$i]);
    $pag_indagini -> set_var('careprovider.id.'.$i, $cpRegistratiId[$i]);
    $pag_indagini -> set_var('careprovider.nome.'.$i, $cpRegistratoNome);
    $pag_indagini -> set_var('careprovider.cognome.'.$i, $cpRegistratoCognome);
}
$pag_indagini -> set_var('careproviderNum', $v);


/******************************************************************************
 * ESTRAGGO I CENTRI DIAGNOSTICI
 ******************************************************************************/
$centriId = getArrayNoCondition('id', 'centriindagini');
$centriNome = getArrayNoCondition('nomeStudio', 'centriindagini');
$centriVia = getArrayNoCondition('via', 'centriindagini');
$centriCitta = getArrayNoCondition('citta', 'centriindagini');
$centriTipo = getArrayNoCondition('tipoCentro', 'centriindagini');
$centriEmail = getArrayNoCondition('mail', 'centriindagini');
$centriIdCpp = getArrayNoCondition('idcpp', 'centriindagini');
$m = count($centriId);
for($i=0; $i<$m; $i++){
    $pag_indagini -> set_var('centro.id.'.$i, $centriId[$i]);
    $pag_indagini -> set_var('centro.nome.'.$i, $centriNome[$i]);
    $pag_indagini -> set_var('centro.via.'.$i, $centriVia[$i]);
    $pag_indagini -> set_var('centro.citta.'.$i, $centriCitta[$i]);
    $pag_indagini -> set_var('centro.tipo.'.$i, $centriTipo[$i]);
    $pag_indagini -> set_var('centro.mail.'.$i, $indaginiAllegato[$i]);
    $pag_indagini -> set_var('centro.responsabileId.'.$i, $centriIdCpp[$i]);
    $responsabileNome = getInfo('nome', 'careproviderpersona', 'idutente='.$centriIdCpp[$i]);
    $responsabileCognome = getInfo('cognome', 'careproviderpersona', 'idutente='.$centriIdCpp[$i]);
    $pag_indagini -> set_var('centro.responsabileNome.'.$i, $responsabileNome);
    $pag_indagini -> set_var('centro.responsabileCognome.'.$i, $responsabileCognome);
}
$pag_indagini -> set_var('centriNum', $m);


/******************************************************************************
 * ESTRAGGO LE DIAGNOSI COLLEGATE AL PAZIENTE
 ******************************************************************************/
$diagnosiId = getArray('id', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente);
$diagnosiData = getArray('dataIns', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente);
$diagnosiPatologia = getArray('patologia', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente);
$diagnosiConf = getArray('conf', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente);
$z = count($diagnosiId);
for($i=0; $i<$z; $i++){
    $pag_indagini -> set_var('diagnosi.id.'.$i, $diagnosiId[$i]);
    $pag_indagini -> set_var('diagnosi.data.'.$i,  italianFormat($diagnosiData[$i]));
    $pag_indagini -> set_var('diagnosi.patologia.'.$i, $diagnosiPatologia[$i]);
    $pag_indagini -> set_var('diagnosi.conf.'.$i, $diagnosiConf[$i]);
}
$pag_indagini -> set_var('diagnosiNum', $z);



$pag_indagini->out('template_page_indagini');
?>
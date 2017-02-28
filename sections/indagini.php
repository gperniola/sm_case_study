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
$cpNome = getInfo('nome', 'careproviderpersona', 'id='.$idCpp);
$cpCognome = getInfo('cognome', 'careproviderpersona', 'id='.$idCpp);
$pag_indagini -> set_var('mioCpNome', $cpNome);
$pag_indagini -> set_var('mioCpCognome', $cpCognome);


/******************************************************************************
 * ESTRAGGO LE INDAGINI E LE INFORMAZIONI COLLEGATE:
 * - diagnosi collegata all'indagine
 * - careprovider che ha richiesto l'indagine
 * - centro in cui si effettua l'indagine
 ******************************************************************************/
$indaginiId = getArray('id', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiTipo = getArray('tipoIndagine', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiData = getArray('dataIndagine', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiReferto = getArray('referto', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiAllegato = getArray('allegato', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiCp = getArray('idcpp', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiCentro = getArray('idStudioIndagini', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiMotivo = getArray('motivo', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiStato = getArray('stato', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiIdDiagnosi = getArray('idDiagnosi', 'indagini','idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiCareprovider = getArray('careprovider', 'indagini','idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$n = count($indaginiId);
for($i=0; $i<$n; $i++){
    $pag_indagini -> set_var('ind.id.'.$i, $indaginiId[$i]);
    $pag_indagini -> set_var('ind.tipo.'.$i, $indaginiTipo[$i]);
    if ($indaginiData[$i] != NULL)
        //$newDate = date("d/m/Y H:i", strtotime($indaginiData[$i]));
        $newDate = (new DateTime($indaginiData[$i]))->format('d/m/Y H:i');
    else
        $newDate = "";
    $pag_indagini -> set_var('ind.data.'.$i, $newDate);
    $pag_indagini -> set_var('ind.referto.'.$i, $indaginiReferto[$i]);
    $pag_indagini -> set_var('ind.allegato.'.$i, $indaginiAllegato[$i]);
    $pag_indagini -> set_var('ind.cpId.'.$i, $indaginiCp[$i]);
    $pag_indagini -> set_var('ind.careprovider.'.$i, $indaginiCareprovider[$i]);
    $careproviderNome = getInfo('nome', 'careproviderpersona', 'id='.$indaginiCp[$i]);
    $careproviderCognome = getInfo('cognome', 'careproviderpersona', 'id='.$indaginiCp[$i]);
    $careproviderRep = getInfo('reperibilita', 'careproviderpersona', 'id='.$indaginiCp[$i]);
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
$cpRegistratiIdUtente = getArray('idcpp', 'careproviderpaziente', 'idutente='.$idPaziente);
$v = count($cpRegistratiIdUtente);
for($i=0; $i<$v; $i++){
    $cpRegistratoId = getInfo('id','careproviderpersona','idutente='.$cpRegistratiIdUtente[$i]);
    $cpRegistratoNome = getInfo('nome', 'careproviderpersona', 'id='.$cpRegistratoId);
    $cpRegistratoCognome = getInfo('cognome', 'careproviderpersona', 'id='.$cpRegistratoId);
    $pag_indagini -> set_var('careprovider.id.'.$i, $cpRegistratoId);
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
    $pag_indagini -> set_var('centro.mail.'.$i, $centriEmail[$i]);
    $pag_indagini -> set_var('centro.responsabileId.'.$i, $centriIdCpp[$i]);
    $responsabileNome = getInfo('nome', 'careproviderpersona', 'id='.$centriIdCpp[$i]);
    $responsabileCognome = getInfo('cognome', 'careproviderpersona', 'id='.$centriIdCpp[$i]);
    $pag_indagini -> set_var('centro.responsabileNome.'.$i, $responsabileNome);
    $pag_indagini -> set_var('centro.responsabileCognome.'.$i, $responsabileCognome);
}
$pag_indagini -> set_var('centriNum', $m);


/******************************************************************************
 * ESTRAGGO LE DIAGNOSI COLLEGATE AL PAZIENTE
 ******************************************************************************/
$diagnosiId = getArray('id', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente . ' ORDER BY dataIns DESC');
$diagnosiData = getArray('dataIns', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente . ' ORDER BY dataIns DESC');
$diagnosiPatologia = getArray('patologia', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente . ' ORDER BY dataIns DESC');
$diagnosiConf = getArray('conf', 'diagnosi','stato < 3 AND idPaziente='.$idPaziente . ' ORDER BY dataIns DESC');
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
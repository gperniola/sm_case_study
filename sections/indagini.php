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
$indaginiRefertoId = getArray('referto', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
$indaginiAllegatoId = getArray('allegato', 'indagini', 'idPaziente='.$idPaziente . ' ORDER BY dataIndagine ASC');
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
        $newDate = (new DateTime($indaginiData[$i]))->format('d/m/Y<\b\r>H:i');
    else
        $newDate = "";
    $pag_indagini -> set_var('ind.data.'.$i, $newDate);

    $refertoNome = getInfo('nomeFile', 'files', 'idFiles='.$indaginiRefertoId[$i]);
    $refertoData = getInfo('dataCreazione', 'files', 'idFiles='.$indaginiRefertoId[$i]);
    $refertoPath = getInfo('path', 'files', 'idFiles='.$indaginiRefertoId[$i]);
    $refertoConf = getInfo('codConfidenzialita', 'files', 'idFiles='.$indaginiRefertoId[$i]);
    $pag_indagini -> set_var('ind.refertoId.'.$i, $indaginiRefertoId[$i]);
    $pag_indagini -> set_var('ind.refertoNome.'.$i, $refertoNome);
    $pag_indagini -> set_var('ind.refertoData.'.$i, $refertoData);
    $pag_indagini -> set_var('ind.refertoPath.'.$i, $refertoPath);
    $pag_indagini -> set_var('ind.refertoConc.'.$i, $refertoConf);

    $allegatoNome = getInfo('nomeFile', 'files', 'idFiles='.$indaginiAllegatoId[$i]);
    $allegatoData = getInfo('dataCreazione', 'files', 'idFiles='.$indaginiAllegatoId[$i]);
    $allegatoPath = getInfo('path', 'files', 'idFiles='.$indaginiAllegatoId[$i]);
    $allegatoConf = getInfo('codConfidenzialita', 'files', 'idFiles='.$indaginiAllegatoId[$i]);
    $pag_indagini -> set_var('ind.allegatoId.'.$i, $indaginiAllegatoId[$i]);
    $pag_indagini -> set_var('ind.allegatoNome.'.$i, $allegatoNome);
    $pag_indagini -> set_var('ind.allegatoData.'.$i, $allegatoData);
    $pag_indagini -> set_var('ind.allegatoPath.'.$i, $allegatoPath);
    $pag_indagini -> set_var('ind.allegatoConc.'.$i, $allegatoConf);

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
$centriId = getArrayNoCondition('id', 'centriindagini ORDER BY id');
$centriIdCpp = getArrayNoCondition('idcpp', 'centriindagini ORDER BY id');
$centriNome = getArrayNoCondition('nomeStudio', 'centriindagini ORDER BY id');
$centriVia = getArrayNoCondition('via', 'centriindagini ORDER BY id');
$centriCitta = getArrayNoCondition('citta', 'centriindagini ORDER BY id');
$centriTipo = getArrayNoCondition('tipoCentro', 'centriindagini ORDER BY id');
$centriEmail = getArrayNoCondition('mail', 'centriindagini ORDER BY id');

$m = count($centriId);
for($i=0; $i<$m; $i++){
    $pag_indagini -> set_var('centro.id.'.$i, $centriId[$i]);
    $pag_indagini -> set_var('centro.nome.'.$i, $centriNome[$i]);
    $pag_indagini -> set_var('centro.via.'.$i, $centriVia[$i]);
    $pag_indagini -> set_var('centro.citta.'.$i, $centriCitta[$i]);
    if ($centriTipo[$i] == "Studio specialistico")
        $pag_indagini -> set_var('centro.tipo.'.$i, 0);
    else if ($centriTipo[$i] == "Studio radiologico")
        $pag_indagini -> set_var('centro.tipo.'.$i, 1);
    else if ($centriTipo[$i] == "Laboratorio analisi")
        $pag_indagini -> set_var('centro.tipo.'.$i, 2);
    else
        $pag_indagini -> set_var('centro.tipo.'.$i, 3);
    $pag_indagini -> set_var('centro.mail.'.$i, $centriEmail[$i]);
    $pag_indagini -> set_var('centro.responsabileId.'.$i, $centriIdCpp[$i]);
    $responsabileNome = getInfo('nome', 'careproviderpersona', 'id='.$centriIdCpp[$i]);
    $responsabileCognome = getInfo('cognome', 'careproviderpersona', 'id='.$centriIdCpp[$i]);
    $pag_indagini -> set_var('centro.responsabileNome.'.$i, $responsabileNome);
    $pag_indagini -> set_var('centro.responsabileCognome.'.$i, $responsabileCognome);
    $contattiTel = getArray('telefono', 'telefonocentriindagini', 'idCentroIndagini=' .$centriId[$i]);
    $numeriTelefono = "";
    foreach($contattiTel as $tel)
        $numeriTelefono = '<a href="tel:'.$tel.'"><i class="glyphicon glyphicon-earphone" ></i> '. $tel . '</a><br>' . $numeriTelefono;

    $pag_indagini -> set_var('centro.contatti.'.$i, $numeriTelefono);
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
    $newDate = (new DateTime($diagnosiData[$i]))->format('d/m/y');
    $pag_indagini -> set_var('diagnosi.data.'.$i,  $newDate);
    $pag_indagini -> set_var('diagnosi.patologia.'.$i, $diagnosiPatologia[$i]);
    $pag_indagini -> set_var('diagnosi.conf.'.$i, $diagnosiConf[$i]);
}
$pag_indagini -> set_var('diagnosiNum', $z);


/******************************************************************************
 * ESTRAGGO FILE E REFERTI COLLEGATI AL PAZIENTE
 ******************************************************************************/
$filesId= getArray('idFiles', 'files','idPaziente='.$pz_id . ' ORDER BY dataCreazione DESC');
$filesData = getArray('dataCreazione', 'files','idPaziente='.$pz_id . ' ORDER BY dataCreazione DESC');
$filesConf = getArray('codConfidenzialita', 'files','idPaziente='.$pz_id . ' ORDER BY dataCreazione DESC');
$filesNome = getArray('nomeFile', 'files','idPaziente='.$pz_id . ' ORDER BY dataCreazione DESC');
$filesPath = getArray('path', 'files','idPaziente='.$pz_id . ' ORDER BY dataCreazione DESC');
$f = count($filesId);

for($i=0; $i<$f; $i++){
    $pag_indagini -> set_var('files.id.'.$i, $filesId[$i]);
    $newDate = (new DateTime($filesData[$i]))->format('d/m/y');
    $pag_indagini -> set_var('files.data.'.$i,  $newDate);
    $pag_indagini -> set_var('files.conf.'.$i, $filesConf[$i]);
    $pag_indagini -> set_var('files.nome.'.$i, $filesNome[$i]);
    $pag_indagini -> set_var('files.path.'.$i, $filesPath[$i]);
}
$pag_indagini -> set_var('filesNum', $f);




$pag_indagini->out('template_page_indagini');
?>
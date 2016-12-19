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


$indaginiId = getArray('id', 'indagini', 'idPaziente='.$idPaziente);
$indaginiTipo = getArray('tipoIndagine', 'indagini', 'idPaziente='.$idPaziente);
$indaginiData = getArray('DATE(data)', 'indagini', 'idPaziente='.$idPaziente);
$indaginiReferto = getArray('referto', 'indagini', 'idPaziente='.$idPaziente);
$indaginiAllegato = getArray('allegato', 'indagini', 'idPaziente='.$idPaziente);
$indaginiCp = getArray('idcpp', 'indagini', 'idPaziente='.$idPaziente);
$indaginiCentro = getArray('idStudioIndagini', 'indagini', 'idPaziente='.$idPaziente);
$indaginiMotivo = getArray('motivo', 'indagini', 'idPaziente='.$idPaziente);
$indaginiStato = getArray('stato', 'indagini', 'idPaziente='.$idPaziente);


$n = count($indaginiId);

for($i=0; $i<$n; $i++){
    $pag_indagini -> set_var('ind.id.'.$i, $indaginiId[$i]);
    $pag_indagini -> set_var('ind.tipo.'.$i, $indaginiTipo[$i]);
    $pag_indagini -> set_var('ind.data.'.$i, $indaginiData[$i]);
    $pag_indagini -> set_var('ind.referto.'.$i, $indaginiReferto[$i]);
    $pag_indagini -> set_var('ind.allegato.'.$i, $indaginiAllegato[$i]);

    $pag_indagini -> set_var('ind.cpId.'.$i, $indaginiCp[$i]);
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
}
$pag_indagini -> set_var('indaginiNum', $n);

$pag_indagini->out('template_page_indagini');
?>
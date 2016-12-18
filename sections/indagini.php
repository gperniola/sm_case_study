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

/*// Costruisco un array con tutte le indagini del paziente
$arrInd = getArray('patologia', 'indagini', 'idPaziente='.$idPaziente);

// Recupero le altre informazioni
$arrId = getArray('id', 'diagnosi', 'idPaziente='.$idPaziente);
$arrStato = getArray('stato', 'diagnosi', 'idPaziente='.$idPaziente);
$arrConf = getArray('conf', 'diagnosi', 'idPaziente='.$idPaziente);
$arrAgg = getArray('DATE(dataAgg)', 'diagnosi', 'idPaziente='.$idPaziente);*/


$indaginiId = getArray('id', 'indagini', 'idPaziente='.$idPaziente);
$indaginiTipo = getArray('tipoIndagine', 'indagini', 'idPaziente='.$idPaziente);
$indaginiData = getArray('DATE(data)', 'indagini', 'idPaziente='.$idPaziente);
$indaginiReferto = getArray('referto', 'indagini', 'idPaziente='.$idPaziente);
$indaginiAllegato = getArray('allegato', 'indagini', 'idPaziente='.$idPaziente);

$n = count($indaginiId);

for($i=0; $i<$n; $i++){
    $pag_indagini -> set_var('ind.id.'.$i, $indaginiId[$i]);
    $pag_indagini -> set_var('ind.tipo.'.$i, $indaginiTipo[$i]);
    $pag_indagini -> set_var('ind.data.'.$i, $indaginiData[$i]);
    $pag_indagini -> set_var('ind.referto.'.$i, $indaginiReferto[$i]);
    $pag_indagini -> set_var('ind.allegato.'.$i, $indaginiAllegato[$i]);
}
$pag_indagini -> set_var('indaginiNum', $n);

$pag_indagini->out('template_page_indagini');
?>
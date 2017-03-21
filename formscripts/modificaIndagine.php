<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idIndagine = $_POST['idIndagine'];
    $idPaziente = $_POST['idPaziente'];
    $idCare = $_POST['idCare'];
    $tipo = $_POST['tipo'];
    $idMotivo = $_POST['idMotivo'];
    $motivoAltro = $_POST['motivoAltro'];
    $careprovider = $_POST['careprovider'];
    $careproviderAltro = $_POST['careproviderAltro'];
    $stato = $_POST['stato'];
    $centro = $_POST['centro'];
    $data = $_POST['data'];
    $referto = $_POST['referto'];
    $allegato = $_POST['allegato'];

    session_start();
    $id_Pz = $_SESSION['pz_Id'];

    // Partendo dall'id utente ($id_Pz) ottengo il relativo id paziente
    $idPazienteConnesso = getInfo('id', 'pazienti', 'idutente = ' .$id_Pz);

    // Prendo l'id del paziente a cui è assegnata l'indagine
    $idPazienteIndagine = getInfo('idpaziente', 'indagini', 'id = ' .$idIndagine);


    //echo'<script>alert("idPaz='.$idPaziente.' PazConn='.$idPazienteConnesso.' PazInd='.$idPazienteIndagine.' IdInd='.$idIndagine.'");</script>';

    if (isset ($_SESSION['cp_Id'])) {
        $id_Cp = $_SESSION['cp_Id'];
        $id_prop = $id_Cp;
    } else
        $id_prop = $id_Pz;

    if ($idPazienteConnesso == $idPaziente){
        if($idPazienteConnesso == $idPazienteIndagine){
            if($careprovider != '')
                $careproviderNome = getInfo('nome', 'careproviderpersona', 'id='.$careprovider) . ' ' .
                    getInfo('cognome', 'careproviderpersona', 'id='.$careprovider);
            else{
                $careprovider = "NULL";
                $careproviderNome = $careproviderAltro;
            }
            if($idMotivo != '') $motivo = getInfo('patologia', 'diagnosi','id='.$idMotivo);
            else{
                $idMotivo = "NULL";
                $motivo = $motivoAltro;
            }
            if($stato == "0"){
                echo modificaIndagineRichiesta($idIndagine, $careprovider, $careproviderNome, $idMotivo, $motivo, 0, $tipo);
            }else if ($stato == "1"){
                echo modificaIndagineProgrammata($idIndagine, $careprovider, $careproviderNome, $idMotivo, $motivo, 1, $tipo, $data, $centro);
            }else if  ($stato == "2") {
                echo modificaIndagineCompletata($idIndagine, $careprovider, $careproviderNome, $idMotivo, $motivo, 2, $tipo, $data, $centro, $referto, $allegato);
            }
        }
        else
            echo'<script>alert("Errore: L\'indagine in modifica non appartiene al paziente connesso");</script>';
    }
    else
        echo'<script>alert("Errore: il paziente in modifica non corrisponde al paziente connesso");</script>';

}
?>


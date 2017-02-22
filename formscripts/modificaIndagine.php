<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
    $idPazienteConnesso = getInfo('id', 'pazienti', 'idutente = ' . $id_Pz);
    if (isset ($_SESSION['cp_Id'])) {
        $id_Cp = $_SESSION['cp_Id'];
        $id_prop = $id_Cp;
    } else
        $id_prop = $id_Pz;


}



?>


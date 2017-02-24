<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idIndagine = $_POST['idIndagine'];

    session_start();
    $id_Pz = $_SESSION['pz_Id'];

// Partendo dall'id utente ($id_Pz) ottengo il relativo id paziente
    $idPazienteConnesso = getInfo('id', 'pazienti', 'idutente = ' . $id_Pz);

// Prendo l'id del paziente a cui è assegnata l'indagine
    $idPazienteIndagine = getInfo('idpaziente', 'indagini', 'id = ' . $idIndagine);

    if (isset ($_SESSION['cp_Id'])) {
        $id_Cp = $_SESSION['cp_Id'];
        $id_prop = $id_Cp;
    } else
        $id_prop = $id_Pz;

    if($idPazienteConnesso == $idPazienteIndagine){
        modificaStatoIndagine($idIndagine, "eliminata");
        eliminaIndagine($idIndagine, $idPazienteIndagine);
    }
    else
        echo'<script>alert("Errore: L\'indagine in eliminazione non appartiene al paziente connesso");</script>';
}


?>
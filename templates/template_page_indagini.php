<!--PAGE CONTENT -->
<!--nella pagina vengono riportate in sezioni diverse le indagini diagnostiche richieste nella pagina "indagini richieste",
le indagini diagnostiche programmate , quelle effettuate, quelle refertate,queste devono essere evidenziabili
se rilevanti per la storiaclinica -->
<?php
//determino se l'utente è un paziente o un careprovider e di conseguenza si determina il $pz_id
if ( isset ($_GET["cp_Id"]))
{
    $cp_id = $_GET["cp_Id"]; //inizializzo $cp_id col valore passato con GET
    $myRole = getRole($cp_id);//ottengo il ruolo dal care provider
    $role = "cp";//  $role nel caso l'accesso sia di un careprovider la variabile $role è inizializzata a 'cp'
}
else
{
    $myRole = getRole(getMyID());
    $role = "pz";//  $role nel caso l'accesso sia di un paziente la variabile $role è inizializzata a 'pz'
}

if ( isset ($_GET["pz_Id"]))
    $pz_id = $_GET["pz_Id"]; //inizializzo $cp_id col valore passato con GET
else
    $pz_id  = getMyID();
?>


<div id="content">
    <div class="inner" style="min-height:1200px;">
        <?php
        include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');
        include_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Login/Database.php');

        //SE L'ACCESSO VIENE ESEGUITO TRAMITE POST, OVVERO TRAMITE CLICK SU UNA DIAGNOSI...
        $accesso_da_menu;
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            //RECUPERO I DATI RELATIVI ALLA DIAGNOSI COLLEGATA ALLE INDAGINI
            $idDiagnosi = $_POST["idDiagnosi"];
            $idPaziente    = getInfo('idPaziente','diagnosi','id='.$idDiagnosi);
            $nomePatologia = getInfo('patologia','diagnosi','id='.$idDiagnosi);
            $dataDiagnosi  = getInfo('DATE(datains)', 'diagnosi', 'id='.$idDiagnosi);
            $accesso_da_menu = false;
            $stringa_diagnosi = "Indagini relative alla diagnosi <b>" . $nomePatologia . "</b> del <b>" . italianFormat($dataDiagnosi) . "</b>";
        }else{
            //ALTRIMENTI, L'ACCESSO E' AVVENUTO DA MENU...
            $accesso_da_menu = true;

            $myRole = getRole(getMyID());
            $maxConfidentiality = 0;
            $defaultPermissions = false;
            if($myRole == 'ass' or $myRole == 'emg'
                or getInfo('idcpp', 'careproviderpaziente', 'idutente = 1') == getMyID()){
                $response = 'Permit';
                $maxConfidentiality = INF;
                $defaultPermissions = true;
            }
            else $response = getResponse('Indagini diagnostiche', 'Lettura');
            if ($response == 'Permit'){
                setAuditAction(getMyID(), 'Accesso a Diario indagini diagnostiche');
                if ($maxConfidentiality == 0)
                    $maxConfidentiality = policyInfo('Diario indagini diagnostiche', 'Confidenzialità');
                if (!$defaultPermissions){
                    $obligations = policyInfo('Indagini diagnostiche', 'Obblighi');
                    if ($obligations == 'In presenza del titolare' && $myRole != 'ass')
                        echo "Questa sezione può essere consultata solo in presenza del titolare" .
                            "<br>";}
            }
            else echo "<h5>Permesso negato<h5>";
        }

       //VARIABILE PER L'USO NEL FILE .JS
        echo '<div id="menu_mode" data-menu="' . $accesso_da_menu . '"></div>';

        // ------ CARICAMENTO DATI DA INDAGINI.PHP --------
        $stato_richiesta = 0;
        $stato_programmata = 1;
        $stato_completata = 2;
        $array_richieste = array();     //array indagini richieste
        $array_programmate = array();   //array indagini programmate
        $array_completate = array();    //array indagini completate
        $nRic = 0;                      //numero indagini richieste
        $nPro = 0;                      //numero indagini programmate
        $nCom = 0;                      //numero completate
        global $offset; $offset = 24;   //offset del vettore per l'inserimento di tutti i dati
        global $nIndagini;              //numero totale di indagini
        $nIndagini = $this->get_var('indaginiNum');

        //SE SONO CAREPROVIDER, RECUPERO ID E CONFIDENZIALITA' COL PAZIENTE
        $SelfCareproviderId =  $this->get_var('idUtenteCp');
        $SelfcareproviderConf = $this->get_var('confidenzialita');

        //RECUPERO DATI PER OGNI INDAGINE...
        for ($i = 0; $i < $nIndagini; $i++){
            //SE L'ACCESSO E' DA MENU O SE IL MOTIVO DELL'INDAGINE E' ASSOCIATO ALLA DIAGNOSI...
            if($accesso_da_menu || $this->get_var('ind.idDiagno.'.$i) == $idDiagnosi  ) {
                //SE SONO IL PAZIENTE O SE SONO UN CAREPROVIDER CON CONFIDENZIALITA' SUFFICIENTE...
                if($role == "pz" || $SelfcareproviderConf >= $this->get_var('ind.conf.'.$i)){
                    $stato = $this->get_var('ind.stato.' . $i);  //verifica lo stato
                    switch ($stato) {
                        case $stato_richiesta:
                            $array_richieste[$nRic + 0] = $this->get_var('ind.id.' . $i);           //id indagine
                            $array_richieste[$nRic + 1] = $this->get_var('ind.tipo.' . $i);         //tipo indagine
                            $array_richieste[$nRic + 2] = $this->get_var('ind.motivo.' . $i);       //motivazione
                            $array_richieste[$nRic + 3] = $this->get_var('ind.stato.' . $i);        //stato indagine
                            $array_richieste[$nRic + 4] = $this->get_var('ind.data.' . $i);         //data indagine
                            $array_richieste[$nRic + 5] = $this->get_var('ind.refertoId.' . $i);    //id del referto
                            $array_richieste[$nRic + 6] = $this->get_var('ind.allegatoId.' . $i);   //id dell'allegato
                            $array_richieste[$nRic + 7] = $this->get_var('ind.cpId.' . $i);         //id careprovider
                            $array_richieste[$nRic + 8] = $this->get_var('ind.cpNome.' . $i);       //nome careprovider
                            $array_richieste[$nRic + 9] = $this->get_var('ind.cpCognome.' . $i);    //cognome careprovider
                            $array_richieste[$nRic + 10] = $this->get_var('ind.cpRep.' . $i);       //ruolo careprovider
                            $array_richieste[$nRic + 11] = $this->get_var('ind.centroId.' . $i);    //id centro diagnostico
                            $array_richieste[$nRic + 12] = $this->get_var('ind.centroNome.' . $i);  //nome centro diagnostico
                            $array_richieste[$nRic + 13] = $this->get_var('ind.centroVia.' . $i);   //via centro diagnostico
                            $array_richieste[$nRic + 14] = $this->get_var('ind.centroCitta.' . $i); //citta centro diagnostico
                            $array_richieste[$nRic + 15] = $this->get_var('ind.idDiagno.' . $i);    //id diagnosi associata
                            $array_richieste[$nRic + 16] = $this->get_var('ind.careprovider.' . $i);//careprovider non registrato
                            $array_richieste[$nRic + 17] = $this->get_var('ind.conf.' . $i);        //confidenzialità della diagnosi
                            $array_richieste[$nRic + 18] = $this->get_var('ind.refertoNome.' . $i); //nome filereferto
                            $array_richieste[$nRic + 19] = $this->get_var('ind.refertoFullpath.' . $i);//path esteso al referto
                            $array_richieste[$nRic + 20] = $this->get_var('ind.refertoConf.' . $i); //confidenzialita' referto
                            $array_richieste[$nRic + 21] = $this->get_var('ind.allegatoNome.' . $i);//nome fileallegato
                            $array_richieste[$nRic + 22] = $this->get_var('ind.allegatoFullpath.' . $i);//path esteso al allegato
                            $array_richieste[$nRic + 23] = $this->get_var('ind.allegatoConf.' . $i);//confidenzialita' allegato
                            $nRic = $nRic + $offset;
                            break;
                        case $stato_programmata:
                            $array_programmate[$nPro + 0] = $this->get_var('ind.id.' . $i);         //id indagine
                            $array_programmate[$nPro + 1] = $this->get_var('ind.tipo.' . $i);       //tipo indagine
                            $array_programmate[$nPro + 2] = $this->get_var('ind.motivo.' . $i);     //motivazione
                            $array_programmate[$nPro + 3] = $this->get_var('ind.stato.' . $i);      //stato indagine
                            $array_programmate[$nPro + 4] = $this->get_var('ind.data.' . $i);       //data indagine
                            $array_programmate[$nPro + 5] = $this->get_var('ind.refertoId.' . $i);
                            $array_programmate[$nPro + 6] = $this->get_var('ind.allegatoId.' . $i);
                            $array_programmate[$nPro + 7] = $this->get_var('ind.cpId.' . $i);       //id careprovider
                            $array_programmate[$nPro + 8] = $this->get_var('ind.cpNome.' . $i);     //nome careprovider
                            $array_programmate[$nPro + 9] = $this->get_var('ind.cpCognome.' . $i);  //cognome careprovider
                            $array_programmate[$nPro + 10] = $this->get_var('ind.cpRep.' . $i);     //ruolo careprovider
                            $array_programmate[$nPro + 11] = $this->get_var('ind.centroId.' . $i);  //id centro diagnostico
                            $array_programmate[$nPro + 12] = $this->get_var('ind.centroNome.' . $i);//nome centro diagnostico
                            $array_programmate[$nPro + 13] = $this->get_var('ind.centroVia.' . $i); // via centro diagnostico
                            $array_programmate[$nPro + 14] = $this->get_var('ind.centroCitta.' . $i);   //citta centro diagnostico
                            $array_programmate[$nPro + 15] = $this->get_var('ind.idDiagno.' . $i);   //id diagnosi associata
                            $array_programmate[$nPro + 16] = $this->get_var('ind.careprovider.' . $i);   //careprovider non registrato
                            $array_programmate[$nPro + 17] = $this->get_var('ind.conf.' . $i);   //confidenzialità della diagnosi
                            $array_programmate[$nPro + 18] = $this->get_var('ind.refertoNome.' . $i);   //nome filereferto
                            $array_programmate[$nPro + 19] = $this->get_var('ind.refertoFullpath.' . $i);   //path esteso al referto
                            $array_programmate[$nPro + 20] = $this->get_var('ind.refertoConf.' . $i);   //confidenzialita' referto
                            $array_programmate[$nPro + 21] = $this->get_var('ind.allegatoNome.' . $i);   //nome fileallegato
                            $array_programmate[$nPro + 22] = $this->get_var('ind.allegatoFullpath.' . $i);   //path esteso al allegato
                            $array_programmate[$nPro + 23] = $this->get_var('ind.allegatoConf.' . $i);   //confidenzialita' allegato
                            $nPro = $nPro + $offset;
                            break;
                        case $stato_completata:
                            $array_completate[$nCom + 0] = $this->get_var('ind.id.' . $i);         //id indagine
                            $array_completate[$nCom + 1] = $this->get_var('ind.tipo.' . $i);       //tipo indagine
                            $array_completate[$nCom + 2] = $this->get_var('ind.motivo.' . $i);     //motivazione
                            $array_completate[$nCom + 3] = $this->get_var('ind.stato.' . $i);      //stato indagine
                            $array_completate[$nCom + 4] = $this->get_var('ind.data.' . $i);       //data indagine
                            $array_completate[$nCom + 5] = $this->get_var('ind.refertoId.' . $i);
                            $array_completate[$nCom + 6] = $this->get_var('ind.allegatoId.' . $i);
                            $array_completate[$nCom + 7] = $this->get_var('ind.cpId.' . $i);       //id careprovider
                            $array_completate[$nCom + 8] = $this->get_var('ind.cpNome.' . $i);     //nome careprovider
                            $array_completate[$nCom + 9] = $this->get_var('ind.cpCognome.' . $i);  //cognome careprovider
                            $array_completate[$nCom + 10] = $this->get_var('ind.cpRep.' . $i);     //ruolo careprovider
                            $array_completate[$nCom + 11] = $this->get_var('ind.centroId.' . $i);  //id centro diagnostico
                            $array_completate[$nCom + 12] = $this->get_var('ind.centroNome.' . $i);//nome centro diagnostico
                            $array_completate[$nCom + 13] = $this->get_var('ind.centroVia.' . $i); // via centro diagnostico
                            $array_completate[$nCom + 14] = $this->get_var('ind.centroCitta.' . $i);   //citta centro diagnostico
                            $array_completate[$nCom + 15] = $this->get_var('ind.idDiagno.' . $i);   //id diagnosi associata
                            $array_completate[$nCom + 16] = $this->get_var('ind.careprovider.' . $i);   //careprovider non registrato
                            $array_completate[$nCom + 17] = $this->get_var('ind.conf.' . $i);   //confidenzialità della diagnosi
                            $array_completate[$nCom + 18] = $this->get_var('ind.refertoNome.' . $i);   //nome filereferto
                            $array_completate[$nCom + 19] = $this->get_var('ind.refertoFullpath.' . $i);   //path esteso al referto
                            $array_completate[$nCom + 20] = $this->get_var('ind.refertoConf.' . $i);   //confidenzialita' referto
                            $array_completate[$nCom + 21] = $this->get_var('ind.allegatoNome.' . $i);   //nome fileallegato
                            $array_completate[$nCom + 22] = $this->get_var('ind.allegatoFullpath.' . $i);   //path esteso al allegato
                            $array_completate[$nCom + 23] = $this->get_var('ind.allegatoConf.' . $i);   //confidenzialita' allegato
                            $nCom = $nCom + $offset;
                            break;
                    }
                }
            }

        }
        //FINE RECUPERO PER OGNI INDAGINE


        //RECUPERO INFORMAZIONI DI TUTTI I CENTRI DIAGNOSTICI...
        $array_centri = array();
        $nCentriIns = 0;
        global $n_centri;             //numero totale di centri
        $n_centri = $this->get_var('centriNum');
        for ($i = 0; $i < $n_centri; $i++) {          //per ogni centro...
            $array_centri[$nCentriIns + 0] = $this->get_var('centro.id.'.$i);
            $array_centri[$nCentriIns + 1] = $this->get_var('centro.nome.'.$i);
            $array_centri[$nCentriIns + 2] = $this->get_var('centro.via.'.$i);
            $array_centri[$nCentriIns + 3] = $this->get_var('centro.citta.'.$i);
            $array_centri[$nCentriIns + 4] = $this->get_var('centro.tipo.'.$i);
            $array_centri[$nCentriIns + 5] = $this->get_var('centro.mail.'.$i);
            $array_centri[$nCentriIns + 6] = $this->get_var('centro.responsabileId.'.$i);
            $array_centri[$nCentriIns + 7] = $this->get_var('centro.responsabileNome.'.$i);
            $array_centri[$nCentriIns + 8] = $this->get_var('centro.responsabileCognome.'.$i);
            $array_centri[$nCentriIns + 9] = $this->get_var('centro.contatti.'.$i);
            $nCentriIns = $nCentriIns + 10;
        }

        //RECUPERO INFORMAZIONI DI TUTTE LE DIAGNOSI COLLEGATE AL PAZIENTE...
        $array_diagnosi = array();
        $nDiagnoIns = 0;
        global $n_diagnosi;
        $n_diagnosi = $this->get_var('diagnosiNum');
        for ($i = 0; $i < $n_diagnosi; $i++) {
            $array_diagnosi[$nDiagnoIns + 0] = $this->get_var('diagnosi.id.'.$i);
            $array_diagnosi[$nDiagnoIns + 1] = $this->get_var('diagnosi.data.'.$i);
            $array_diagnosi[$nDiagnoIns + 2] = $this->get_var('diagnosi.patologia.'.$i);
            $array_diagnosi[$nDiagnoIns + 3] = $this->get_var('diagnosi.conf.'.$i);
            $nDiagnoIns = $nDiagnoIns + 4;
        }

        //RECUPERO INFORMAZIONI DI TUTTI I CAREPROVIDER REGISTRATI AL PAZIENTE...
        $array_careprovider = array();
        $n_v = 0;
        global $n_careprovider;
        $n_careprovider = $this->get_var('careproviderNum');
        for ($i = 0; $i < $n_careprovider; $i++) {
            $array_careprovider[$n_v + 0] = $this->get_var('careprovider.id.'.$i);
            $array_careprovider[$n_v + 1] = $this->get_var('careprovider.nome.'.$i);
            $array_careprovider[$n_v + 2] = $this->get_var('careprovider.cognome.'.$i);
            $n_v =  $n_v + 3;
        }

        //RECUPERO INFORMAZIONI DEI REFERTI E ALLEGATI CARICATI DAL PAZIENTE...
        $array_referti = array();
        $array_allegati = array();
        $n_ref_tot = $this->get_var('refertiNum');
        $n_all_tot = $this->get_var('allegatiNum');
        $n_ref = 0;
        $n_all = 0;
        for ($i = 0; $i < $n_ref_tot; $i++) {

            $array_referti[$n_ref + 0] = $this->get_var('referti.id.' . $i);
            $array_referti[$n_ref + 1] = $this->get_var('referti.data.' . $i);
            $array_referti[$n_ref + 2] = $this->get_var('referti.nome.' . $i);
            $array_referti[$n_ref + 3] = $this->get_var('referti.path.' . $i);
            $array_referti[$n_ref + 4] = $this->get_var('referti.conf.' . $i);
            $n_ref = $n_ref + 5;
        }
        for ($i = 0; $i < $n_all_tot; $i++) {
            $array_allegati[$n_all + 0] = $this->get_var('allegati.id.'.$i);
            $array_allegati[$n_all + 1] = $this->get_var('allegati.data.'.$i);
            $array_allegati[$n_all + 2] = $this->get_var('allegati.nome.'.$i);
            $array_allegati[$n_all + 3] = $this->get_var('allegati.path.' . $i);
            $array_allegati[$n_all + 4] = $this->get_var('allegati.conf.'.$i);
            $n_all = $n_all + 5;
        }

        $mioCpNome = $this->get_var('mioCpNome');
        $mioCpCognome = $this->get_var('mioCpCognome');
        // --------------------------------------
        ?>


        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="assets/js/moment-with-locales.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
        <script src="formscripts/jquery-ui.js"></script>
        <script src="formscripts/indagini.js"></script>


        <hr>
        <h2>Indagini diagnostiche</h2>
        <p>In questa pagina è possibile visualizzare tutti gli esami che un paziente deve effettuare o ha già effettuato e
            l'elenco di tutti gli studi e laboratori dove è possibile effettuare un determinato esame.</p>
        <hr/>

       <!-- ACCORDION -->
        <div class="panel-group ac" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading row">
                    <div class="col-lg-6">
                        <h3><a data-toggle="collapse" data-parent="#accordion" href="#collapse1"><i class="icon-book"></i>
                                Diario indagini diagnostiche</a></h3>
                    </div>
                    <div class="col-lg-6">
                        <h3><a data-toggle="collapse" data-parent="#accordion" href="#collapse2"><i class="icon-map-marker"></i>
                            Centri indagini diagnostiche</a></h3>
                    </div>
                </div>

                <!-- COLLAPSE DIARIO INDAGINI DIAGNOSTICHE -->
                <div id="collapse1" class="panel-collapse collapse in"><hr/>

                    <!-- FORM NUOVA INDAGINE -->
                <div class="row">
                    <div class="col-lg-12" >
                        <div class="btn-group">
                            <button class="btn btn-primary" id="nuovoFile"><i class="icon-file-text-alt"></i> Nuova indagine</button>
                            <button class="btn btn-primary" id="concludi"><i class="icon-ok-sign"></i> Concludi indagine</button>
                            <button class="btn btn-primary" id="annulla"><i class="icon-trash"></i> Annulla indagine</button>
                        </div>
                    </div>
                </div>
                <form style="display:none;" id="formIndagini" action="formscripts/indagini.php" method="POST" class="form-horizontal" >
                    <div class="tab-content">
                        <div class="row">
                             <div style="display:none;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">ID Paziente:</label>
                                        <div class="col-lg-4">
                                            <input id="idPaziente" type="text"  readonly class="form-control" value="<?php echo $this->get_var('idPaz'); ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">ID CP:</label>
                                        <div class="col-lg-4">
                                            <input id="cpId" type="text"  readonly class="form-control"
                                            <?php
                                            if ($role == "cp") {echo 'value="'.$SelfCareproviderId.'"';}
                                            else {echo 'value="-1"';}
                                            ?>
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- End hidden row -->
                            <div hidden class="col-lg-6 alert alert-danger" id="formAlert_new" role="alert"  style="float: none; margin: 0 auto;">
                                <div style="text-align: center;">
                                    <i class="glyphicon glyphicon-exclamation-sign" ></i>
                                    <strong>Attenzione:</strong> Compilare correttamente i campi bordati in rosso.
                                </div>
                            </div></br>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Tipo indagine *</label>
                                    <div class="col-lg-4">
                                        <input id="tipoIndagine" type="text"  class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Motivo *</label>
                                    <div class="col-lg-4">
                                        <select id="motivoIndagine_new" class="form-control">
                                            <option selected hidden style='display: none' value="placeholder">Selezionare una motivazione..</option>
                                            <optgroup label="Diagnosi del paziente">
                                            <?php
                                            for($i = 0; $i < $nDiagnoIns; $i +=4 ){
                                                if($role == "pz" || $SelfcareproviderConf >= $array_diagnosi[$i+3] ){
                                                    echo '<option value="'.$array_diagnosi[$i+0] .'">' .$array_diagnosi[$i+1] .' - '
                                                        .$array_diagnosi[$i+2].'</option>';
                                                }
                                            }
                                            ?>
                                            </optgroup>
                                            <option value=''>Altra Motivazione..</option>
                                        </select>
                                        <input id="motivoAltro_new" type="text" placeholder="Inserire motivazione.."  class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Care provider *</label>
                                    <div class="col-lg-4">
                                        <?php
                                        if ($role == "cp")
                                            echo ' <select disabled id="careproviderIndagine_new" class="form-control">';
                                        else
                                            echo '<select id="careproviderIndagine_new" class="form-control">';
                                        echo '<option selected hidden style="display: none" value="placeholder">Selezionare una careprovider..</option>';
                                        echo '<optgroup label="Careproviders registrati">';
                                        for($i = 0; $i < $n_v; $i +=3 ){
                                            if($SelfCareproviderId == $array_careprovider[$i+0])
                                                echo '<option selected value="'.$array_careprovider[$i+0] .'">' .$array_careprovider[$i+1] .' ' .$array_careprovider[$i+2].'</option>';
                                            else
                                                echo '<option value="'.$array_careprovider[$i+0] .'">' .$array_careprovider[$i+1] .' ' .$array_careprovider[$i+2].'</option>';
                                        }
                                        echo '</optgroup>';
                                        echo '<option value="">Nuovo careprovider..</option></select>';
                                        if ($role == "cp")
                                            echo '<input disabled id="careproviderAltro_new" type="text" placeholder="Inserire careprovider.."  class="form-control"/>';
                                        else
                                            echo '<input id="careproviderAltro_new" type="text" placeholder="Inserire careprovider.."  class="form-control"/>';
                                        ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Stato *</label>
                                    <div class="col-lg-4">
                                        <select id="statoIndagine_new" class="form-control">
                                            <option selected value="0">Richiesta</option>
                                            <option value="1">Programmata</option>
                                            <option value="2">Completata</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="divCentro_new" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Centro *</label>
                                    <div class="col-lg-4">
                                        <select id="centroIndagine_new" class="form-control">
                                            <option selected hidden style='display: none' value="placeholder">Selezionare un centro..</option>
                                            <?php
                                            for($i = 0; $i < $nCentriIns; $i +=10 ){
                                                echo '<option value="'.$array_centri[$i+0] .'">' .$array_centri[$i+1] .',  '
                                                    .$array_centri[$i+3].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="divData_new" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Data*</label>
                                    <div class="col-lg-4">
                                        <input id="data" type="text" placeholder="Selezionare una data.." class="form-control"/>
                                    </div>
                                      <script>
                                        var $j = jQuery.noConflict();
                                        $j(document).ready(function() {
                                            $j('#data').datetimepicker({
                                                locale:'it',
                                                sideBySide:true
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="col-lg-12" id="divReferto_new" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Referto</label>
                                    <div class="col-lg-4" >
                                        <select id="refertoIndagine_new" class="form-control">
                                            <option selected hidden style='display: none' value="">Selezionare un file..</option>
                                            <?php
                                            for($i = 0; $i < $n_ref; $i +=5 ){
                                                if($role == "pz" || $SelfcareproviderConf >= $array_referti[$i+4] ){
                                                    echo '<option value="'.$array_referti[$i+0] .'">' .$array_referti[$i+1] .' - '
                                                        .$array_referti[$i+2].'</option>';
                                                }
                                            }
                                            ?>
                                            <option value="">Nessuno</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="divAllegato_new" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Allegato</label>
                                    <div class="col-lg-4">
                                        <select id="allegatoIndagine_new" class="form-control">
                                            <option selected hidden style='display: none' value="" >Selezionare un file..</option>
                                            <?php
                                            for($i = 0; $i < $n_all; $i +=5 ){
                                                if($role == "pz" || $SelfcareproviderConf >= $array_allegati[$i+4] ){
                                                    echo '<option value="'.$array_allegati[$i+0] .'">' .$array_allegati[$i+1] .' - '
                                                        .$array_allegati[$i+2].'</option>';
                                                }
                                            }
                                            ?>
                                            <option value="">Nessuno</option>
                                        </select>
                                    </div>
                                </div>
                                <div class=" col-lg-6 alert alert-info" role="alert" style="float: none; margin: 0 auto;" >
                                    <div style="text-align:center;">
                                        <strong>Attenzione:</strong> Per selezionare un file come referto o allegato è necessario caricarlo
                                        preventivamente nella sezione <strong>Files</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- FINE FORM NUOVA INDAGINE -->
                <br>

                <!-- STRINGA DIAGNOSI SE ACCESSO DA POST -->
                <div id="info_diagnosi" align="center"><h4> <?php echo $stringa_diagnosi; ?></h4></div>

                <!-- TABELLA INDAGINI RICHIESTE -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-warning">
                            <div class="panel-heading">Indagini Richieste</div>
                            <div class=" panel-body">
                                <div class="table-responsive" >
                                    <table class="table" id="tableRichieste">
                                        <thead>
                                        <tr>
                                            <th>Indagine</th><th>Motivo</th><th>Care provider</th><th style="text-align:center">Opzioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php   //popolamento tabella indagini richieste
                                        for($i = 0; $i < $nRic; $i+=$offset){
                                            echo '<tr class="info" id="r'.$array_richieste[$i+0].'">';
                                            echo '<td id="tipoRichiesta'.$array_richieste[$i+0].'">' . $array_richieste[$i+1] . '</td>';
                                            echo '<td id="motivoRichiesta'.$array_richieste[$i+0].'">' . $array_richieste[$i+2] . '</td>';
                                            echo '<td id="careRichiesta'.$array_richieste[$i+0].'">' . $array_richieste[$i+16] .'</td>';
                                            echo '<td style="text-align:center"><div id="btn-group">
										            <button id='.$array_richieste[$i+0].' class="modifica btn btn-success "><i class="icon-pencil icon-white"></i></button>
												    <button id='.$array_richieste[$i+0].' class="elimina btn btn-danger"><i class="icon-remove icon-white"></i></button>
												 </div></td></tr>';
                                            //inserimento riga con form di modifica indagine
                                            echo '
                                            <tr id="riga'.$array_richieste[$i+0].'" style="display:none">
		                                        <td colspan="4">';
                                            echo'
		                                            <form class="form-horizontal">
		                                                <div class="row">
		                                                    <div hidden class="col-lg-6 alert alert-danger" id="formAlert_'.$array_richieste[$i+0].'" role="alert"  style="float: none; margin: 0 auto;">
                                                                <div style="text-align: center;">
                                                                    <i class="glyphicon glyphicon-exclamation-sign" ></i>
                                                                    <strong>Attenzione:</strong> Compilare correttamente i campi bordati in rosso.
                                                                </div>
                                                            </div></br>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Tipo indagine *</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="tipoIndagine'.$array_richieste[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_richieste[$i+1].'"/>
                                                                    </div>
                                                                </div>
					                                        </div>
					                                        <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Motivo *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="motivoIndagine_'.$array_richieste[$i+0].'" class="form-control">
                                                                            <option selected value=\'\'>Altra motivazione..</option>';
                                                                                    for($k = 0; $k < $nDiagnoIns; $k +=4 ) {
                                                                                        if ($role == "pz" || $SelfcareproviderConf >= $array_diagnosi[$k + 3]) {
                                                                                            if ($array_diagnosi[$k + 0] == $array_richieste[$i + 15])
                                                                                                echo '<option selected value=\'' . $array_diagnosi[$k + 0] . '\'>' . $array_diagnosi[$k + 1] . ' - ' . $array_diagnosi[$k + 2] . '</option>';
                                                                                            else
                                                                                                echo '<option value=\'' . $array_diagnosi[$k + 0] . '\'>' . $array_diagnosi[$k + 1] . ' - ' . $array_diagnosi[$k + 2] . '</option>';
                                                                                        }
                                                                                    }
                                                                                echo '
                                                                        </select>
                                                                        <input id="motivoAltro_'.$array_richieste[$i+0].'" type="text" placeholder="Inserire motivazione.."  class="form-control" value="'. $array_richieste[$i+2] .'"/>
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Care provider *</label>
                                                                    <div class="col-lg-4">';
                                                                        if ($role == "cp")
                                                                            echo '<select disabled id="careproviderIndagine_'.$array_richieste[$i+0].'" class="form-control">';
                                                                        else
                                                                            echo '<select id="careproviderIndagine_'.$array_richieste[$i+0].'" class="form-control">';
                                                                        echo ' <option selected value=\'\'>Altro..</option>';
                                                                        for($k = 0; $k < $n_v; $k +=3 ) {
                                                                            if ($array_careprovider[$k+0] == $array_richieste[$i+7])
                                                                                echo '<option selected value=\'' . $array_careprovider[$k + 0] . '\'>' . $array_careprovider[$k+1] . ' ' . $array_careprovider[$k+2] . '</option>';
                                                                            else
                                                                                echo '<option value=\'' . $array_careprovider[$k + 0] . '\'>' .$array_careprovider[$k+1] . ' ' . $array_careprovider[$k+2] . '</option>';
                                                                        }
                                                                        echo '</select>';
                                                                        if ($role == "cp")
                                                                            echo '<input disabled id="careproviderAltro_'.$array_richieste[$i+0].'" type="text" placeholder="Inserire careprovider.."  class="form-control" value="'. $array_richieste[$i+16] .'"/>';
                                                                        else
                                                                            echo '<input id="careproviderAltro_'.$array_richieste[$i+0].'" type="text" placeholder="Inserire careprovider.."  class="form-control" value="'. $array_richieste[$i+16] .'"/>';
                                                                        echo'
                                                                        </div>
                                                                 </div>
                                                             </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Stato *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="statoIndagine_'.$array_richieste[$i+0].'" class="form-control">
                                                                            <option selected value="0">Richiesta</option>
                                                                            <option value="1">Programmata</option>
                                                                            <option value="2">Completata</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divCentro_'.$array_richieste[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Centro *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="centroIndagine'.$array_richieste[$i+0].'" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value=\'\'>Selezionare un centro..</option>';
                                                                        for($k = 0; $k < $nCentriIns; $k +=10 ){
                                                                            if( $array_centri[$k+0] == $array_richieste[$i+11])
                                                                                echo '<option selected value="'.$array_centri[$k+0] .'">' .$array_centri[$k+1] .',  '.$array_centri[$k+3].'</option>';
                                                                            else
                                                                                echo '<option value="'.$array_centri[$k+0] .'">' .$array_centri[$k+1] .',  '.$array_centri[$k+3].'</option>';
                                                                        }
                                                                        echo '</select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                             <div class="col-lg-12" id="divData_'.$array_richieste[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Data *</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="data'.$array_richieste[$i+0].'" type="text" placeholder="Selezionare una data.."  class="form-control"
                                                                        value ="'.$array_richieste[$i+4].'"/>
                                                                    </div>
                                                                    <script>
                                                                        $j(document).ready(function() {
                                                                            $j(\'#data'.$array_richieste[$i+0].'\').datetimepicker({
                                                                                locale:\'it\',
                                                                                sideBySide:true
                                                                            });
                                                                        });
                                                                    </script>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divReferto_'.$array_richieste[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Referto</label>
                                                                    <div class="col-lg-4">';
                                                                        if($role != "pz" && $array_richieste[$i + 5] != null && $SelfcareproviderConf < $array_richieste[$i + 20])
                                                                            echo '<select disabled id="refertoIndagine_'.$array_richieste[$i+0].'" class="form-control">
                                                                                    <option selected value="'.$array_richieste[$i + 5] .'">RISERVATO</option>
                                                                                  </select>';
                                                                        else{
                                                                        echo '<select id="refertoIndagine_'.$array_richieste[$i+0].'" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value="" >Selezionare un file..</option>';
                                                                                    for($k = 0; $k < $n_ref; $k +=5 ) {
                                                                                        if($role == "pz" || $SelfcareproviderConf >= $array_referti[$k+4] ){
                                                                                            if ($array_referti[$k + 0] == $array_richieste[$i + 5])
                                                                                                echo '<option selected value="'.$array_referti[$k+0] .'">' .$array_referti[$k+1] .' - ' .$array_referti[$k+2].'</option>';
                                                                                            else
                                                                                                echo '<option value="'.$array_referti[$k+0] .'">' .$array_referti[$k+1] .' - ' .$array_referti[$k+2].'</option>';
                                                                                        }
                                                                                    }
                                                                                echo '
                                                                            <option value="">Nessuno</option>
                                                                            </select>';
                                                                        }
                                                                        echo '
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divAllegato_'.$array_richieste[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Allegato</label>
                                                                    <div class="col-lg-4">';
                                                                        if($role != "pz" && $array_richieste[$i + 6] != null && $SelfcareproviderConf < $array_richieste[$i + 23])
                                                                            echo '<select disabled id="allegatoIndagine_'.$array_richieste[$i+0].'" class="form-control">
                                                                                    <option selected value="'.$array_richieste[$i + 6] .'">RISERVATO</option>
                                                                                  </select>';
                                                                        else {
                                                                            echo ' <select id="allegatoIndagine_' . $array_richieste[$i + 0] . '" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value="" >Selezionare un file..</option>';
                                                                            for ($k = 0; $k < $n_all; $k += 5) {
                                                                                if ($role == "pz" || $SelfcareproviderConf >= $array_allegati[$k + 4]) {
                                                                                    if ($array_allegati[$k + 0] == $array_richieste[$i + 6])
                                                                                        echo '<option selected value="' . $array_allegati[$k + 0] . '">' . $array_allegati[$k + 1] . ' - ' . $array_allegati[$k + 2] . '</option>';
                                                                                    else
                                                                                        echo '<option value="' . $array_allegati[$k + 0] . '">' . $array_allegati[$k + 1] . ' - ' . $array_allegati[$k + 2] . '</option>';
                                                                                }
                                                                            }
                                                                            echo '
                                                                            <option value="">Nessuno</option>
                                                                         </select>';
                                                                        }
                                                                        echo '
                                                                    </div>
                                                                </div>
                                                                <div class=" col-lg-6 alert alert-info" role="alert" style="float: none; margin: 0 auto;" >
                                                                    <div style="text-align:center;">
                                                                        <strong>Attenzione:</strong> Per selezionare un file come referto o allegato è necessario caricarlo
                                                                        preventivamente nella sezione <strong>Files</strong>.
                                                                    </div>
                                                                </div></br>
                                                            </div>
					                                    </div>
			                                        </form>
			                                        <div style="text-align:center;">
				                                        <a href="" onclick="return false;" class=annulla id="'.$array_richieste[$i+0].'"><button class="btn btn-danger"><i class="icon icon-undo"></i> Annulla modifiche</button></a>
				                                        <a href="" onclick="return false;" class=conferma id="'.$array_richieste[$i+0].'"><button class="btn btn-success"><i class="icon icon-check"></i> Conferma modifiche</button></a>
			                                        </div>
			                                    </td>
	                                     </tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>	<!--paneldanger-->
                        </div>	<!--col lg12-->
                    </div>
                </div><br>
                <!-- TABELLA INDAGINI PROGRAMMATE -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-danger">
                            <div class="panel-heading">Indagini Programmate</div>
                            <div class=" panel-body">
                                <div class="table-responsive" >
                                    <table class="table" id="tableProgrammate">
                                        <thead>
                                        <tr>
                                            <th>Indagine</th><th>Motivo</th><th>Care provider</th><th>Data</th>
                                            <th>Centro</th><th style="text-align:center">Opzioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php   //popolamento tabella indagini programmate
                                        for($i = 0; $i < $nPro; $i+=$offset){
                                            echo '<tr class="info" id="r'.$array_programmate[$i+0].'">';
                                            echo '<td id="tipoProgrammata'.$array_programmate[$i+0].'">' . $array_programmate[$i+1] . '</td>';
                                            echo '<td id="motivoProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+2] . '</td>';
                                            echo '<td id="careProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+16] . '</td>';
                                            echo '<td id="dataProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+4] . '</td>';
                                            echo '<td id="centroProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+12] . '<br>' . $array_programmate[$i+13] . '<br>' .
                                                $array_programmate[$i+14] . '</td>';
                                            echo '<td style="text-align:center">
												<div id="btn-group">
												<button id='.$array_programmate[$i+0].' class="modifica btn btn-success "><i class="icon-pencil icon-white"></i></button>
												<button id='.$array_programmate[$i+0].' class="elimina btn btn-danger"><i class="icon-remove icon-white"></i></button>
												</div></td></tr>';
                                            echo '
                                            <tr id="riga'.$array_programmate[$i+0].'" style="display:none">
		                                        <td colspan="6">';
                                            echo'
		                                            <form class="form-horizontal">
		                                                <div class="row">
		                                                    <div hidden class="col-lg-6 alert alert-danger" id="formAlert_'.$array_programmate[$i+0].'" role="alert"  style="float: none; margin: 0 auto;">
                                                                <div style="text-align: center;">
                                                                    <i class="glyphicon glyphicon-exclamation-sign" ></i>
                                                                    <strong>Attenzione:</strong> Compilare correttamente i campi bordati in rosso.
                                                                </div>
                                                            </div></br>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Tipo indagine *</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="tipoIndagine'.$array_programmate[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_programmate[$i+1].'"/>
                                                                    </div>
                                                                </div>
					                                        </div>
					                                        <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Motivo *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="motivoIndagine_'.$array_programmate[$i+0].'" class="form-control">
                                                                            <option selected value=\'\'>Altro..</option>';
                                                                                    for($k = 0; $k < $nDiagnoIns; $k +=4 ) {
                                                                                        if ($role == "pz" || $SelfcareproviderConf >= $array_diagnosi[$k + 3]) {
                                                                                            if ($array_diagnosi[$k + 0] == $array_programmate[$i + 15])
                                                                                                echo '<option selected value=\'' . $array_diagnosi[$k + 0] . '\'>' . $array_diagnosi[$k + 1] . ' - ' . $array_diagnosi[$k + 2] . '</option>';
                                                                                            else
                                                                                                echo '<option value=\'' . $array_diagnosi[$k + 0] . '\'>' . $array_diagnosi[$k + 1] . ' - ' . $array_diagnosi[$k + 2] . '</option>';
                                                                                        }
                                                                                    }
                                                                                echo '
                                                                        </select>
                                                                        <input id="motivoAltro_'.$array_programmate[$i+0].'" type="text" placeholder="Inserire motivazione.."  class="form-control" value="'. $array_programmate[$i+2] .'"/>
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Care provider *</label>
                                                                    <div class="col-lg-4">';
                                                                    if ($role == "cp")
                                                                        echo '<select disabled id="careproviderIndagine_'.$array_programmate[$i+0].'" class="form-control">';
                                                                    else
                                                                        echo'<select id="careproviderIndagine_'.$array_programmate[$i+0].'" class="form-control">';
                                                                    echo '<option selected value=\'\'>Altro..</option>';
                                                                    for($k = 0; $k < $n_v; $k +=3 ) {
                                                                        if ($array_careprovider[$k+0] == $array_programmate[$i+7])
                                                                            echo '<option selected value=\'' . $array_careprovider[$k + 0] . '\'>' . $array_careprovider[$k+1] . ' ' . $array_careprovider[$k+2] . '</option>';
                                                                        else
                                                                            echo '<option value=\'' . $array_careprovider[$k + 0] . '\'>' .$array_careprovider[$k+1] . ' ' . $array_careprovider[$k+2] . '</option>';
                                                                    }
                                                                    echo '</select>';
                                                                    if ($role == "cp")
                                                                        echo '<input disabled id="careproviderAltro_'.$array_programmate[$i+0].'" type="text" placeholder="Inserire careprovider.."  class="form-control" value="'. $array_programmate[$i+16] .'"/>';
                                                                    else
                                                                        echo '<input id="careproviderAltro_'.$array_programmate[$i+0].'" type="text" placeholder="Inserire careprovider.."  class="form-control" value="'. $array_programmate[$i+16] .'"/>';
                                                                    echo '              
                                                                    </div>
                                                                 </div>
                                                             </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Stato *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="statoIndagine_'.$array_programmate[$i+0].'" class="form-control">
                                                                            <option selected value="1">Programmata</option>
                                                                            <option value="2">Completata</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divCentro_'.$array_programmate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Centro *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="centroIndagine'.$array_programmate[$i+0].'" class="form-control">
                                                                        <option selected hidden style="display: none" value=\'\'>Selezionare un centro..</option>';
                                                                            for($k = 0; $k < $nCentriIns; $k +=10 ){
                                                                            if( $array_centri[$k+0] == $array_programmate[$i+11])
                                                                                echo '<option selected value="'.$array_centri[$k+0] .'">' .$array_centri[$k+1] .',  '.$array_centri[$k+3].'</option>';
                                                                            else
                                                                                echo '<option value="'.$array_centri[$k+0] .'">' .$array_centri[$k+1] .',  '.$array_centri[$k+3].'</option>';
                                                                            }
                                                                        echo '</select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                             <div class="col-lg-12" id="divData_'.$array_programmate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Data *</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="data'.$array_programmate[$i+0].'" type="text" placeholder="Selezionare una data.." class="form-control" 
                                                                        value ="'.$array_programmate[$i+4].'"/>
                                                                    </div>
                                                                    <script>
                                                                        $j(document).ready(function() {
                                                                            $j(\'#data'.$array_programmate[$i+0].'\').datetimepicker({
                                                                                locale:\'it\',
                                                                                sideBySide:true
                                                                            });
                                                                        });
                                                                    </script>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divReferto_'.$array_programmate[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Referto</label>
                                                                    <div class="col-lg-4">';
                                                                        if($role != "pz" && $array_programmate[$i + 5] != null && $SelfcareproviderConf < $array_programmate[$i + 20])
                                                                            echo '<select disabled id="refertoIndagine_'.$array_programmate[$i+0].'" class="form-control">
                                                                                    <option selected value="'.$array_programmate[$i + 5] .'">RISERVATO</option>
                                                                                  </select>';
                                                                        else{
                                                                        echo '<select id="refertoIndagine_'.$array_programmate[$i+0].'" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value="" >Selezionare un file..</option>';
                                                                                    for($k = 0; $k < $n_ref; $k +=5 ) {
                                                                                        if($role == "pz" || $SelfcareproviderConf >= $array_referti[$k+4] ){
                                                                                            if ($array_referti[$k + 0] == $array_programmate[$i + 5])
                                                                                                echo '<option selected value="'.$array_referti[$k+0] .'">' .$array_referti[$k+1] .' - ' .$array_referti[$k+2].'</option>';
                                                                                            else
                                                                                                echo '<option value="'.$array_referti[$k+0] .'">' .$array_referti[$k+1] .' - ' .$array_referti[$k+2].'</option>';
                                                                                        }
                                                                                    }
                                                                                echo '
                                                                            <option value="">Nessuno</option>
                                                                            </select>';
                                                                        }
                                                                        echo '
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divAllegato_'.$array_programmate[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Allegato</label>
                                                                    <div class="col-lg-4">';
                                                                        if($role != "pz" && $array_programmate[$i + 6] != null && $SelfcareproviderConf < $array_programmate[$i + 23])
                                                                            echo '<select disabled id="allegatoIndagine_'.$array_programmate[$i+0].'" class="form-control">
                                                                                    <option selected value="'.$array_programmate[$i + 6] .'">RISERVATO</option>
                                                                                  </select>';
                                                                        else {
                                                                            echo ' <select id="allegatoIndagine_' . $array_programmate[$i + 0] . '" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value="" >Selezionare un file..</option>';
                                                                            for ($k = 0; $k < $n_all; $k += 5) {
                                                                                if ($role == "pz" || $SelfcareproviderConf >= $array_allegati[$k + 4]) {
                                                                                    if ($array_allegati[$k + 0] == $array_programmate[$i + 6])
                                                                                        echo '<option selected value="' . $array_allegati[$k + 0] . '">' . $array_allegati[$k + 1] . ' - ' . $array_allegati[$k + 2] . '</option>';
                                                                                    else
                                                                                        echo '<option value="' . $array_allegati[$k + 0] . '">' . $array_allegati[$k + 1] . ' - ' . $array_allegati[$k + 2] . '</option>';
                                                                                }
                                                                            }
                                                                            echo '
                                                                            <option value="">Nessuno</option>
                                                                         </select>';
                                                                        }
                                                                        echo '
                                                                    </div>
                                                                </div>
                                                                <div class=" col-lg-6 alert alert-info" role="alert" style="float: none; margin: 0 auto;" >
                                                                    <div style="text-align:center;">
                                                                        <strong>Attenzione:</strong> Per selezionare un file come referto o allegato è necessario caricarlo
                                                                        preventivamente nella sezione <strong>Files</strong>.
                                                                    </div>
                                                                </div></br>
                                                            </div>
					                                    </div>
			                                        </form>
			                                        <div style="text-align:center;">
				                                        <a href="" onclick="return false;" class=annulla id="'.$array_programmate[$i+0].'"><button class="btn btn-danger"><i class="icon icon-undo"></i> Annulla modifiche</button></a>
				                                        <a href="" onclick="return false;" class=conferma id="'.$array_programmate[$i+0].'"><button class="btn btn-success"><i class="icon icon-check"></i> Conferma modifiche</button></a>
			                                        </div>
			                                    </td>
	                                     </tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>	<!--paneldanger-->
                        </div>	<!--col lg12-->
                    </div>
                </div><br>
                <!-- TABELLA INDAGINI COMPLETATE -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">Indagini completate</div>
                            <div class=" panel-body">
                                <div class="table-responsive" >
                                    <table class="table" id="tableCompletate">
                                        <thead>
                                        <tr>
                                            <th>Indagine</th><th>Motivo</th><th>Care provider</th><th>Data</th>
                                            <th style="text-align:center">Referto</th><th style="text-align:center">Allegati</th><th style="text-align:center">Opzioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php   //popolamento tabella indagini completate
                                        for($i = 0; $i < $nCom; $i+=$offset){
                                            echo '<tr class="info" id="r'.$array_completate[$i+0].'">';
                                            echo '<td id="tipoCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+1] . '</td>';
                                            echo '<td id="motivoCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+2] . '</td>';
                                            echo '<td id="careCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+16] . '</td>';
                                            echo '<td id="dataCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+4] . '</td>';
                                            echo '<td  id="refertoCompletata'.$array_completate[$i+0].'" style="text-align:center"><div id="btn-group">';
                                            if ($array_completate[$i + 5] != null && ($role == "pz" || $SelfcareproviderConf >= $array_completate[$i + 20]))
                                                echo '<a href="'. $array_completate[$i + 19].'" target="_blank"><button class="btn btn-info"  type="button" id="refertoButton'. $array_completate[$i + 0] .'">
                                                <i class="icon-file-text"></i></button></a>';
                                            else
                                                echo '<button disabled class="btn btn-info"  type="button" id="refertoButton'. $array_completate[$i + 0] .'">
                                                <i class="icon-file-text"></i></button>';
                                            echo '</div></td>';
                                            echo '<td id="allegatoCompletata'.$array_completate[$i+0].'" style="text-align:center"><div id="btn-group">';
                                            if ($array_completate[$i + 6] != null && ($role == "pz" || $SelfcareproviderConf >= $array_completate[$i + 23]))
                                                echo '<a href="'. $array_completate[$i + 22].'" target="_blank"><button class="btn"  type="button" id="refertoButton'. $array_completate[$i + 0] .'">
                                                <i class="icon-file-text"></i></button></a>';
                                            else
                                                echo '<button disabled class="btn"  type="button" id="refertoButton'. $array_completate[$i + 0] .'">
                                                <i class="icon-file-text"></i></button>';
                                            echo '</div></td>';
                                            echo '<td style="text-align:center">
												<div id="btn-group">
												<button id='.$array_completate[$i+0].' class="modifica btn btn-success "><i class="icon-pencil icon-white"></i></button>
												<button id='.$array_completate[$i+0].' class="elimina btn btn-danger"><i class="icon-remove icon-white"></i></button>
												</div></td></tr>';
                                            echo '
                                            <tr id="riga'.$array_completate[$i+0].'" style="display:none">
		                                        <td colspan="7">';
                                            echo'
		                                            <form class="form-horizontal">
		                                                <div class="row">
		                                                    <div hidden class="col-lg-6 alert alert-danger" id="formAlert_'.$array_completate[$i+0].'" role="alert"  style="float: none; margin: 0 auto;">
                                                                <div style="text-align: center;">
                                                                    <i class="glyphicon glyphicon-exclamation-sign" ></i>
                                                                    <strong>Attenzione:</strong> Compilare correttamente i campi bordati in rosso.
                                                                </div>
                                                            </div></br>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Tipo indagine *</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="tipoIndagine'.$array_completate[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_completate[$i+1].'"/>
                                                                    </div>
                                                                </div>
					                                        </div>
					                                        <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Motivo *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="motivoIndagine_'.$array_completate[$i+0].'" class="form-control">
                                                                            <option selected value=\'\'>Altro..</option>';
                                                                                    for($k = 0; $k < $nDiagnoIns; $k +=4 ) {
                                                                                        if ($role == "pz" || $SelfcareproviderConf >= $array_diagnosi[$k + 3]) {
                                                                                            if ($array_diagnosi[$k + 0] == $array_completate[$i + 15])
                                                                                                echo '<option selected value=\'' . $array_diagnosi[$k + 0] . '\'>' . $array_diagnosi[$k + 1] . ' - ' . $array_diagnosi[$k + 2] . '</option>';
                                                                                            else
                                                                                                echo '<option value=\'' . $array_diagnosi[$k + 0] . '\'>' . $array_diagnosi[$k + 1] . ' - ' . $array_diagnosi[$k + 2] . '</option>';
                                                                                        }
                                                                                    }
                                                                                echo '
                                                                        </select>
                                                                        <input id="motivoAltro_'.$array_completate[$i+0].'" type="text" placeholder="Compilare motivazione.."  class="form-control" value="'. $array_completate[$i+2] .'"/>
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Care provider *</label>
                                                                    <div class="col-lg-4">';
                                                                        if ($role == "cp")
                                                                            echo '<select disabled id="careproviderIndagine_'.$array_completate[$i+0].'" class="form-control">';
                                                                        else
                                                                            echo '<select id="careproviderIndagine_'.$array_completate[$i+0].'" class="form-control">';
                                                                        echo '<option selected value=\'\'>Altro..</option>';
                                                                        for($k = 0; $k < $n_v; $k +=3 ) {
                                                                            if ($array_careprovider[$k+0] == $array_completate[$i+7])
                                                                                echo '<option selected value=\'' . $array_careprovider[$k + 0] . '\'>' . $array_careprovider[$k+1] . ' ' . $array_careprovider[$k+2] . '</option>';
                                                                            else
                                                                                echo '<option value=\'' . $array_careprovider[$k + 0] . '\'>' .$array_careprovider[$k+1] . ' ' . $array_careprovider[$k+2] . '</option>';
                                                                        }
                                                                        echo '</select>';
                                                                        if ($role == "cp")
                                                                            echo '<input disabled id="careproviderAltro_'.$array_completate[$i+0].'" type="text" placeholder="Inserire careprovider.."  class="form-control" value="'. $array_completate[$i+16] .'"/>';
                                                                        else
                                                                            echo '<input id="careproviderAltro_'.$array_completate[$i+0].'" type="text" placeholder="Inserire careprovider.."  class="form-control" value="'. $array_completate[$i+16] .'"/>';
                                                                        echo '
                                                                    </div>
                                                                 </div>
                                                             </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Stato *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="statoIndagine_'.$array_completate[$i+0].'" class="form-control">
                                                                            <option selected value="2">Completata</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divCentro_'.$array_completate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Centro *</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="centroIndagine'.$array_completate[$i+0].'" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value=\'\'>Selezionare un centro..</option>';
                                            for($k = 0; $k < $nCentriIns; $k +=10 ){
                                                if( $array_centri[$k+0] == $array_completate[$i+11])
                                                    echo '<option selected value="'.$array_centri[$k+0] .'">' .$array_centri[$k+1] .',  '.$array_centri[$k+3].'</option>';
                                                else
                                                    echo '<option value="'.$array_centri[$k+0] .'">' .$array_centri[$k+1] .',  '.$array_centri[$k+3].'</option>';
                                            }
                                            echo '</select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                             <div class="col-lg-12" id="divData_'.$array_completate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Data *</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="data'.$array_completate[$i+0].'" type="text" placeholder="Selezionare una data.." class="form-control"
                                                                            value ="'.$array_completate[$i+4].'"/>
                                                                    </div>
                                                                    <script>
                                                                        $j(document).ready(function() {
                                                                            $j(\'#data'.$array_completate[$i+0].'\').datetimepicker({
                                                                                locale:\'it\',
                                                                                sideBySide:true
                                                                            });
                                                                        });
                                                                    </script>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divReferto_'.$array_completate[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Referto</label>
                                                                    <div class="col-lg-4">';
                                                                        if($role != "pz" && $array_completate[$i + 5] != null && $SelfcareproviderConf < $array_completate[$i + 20])
                                                                            echo '<select disabled id="refertoIndagine_'.$array_completate[$i+0].'" class="form-control">
                                                                                    <option selected value="'.$array_completate[$i + 5] .'">RISERVATO</option>
                                                                                  </select>';
                                                                        else{
                                                                        echo '<select id="refertoIndagine_'.$array_completate[$i+0].'" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value="" >Selezionare un file..</option>';
                                                                                    for($k = 0; $k < $n_ref; $k +=5 ) {
                                                                                        if($role == "pz" || $SelfcareproviderConf >= $array_referti[$k+4] ){
                                                                                            if ($array_referti[$k + 0] == $array_completate[$i + 5])
                                                                                                echo '<option selected value="'.$array_referti[$k+0] .'">' .$array_referti[$k+1] .' - ' .$array_referti[$k+2].'</option>';
                                                                                            else
                                                                                                echo '<option value="'.$array_referti[$k+0] .'">' .$array_referti[$k+1] .' - ' .$array_referti[$k+2].'</option>';
                                                                                        }
                                                                                    }
                                                                                echo '
                                                                            <option value="">Nessuno</option>
                                                                            </select>';
                                                                        }
                                                                        echo '
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divAllegato_'.$array_completate[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Allegato</label>
                                                                    <div class="col-lg-4">';
                                                                        if($role != "pz" && $array_completate[$i + 6] != null && $SelfcareproviderConf < $array_completate[$i + 23])
                                                                            echo '<select disabled id="allegatoIndagine_'.$array_completate[$i+0].'" class="form-control">
                                                                                    <option selected value="'.$array_completate[$i + 6] .'">RISERVATO</option>
                                                                                  </select>';
                                                                        else {
                                                                            echo ' <select id="allegatoIndagine_' . $array_completate[$i + 0] . '" class="form-control">
                                                                            <option selected hidden style=\'display: none\' value="" >Selezionare un file..</option>';
                                                                            for ($k = 0; $k < $n_all; $k += 5) {
                                                                                if ($role == "pz" || $SelfcareproviderConf >= $array_allegati[$k + 4]) {
                                                                                    if ($array_allegati[$k + 0] == $array_completate[$i + 6])
                                                                                        echo '<option selected value="' . $array_allegati[$k + 0] . '">' . $array_allegati[$k + 1] . ' - ' . $array_allegati[$k + 2] . '</option>';
                                                                                    else
                                                                                        echo '<option value="' . $array_allegati[$k + 0] . '">' . $array_allegati[$k + 1] . ' - ' . $array_allegati[$k + 2] . '</option>';
                                                                                }
                                                                            }
                                                                            echo '
                                                                            <option value="">Nessuno</option>
                                                                         </select>';
                                                                        }
                                                                        echo '
                                                                    </div>
                                                                </div>
                                                                <div class=" col-lg-6 alert alert-info" role="alert" style="float: none; margin: 0 auto;" >
                                                                    <div style="text-align:center;">
                                                                        <strong>Attenzione:</strong> Per selezionare un file come referto o allegato è necessario caricarlo
                                                                        preventivamente nella sezione <strong>Files</strong>.
                                                                    </div>
                                                                </div></br>
                                                            </div>
					                                    </div>
			                                        </form>
			                                        <div style="text-align:center;">
				                                        <a href="" onclick="return false;" class=annulla id="'.$array_completate[$i+0].'"><button class="btn btn-danger"><i class="icon icon-undo"></i> Annulla modifiche</button></a>
				                                        <a href="" onclick="return false;" class=conferma id="'.$array_completate[$i+0].'"><button class="btn btn-success"><i class="icon icon-check"></i> Conferma modifiche</button></a>
			                                        </div>
			                                    </td>
	                                     </tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>	<!--paneldanger-->
                        </div>	<!--col lg12-->
                    </div><br>
        </div>
                </div>
                <!-- FINE COLLAPSE DIARIO INDAGINI DIAGNOSTICHE -->

                <!-- COLLAPSE CENTRI INDAGINI DIAGNOSTICHE -->
                <div id="collapse2" class="panel-collapse collapse"><hr/>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-warning">
                                    <div class="panel-heading">Studi Specialistici</div>
                                    <div class=" panel-body">
                                        <div class="table-responsive" >
                                            <table class="table" id="tableStudiSpecialistici">
                                                <thead>
                                                <tr>
                                                    <th>Studio</th><th>Sede</th><th>Contatti</th><th>Mail</th><th style="text-align:center">Messaggio FSEM</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php   //popolamento tabella studi specialistici
                                                $link = $this->get_var('link_mostratutti');
                                                for($i = 0; $i < $nCentriIns; $i+=10) {
                                                    if ($array_centri[$i + 4] == 0){
                                                        echo '<tr class="info" id="studioSpecialistico' . $array_centri[$i + 0] . '">';
                                                        echo '<td id="nomeStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 1] . '</td>';
                                                        echo '<td id="sedeStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 2] . '<br>' . $array_centri[$i + 3] . '</td>';
                                                        echo '<td id="contattiStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 9] . '</td>';
                                                        echo '<td><a href="mailto:'. $array_centri[$i + 5].'">
                                                        <button class="btn btn-warning"  type="button" id="mailStudioSpecialistico'. $array_centri[$i + 0] .'">
                                                        <i class="icon-envelope"></i></button> '. $array_centri[$i + 5].'</a></td>';
                                                        echo '<td ><a class="a-messaggio" id="'. $array_centri[$i + 0] .'" data-toggle="modal" data-target="#messageModal" href="' .$link .'" >
                                                        <button class="btn-messaggio btn"  type="button" id="'. $array_centri[$i + 0] .'">
                                                        <i class="icon_custom-chat"></i></button> '. $array_centri[$i + 7] .' '. $array_centri[$i + 8] .'</a>
                                                        <div id="careproviderStudio'. $array_centri[$i + 0] .'" data-nome="'. $array_centri[$i + 7] .' '. $array_centri[$i + 8] .'"></div></td>
                                                        </tr>';
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>	<!--paneldanger-->
                            </div>	<!--col lg12-->
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-danger">
                                    <div class="panel-heading">Studi Radiologici</div>
                                    <div class=" panel-body">
                                        <div class="table-responsive" >
                                            <table class="table" id="tableStudiRadiologici">
                                                <thead>
                                                <tr>
                                                    <th>Studio</th><th>Sede</th><th>Contatti</th><th>Mail</th><th style="text-align:center">Messaggio FSEM</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php   //popolamento tabella studi radiologici
                                                $link = $this->get_var('link_mostratutti');
                                                for($i = 0; $i < $nCentriIns; $i+=10) {
                                                    if ($array_centri[$i + 4] == 1){
                                                        echo '<tr class="info" id="studioSpecialistico' . $array_centri[$i + 0] . '">';
                                                        echo '<td id="nomeStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 1] . '</td>';
                                                        echo '<td id="sedeStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 2] . '<br>' . $array_centri[$i + 3] . '</td>';
                                                        echo '<td id="contattiStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 9] . '</td>';
                                                        echo '<td><a href="mailto:'. $array_centri[$i + 5].'">
                                                        <button class="btn btn-warning"  type="button" id="mailStudioSpecialistico'. $array_centri[$i + 0] .'">
                                                        <i class="icon-envelope"></i></button> '. $array_centri[$i + 5].'</a></td>';
                                                        echo '<td ><a class="a-messaggio" id="'. $array_centri[$i + 0] .'" data-toggle="modal" data-target="#messageModal" href="' .$link .'" >
                                                        <button class="btn-messaggio btn"  type="button" id="'. $array_centri[$i + 0] .'">
                                                        <i class="icon_custom-chat"></i></button> '. $array_centri[$i + 7] .' '. $array_centri[$i + 8] .'</a>
                                                        <div id="careproviderStudio'. $array_centri[$i + 0] .'" data-nome="'. $array_centri[$i + 7] .' '. $array_centri[$i + 8] .'"></div></td>
                                                        </tr>';
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>	<!--paneldanger-->
                            </div>	<!--col lg12-->
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-info">
                                    <div class="panel-heading">Laboratori Analisi</div>
                                    <div class=" panel-body">
                                        <div class="table-responsive" >
                                            <table class="table" id="tableLaboratoriAnalisi">
                                                <thead>
                                                <tr>
                                                    <th>Studio</th><th>Sede</th><th>Contatti</th><th>Mail</th><th style="text-align:center">Messaggio FSEM</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php   //popolamento tabella laboratori analisi
                                                $link = $this->get_var('link_mostratutti');
                                                for($i = 0; $i < $nCentriIns; $i+=10) {
                                                    if ($array_centri[$i + 4] == 2){
                                                        echo '<tr class="info" id="studioSpecialistico' . $array_centri[$i + 0] . '">';
                                                        echo '<td id="nomeStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 1] . '</td>';
                                                        echo '<td id="sedeStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 2] . '<br>' . $array_centri[$i + 3] . '</td>';
                                                        echo '<td id="contattiStudioSpecialistico' . $array_centri[$i + 0] . '">' . $array_centri[$i + 9] . '</td>';
                                                        echo '<td><a href="mailto:'. $array_centri[$i + 5].'">
                                                        <button class="btn btn-warning"  type="button" id="mailStudioSpecialistico'. $array_centri[$i + 0] .'">
                                                        <i class="icon-envelope"></i></button> '. $array_centri[$i + 5].'</a></td>';
                                                        echo '<td ><a class="a-messaggio" id="'. $array_centri[$i + 0] .'" data-toggle="modal" data-target="#messageModal" href="' .$link .'" >
                                                        <button class="btn-messaggio btn"  type="button" id="'. $array_centri[$i + 0] .'">
                                                        <i class="icon_custom-chat"></i></button> '. $array_centri[$i + 7] .' '. $array_centri[$i + 8] .'</a>
                                                        <div id="careproviderStudio'. $array_centri[$i + 0] .'" data-nome="'. $array_centri[$i + 7] .' '. $array_centri[$i + 8] .'"></div></td>
                                                        </tr>';
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>	<!--paneldanger-->
                            </div>	<!--col lg12-->
                        </div>
                    </div><!--row-->
                </div>
                <!-- FINE COLLAPSE CENTRI INDAGINI DIAGNOSTICHE -->
            </div>
        </div>
        <!-- FINE ACCORDION -->


    </div>
</div>
<!--END PAGE CONTENT -->
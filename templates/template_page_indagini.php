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


        if ($_SERVER['REQUEST_METHOD'] == 'POST'){


            $idDiagnosi = $_POST["idDiagnosi"];
            echo 'Provieni dalla pagina diagnosi:'.$idDiagnosi;
            $idPaziente    = getInfo('idPaziente','diagnosi','id='.$idDiagnosi);
            $nomePatologia = getInfo('patologia','diagnosi','id='.$idDiagnosi);
            $dataDiagnosi  = getInfo('DATE(datains)', 'diagnosi', 'id='.$idDiagnosi);


            $indaginiData     = getArray('data','indagini','diagnosi_id='.$idDiagnosi);
            $indaginiTipo     = getArray('tipoIndagine','indagini','diagnosi_id='.$idDiagnosi);
            $indaginiReferto  = getArray('referto','indagini','diagnosi_id='.$idDiagnosi);
            $indaginiAllegato = getArray('allegato','indagini','diagnosi_id='.$idDiagnosi);
            $indaginiNum      = count($indaginiData);

        }else{
            echo 'Hai cliccato indagini dal menù principale';
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

        // ------ DATA FROM diagnosi.php --------
        $stato_richiesta = "richiesta";
        $stato_programmata = "programmata";
        $stato_completata = "conclusa";
        $array_richieste = array();      //array indagini richieste
        $array_programmate = array();   //array indagini programmate
        $array_completate = array();    //array indagini completate
        $n_r = 0;               //numero indagini richieste
        $n_p = 0;             //numero indagini programmate
        $n_c = 0;              //numero completate
        global $offset; $offset = 15;
        global $n_indagini;             //numero totale di indagini
        $n_indagini = $this->get_var('indaginiNum');

        for ($i = 0; $i < $n_indagini; $i++){          //per ogni indagine...
            $stato = $this->get_var('ind.stato.'.$i);  //verifica lo stato
             switch ($stato){
                 case $stato_richiesta:
                     $array_richieste[$n_r + 0] = $this->get_var('ind.id.'.$i);         //id indagine
                     $array_richieste[$n_r + 1] = $this->get_var('ind.tipo.'.$i);       //tipo indagine
                     $array_richieste[$n_r + 2] = $this->get_var('ind.motivo.'.$i);     //motivazione
                     $array_richieste[$n_r + 3] = $this->get_var('ind.stato.'.$i);      //stato indagine
                     $array_richieste[$n_r + 4] = $this->get_var('ind.data.'.$i);       //data indagine
                     $array_richieste[$n_r + 5] = $this->get_var('ind.referto.'.$i);       
                     $array_richieste[$n_r + 6] = $this->get_var('ind.allegato.'.$i);
                     $array_richieste[$n_r + 7] = $this->get_var('ind.cpId.'.$i);       //id careprovider
                     $array_richieste[$n_r + 8] = $this->get_var('ind.cpNome.'.$i);     //nome careprovider
                     $array_richieste[$n_r + 9] = $this->get_var('ind.cpCognome.'.$i);  //cognome careprovider
                     $array_richieste[$n_r + 10] = $this->get_var('ind.cpRep.'.$i);     //ruolo careprovider
                     $array_richieste[$n_r + 11] = $this->get_var('ind.centroId.'.$i);  //id centro diagnostico
                     $array_richieste[$n_r + 12] = $this->get_var('ind.centroNome.'.$i);//nome centro diagnostico
                     $array_richieste[$n_r + 13] = $this->get_var('ind.centroVia.'.$i); // via centro diagnostico
                     $array_richieste[$n_r + 14] = $this->get_var('ind.centroCitta.'.$i);   //citta centro diagnostico
                     $n_r = $n_r + $offset;
                     break;
                 case $stato_programmata:
                     $array_programmate[$n_p + 0] = $this->get_var('ind.id.'.$i);         //id indagine
                     $array_programmate[$n_p + 1] = $this->get_var('ind.tipo.'.$i);       //tipo indagine
                     $array_programmate[$n_p + 2] = $this->get_var('ind.motivo.'.$i);     //motivazione
                     $array_programmate[$n_p + 3] = $this->get_var('ind.stato.'.$i);      //stato indagine
                     $array_programmate[$n_p + 4] = $this->get_var('ind.data.'.$i);       //data indagine
                     $array_programmate[$n_p + 5] = $this->get_var('ind.referto.'.$i);
                     $array_programmate[$n_p + 6] = $this->get_var('ind.allegato.'.$i);
                     $array_programmate[$n_p + 7] = $this->get_var('ind.cpId.'.$i);       //id careprovider
                     $array_programmate[$n_p + 8] = $this->get_var('ind.cpNome.'.$i);     //nome careprovider
                     $array_programmate[$n_p + 9] = $this->get_var('ind.cpCognome.'.$i);  //cognome careprovider
                     $array_programmate[$n_p + 10] = $this->get_var('ind.cpRep.'.$i);     //ruolo careprovider
                     $array_programmate[$n_p + 11] = $this->get_var('ind.centroId.'.$i);  //id centro diagnostico
                     $array_programmate[$n_p + 12] = $this->get_var('ind.centroNome.'.$i);//nome centro diagnostico
                     $array_programmate[$n_p + 13] = $this->get_var('ind.centroVia.'.$i); // via centro diagnostico
                     $array_programmate[$n_p + 14] = $this->get_var('ind.centroCitta.'.$i);   //citta centro diagnostico
                     $n_p = $n_p + $offset;
                     break;
                 case $stato_completata:
                     $array_completate[$n_c + 0] = $this->get_var('ind.id.'.$i);         //id indagine
                     $array_completate[$n_c + 1] = $this->get_var('ind.tipo.'.$i);       //tipo indagine
                     $array_completate[$n_c + 2] = $this->get_var('ind.motivo.'.$i);     //motivazione
                     $array_completate[$n_c + 3] = $this->get_var('ind.stato.'.$i);      //stato indagine
                     $array_completate[$n_c + 4] = $this->get_var('ind.data.'.$i);       //data indagine
                     $array_completate[$n_c + 5] = $this->get_var('ind.referto.'.$i);
                     $array_completate[$n_c + 6] = $this->get_var('ind.allegato.'.$i);
                     $array_completate[$n_c + 7] = $this->get_var('ind.cpId.'.$i);       //id careprovider
                     $array_completate[$n_c + 8] = $this->get_var('ind.cpNome.'.$i);     //nome careprovider
                     $array_completate[$n_c + 9] = $this->get_var('ind.cpCognome.'.$i);  //cognome careprovider
                     $array_completate[$n_c + 10] = $this->get_var('ind.cpRep.'.$i);     //ruolo careprovider
                     $array_completate[$n_c + 11] = $this->get_var('ind.centroId.'.$i);  //id centro diagnostico
                     $array_completate[$n_c + 12] = $this->get_var('ind.centroNome.'.$i);//nome centro diagnostico
                     $array_completate[$n_c + 13] = $this->get_var('ind.centroVia.'.$i); // via centro diagnostico
                     $array_completate[$n_c + 14] = $this->get_var('ind.centroCitta.'.$i);   //citta centro diagnostico
                     $n_c = $n_c + $offset;
                     break;
             }
         }

        $array_centri = array();
        $n_s = 0;
        global $n_centri;             //numero totale di centri
        $n_centri = $this->get_var('centriNum');
        for ($i = 0; $i < $n_centri; $i++) {          //per ogni centro...
            $array_centri[$n_s + 0] = $this->get_var('centro.id.'.$i);
            $array_centri[$n_s + 1] = $this->get_var('centro.nome.'.$i);
            $array_centri[$n_s + 2] = $this->get_var('centro.via.'.$i);
            $array_centri[$n_s + 3] = $this->get_var('centro.citta.'.$i);
            $array_centri[$n_s + 4] = $this->get_var('centro.tipo.'.$i);
            $array_centri[$n_s + 5] = $this->get_var('centro.mail.'.$i);
            $array_centri[$n_s + 6] = $this->get_var('centro.responsabileId.'.$i);
            $array_centri[$n_s + 7] = $this->get_var('centro.responsabileNome.'.$i);
            $array_centri[$n_s + 8] = $this->get_var('centro.responsabileCognome.'.$i);
            $n_s = $n_s + 9;
        }


        $array_diagnosi = array();
        $n_z = 0;
        global $n_diagnosi;
        $n_diagnosi = $this->get_var('diagnosiNum');
        for ($i = 0; $i < $n_diagnosi; $i++) {
            $array_diagnosi[$n_z + 0] = $this->get_var('diagnosi.id.'.$i);
            $array_diagnosi[$n_z + 1] = $this->get_var('diagnosi.data.'.$i);
            $array_diagnosi[$n_z + 2] = $this->get_var('diagnosi.patologia.'.$i);
            $array_diagnosi[$n_z + 3] = $this->get_var('diagnosi.conf.'.$i);
            $n_z = $n_z + 4;
        }

        $mioCpNome = $this->get_var('mioCpNome');
        $mioCpCognome = $this->get_var('mioCpCognome');

        // --------------------------------------
        ?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="formscripts/jquery.js"></script>
        <script src="formscripts/jquery-ui.js"></script>
        <script src="formscripts/indagini.js"></script>

        <h2>Indagini diagnostiche</h2><hr>
        <p>In questa pagina è possibile visualizzare tutti gli esami che un paziente deve effettuare o ha già effettuato e
        l'elenco di tutti gli studi e laboratori dove è possibile effettuare un determinato esame.</p>
        <hr/>
       <!-- ACCORDION -->
        <div class="accordion ac" id="accordion">
            <div class="accordion-group">
                <div class="accordion-heading centered">
                    <h3>
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                            Diario indagini diagnostiche
                            <span ><i  class="icon-angle-down"></i></span>
                        </a>
                    </h3>
                </div>
                <div id="collapse1" class="acordion-body collapse">
                    <div class="accordion-inner">
        <div class="row">
            <div class="col-lg-12">
                <hr>

                <div class="row">
                    <div class="col-lg-12" >
                        <div class="btn-group">

                            <?php
                            //if($_SERVER['REQUEST_METHOD'] == 'POST'){
                                echo '<button class="btn btn-primary" id="nuovoFile"><i class="icon-file-text-alt"></i> Nuova indagine</button>';
                            //}else{
                             //   echo '<button disabled class="btn btn-primary" id="nuovoFile"><i class="icon-file-text-alt"></i> Nuova indagine</button>';
                           // }
                            ?>
                            <button class="btn btn-primary" id="concludi"><i class="icon-ok-sign"></i> Concludi indagine</button>
                            <button class="btn btn-primary" id="annulla"><i class="icon-trash"></i> Annulla indagine</button>

                        </div></div></div><br>

                <form style="display:none;" id="formIndagini" action="formscripts/indagini.php" method="POST" class="form-horizontal" >
                    <div class="tab-content">
                        <div class="row"> <!-- Hidden row -->
                            <?php
                            $cps=$this->get_var('suggestCps');
                            $script = '<script>
							   $(document).ready(function(){
								var cps ='.$cps." var cpSuggest = new Bloodhound({
								datumTokenizer: Bloodhound.tokenizers.whitespace,
								queryTokenizer: Bloodhound.tokenizers.whitespace,
								local: cps});";
                            for($i=0; $i<$n_indagini; $i++){
                                $script=$script."$('#nomeCpD".$this->get_var('ind.id.'.$i)."').typeahead({
                                hint: true,highlight: true,minLength: 1},{name: 'cps',source: cpSuggest,
                                limit: 10});";
                            }
                            $script=$script."$('#nomeCpD').typeahead({ hint: true,highlight: true,
                            minLength: 1},{name: 'cps',source: cpSuggest,limit: 10});});</script>";
                            if ($cp_id == NULL){echo $script;}
                            ?>
                            <div style="display:none;" >
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
                                            if ($cp_id != NULL) {echo 'value="'.$this -> get_var('idUtenteCp').'" ';}
                                            else {echo 'value="-1"';}
                                            ?>
                                        />
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">ID Diagnosi:</label>
                                    <div class="col-lg-4">
                                        <input id="idDiagnosi" type="text"  readonly class="form-control" value="<?php /*echo $idDiagnosi; */?>"/>
                                    </div>
                                </div>
                            </div> -->
                        </div> <!-- End hidden row -->




                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Tipo indagine:</label>
                                    <div class="col-lg-4">
                                        <input id="tipoIndagine" type="text"  class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Motivo:</label>
                                    <div class="col-lg-4">
                                        <input id="motivoIndagine" type="text"  class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Care provider:</label>
                                    <div class="col-lg-4">
                                        <input id="nomeCpD" <?php if ($cp_id != NULL){echo 'value="'.$mioCpNome.' '.$mioCpCognome.'" readonly ';}?> type="text" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Stato:</label>
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
                                    <label class="control-label col-lg-4">Centro:</label>
                                    <div class="col-lg-4">
                                        <select id="centroIndagine_new" class="form-control">
                                            <option selected disabled hidden style='display: none' value=''>Selezionare un centro..</option>
                                            <?php
                                            for($i = 0; $i < $n_s; $i +=9 ){
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
                                    <label class="control-label col-lg-4">Data:</label>
                                    <div class="col-lg-4">
                                        <input id="data" type="date" placeholder="aaaa-mm-gg" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="divReferto_new" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Referto:</label>
                                    <div class="col-lg-4" >
                                        <input id="referto" type="text"  class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="divAllegato_new" style="display:none;">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Allegato:</label>
                                    <div class="col-lg-4">
                                        <input id="allegato" type="text"  class="form-control"y/>
                                    </div>
                                </div>
                            </div>
                            <!-- <div id="da_eliminare" style="display:none;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Care provider:</label>
                                        <div class="col-lg-4">
                                            <?php/*
                                            $sp = ' ';
                                            if($cp_id != NULL){ // Se sono un care provider
                                                // Il mio nome è pre-inserito e non può essere cambiato
                                                echo '<select id="cpD" class="form-control"><option selected value="'.$mioCpId.'">'.$mioCpNome.$sp.$mioCpCognome.'</option></select>';
                                            }else{
                                                echo '<select id="cpD" class="form-control">';
                                                for($i=0; $i<$nCps; $i++){
                                                    echo '<option value='.$myCpId[$i].'>'.$myCpNome[$i].$sp.$myCpCognome[$i].'</option>';
                                                }
                                                echo '<option style="font-style:italic;"value=-1>Inserisci manualmente...</option>';
                                                echo '</select>';
                                            }
                                            */?>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Nome cp:</label>
                                        <div class="col-lg-4">
                                            <input id="nomeCp" type="text" class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Cognome cp:</label>
                                        <div class="col-lg-4">
                                            <input id="cognomeCp" type="text" class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                            </div> -->


                        </div>
                    </div>
                </form><br>

                <!---------------------INDAGINI RICHIESTE------------------------------------>
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
                                        for($i = 0; $i < $n_r; $i+=$offset){
                                            echo '<tr class="info" id="r'.$array_richieste[$i+0].'">';
                                            echo '<td id="tipoRichiesta'.$array_richieste[$i+0].'">' . $array_richieste[$i+1] . '</td>';
                                            echo '<td id="motivoRichiesta'.$array_richieste[$i+0].'">' . $array_richieste[$i+2] . '</td>';
                                            echo '<td id="careRichiesta'.$array_richieste[$i+0].'">' . $array_richieste[$i+8]. ' ' .$array_richieste[$i+9].'</td>';
                                            echo '<td style="text-align:center"><div id="btn-group">
										            <button id='.$array_richieste[$i+0].' class="modifica btn btn-success "><i class="icon-pencil icon-white"></i></button>
												    <button id='.$array_richieste[$i+0].' class="elimina btn btn-danger"><i class="icon-remove icon-white"></i></button>
												 </div></td></tr>';
                                            echo '
                                            <tr id="riga'.$array_richieste[$i+0].'" style="display:none">
		                                        <td colspan="5">';
                                            echo'
		                                            <form class="form-horizontal">
		                                                <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Tipo indagine:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="tipoIndagine'.$array_richieste[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_richieste[$i+1].'"/>
                                                                    </div>
                                                                </div>
					                                        </div>
					                                        <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Motivo:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="motivoIndagine'.$array_richieste[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_richieste[$i+2].'"/>
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Care provider:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="nomeCpD'.$array_richieste[$i+0].'" type="text" class="form-control"
                                                                            value ="'. $array_richieste[$i+8]. ' ' .$array_richieste[$i+9].'"';
                                                                            if ($cp_id != NULL) echo ' readonly ';
                                                                        echo '/>
                                                                    </div>
                                                                 </div>
                                                             </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Stato:</label>
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
                                                                <div class="form-group"><label class="control-label col-lg-4">Centro:</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="centroIndagine'.$array_richieste[$i+0].'" class="form-control">';
                                                                        for($k = 0; $k < $n_s; $k +=9 ){
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
                                                                <div class="form-group"><label class="control-label col-lg-4">Data:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="data'.$array_richieste[$i+0].'" type="date" placeholder="aaaa-mm-gg" class="form-control"
                                                                        value ="'.$array_richieste[$i+4].'"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divReferto_'.$array_richieste[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Referto:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="referto'.$array_richieste[$i+0].'" type="text"  class="form-control" value ="'.$array_richieste[$i+5].'"/>
                                                                      
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divAllegato_'.$array_richieste[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Allegato:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="allegato'.$array_richieste[$i+0].'" type="text"  class="form-control" value ="'.$array_richieste[$i+6].'"/>
                                                                  
                                                                    </div>
                                                                </div>
                                                            </div>
                                
					                                    </div>
			                                        </form>
			                                        <div style="text-align:right;">
				                                        <a href="" onclick="return false;" class=annulla id="'.$array_richieste[$i+0].'">[Annulla]</a>
				                                        <a href="" onclick="return false;" class=conferma id="'.$array_richieste[$i+0].'">[Conferma]</a>
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
                <!---------------------INDAGINI PROGRAMMATE------------------------------------>
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
                                        for($i = 0; $i < $n_p; $i+=$offset){
                                            echo '<tr class="info" id="r'.$array_programmate[$i+0].'">';
                                            echo '<td id="tipoProgrammata'.$array_programmate[$i+0].'">' . $array_programmate[$i+1] . '</td>';
                                            echo '<td id="motivoProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+2] . '</td>';
                                            echo '<td id="careProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+8] . ' ' . $array_programmate[$i+9] . '</td>';
                                            echo '<td id="dataProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+4] . '</td>';
                                            echo '<td id="centroProgrammata'.$array_programmate[$i+0].'">'. $array_programmate[$i+12] . '<br>' . $array_programmate[$i+13] . ' - ' .
                                                $array_programmate[$i+14] . '</td>';
                                            echo '<td style="text-align:center">
												<div id="btn-group">
												<button id='.$array_programmate[$i+0].' class="modifica btn btn-success "><i class="icon-pencil icon-white"></i></button>
												<button id='.$array_programmate[$i+0].' class="elimina btn btn-danger"><i class="icon-remove icon-white"></i></button>
												</div></td></tr>';
                                            echo '
                                            <tr id="riga'.$array_programmate[$i+0].'" style="display:none">
		                                        <td colspan="5">';
                                            echo'
		                                            <form class="form-horizontal">
		                                                <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Tipo indagine:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="tipoIndagine'.$array_programmate[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_programmate[$i+1].'"/>
                                                                    </div>
                                                                </div>
					                                        </div>
					                                        <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Motivo:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="motivoIndagine'.$array_programmate[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_programmate[$i+2].'"/>
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Care provider:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="nomeCpD'.$array_programmate[$i+0].'" type="text" class="form-control"
                                                                            value ="'. $array_programmate[$i+8]. ' ' .$array_programmate[$i+9].'"';
                                            if ($cp_id != NULL) echo ' readonly ';
                                            echo '/>
                                                                    </div>
                                                                 </div>
                                                             </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Stato:</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="statoIndagine_'.$array_programmate[$i+0].'" class="form-control">
                                                                            <option value="0">Richiesta</option>
                                                                            <option selected value="1">Programmata</option>
                                                                            <option value="2">Completata</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divCentro_'.$array_programmate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Centro:</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="centroIndagine'.$array_programmate[$i+0].'" class="form-control">';
                                            for($k = 0; $k < $n_s; $k +=9 ){
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
                                                                <div class="form-group"><label class="control-label col-lg-4">Data:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="data'.$array_programmate[$i+0].'" type="date" placeholder="aaaa-mm-gg" class="form-control" 
                                                                        value ="'.$array_programmate[$i+4].'"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divReferto_'.$array_programmate[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Referto:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="referto'.$array_programmate[$i+0].'" type="text"  class="form-control" value ="'.$array_programmate[$i+5].'"/>        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divAllegato_'.$array_programmate[$i+0].'" style="display:none;">
                                                                <div class="form-group"><label class="control-label col-lg-4">Allegato:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="allegato'.$array_programmate[$i+0].'" type="text"  class="form-control" value ="'.$array_programmate[$i+6].'"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                
					                                    </div>
			                                        </form>
			                                        <div style="text-align:right;">
				                                        <a href="" onclick="return false;" class=annulla id="'.$array_programmate[$i+0].'">[Annulla]</a>
				                                        <a href="" onclick="return false;" class=conferma id="'.$array_programmate[$i+0].'">[Conferma]</a>
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
                <!---------------------INDAGINI COMPLETATE------------------------------------>
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
                                            <th>Referto</th><th>Allegati</th><th style="text-align:center">Opzioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php   //popolamento tabella indagini completate
                                        for($i = 0; $i < $n_c; $i+=$offset){
                                            echo '<tr class="info" id="r'.$array_completate[$i+0].'">';
                                            echo '<td id="tipoCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+1] . '</td>';
                                            echo '<td id="motivoCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+2] . '</td>';
                                            echo '<td id="careCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+8] . ' ' . $array_completate[$i+9] . '</td>';
                                            echo '<td id="dataCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+4] . '</td>';
                                            echo '<td id="refertoCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+5] . '</td>';
                                            echo '<td id="allegatoCompletata'.$array_completate[$i+0].'">' . $array_completate[$i+6] . '</td>';
                                            echo '<td style="text-align:center">
												<div id="btn-group">
												<button id='.$array_completate[$i+0].' class="modifica btn btn-success "><i class="icon-pencil icon-white"></i></button>
												<button id='.$array_completate[$i+0].' class="elimina btn btn-danger"><i class="icon-remove icon-white"></i></button>
												</div></td></tr>';
                                            echo '
                                            <tr id="riga'.$array_completate[$i+0].'" style="display:none">
		                                        <td colspan="5">';
                                            echo'
		                                            <form class="form-horizontal">
		                                                <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Tipo indagine:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="tipoIndagine'.$array_completate[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_completate[$i+1].'"/>
                                                                    </div>
                                                                </div>
					                                        </div>
					                                        <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Motivo:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="motivoIndagine'.$array_completate[$i+0].'" type="text"  class="form-control"
                                                                            value ="'.$array_completate[$i+2].'"/>
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Care provider:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="nomeCpD'.$array_completate[$i+0].'" type="text" class="form-control"
                                                                            value ="'. $array_completate[$i+8]. ' ' .$array_completate[$i+9].'"';
                                            if ($cp_id != NULL) echo ' readonly ';
                                            echo '/>
                                                                    </div>
                                                                 </div>
                                                             </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group"><label class="control-label col-lg-4">Stato:</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="statoIndagine_'.$array_completate[$i+0].'" class="form-control">
                                                                            <option value="0">Richiesta</option>
                                                                            <option value="1">Programmata</option>
                                                                            <option selected value="2">Completata</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divCentro_'.$array_completate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Centro:</label>
                                                                    <div class="col-lg-4">
                                                                        <select id="centroIndagine'.$array_completate[$i+0].'" class="form-control">';
                                            for($k = 0; $k < $n_s; $k +=9 ){
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
                                                                <div class="form-group"><label class="control-label col-lg-4">Data:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="data'.$array_completate[$i+0].'" type="date" placeholder="aaaa-mm-gg" class="form-control"
                                                                            value ="'.$array_completate[$i+4].'"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divReferto_'.$array_completate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Referto:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="referto'.$array_completate[$i+0].'" type="text"  class="form-control" value ="'.$array_completate[$i+5].'"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12" id="divAllegato_'.$array_completate[$i+0].'">
                                                                <div class="form-group"><label class="control-label col-lg-4">Allegato:</label>
                                                                    <div class="col-lg-4">
                                                                        <input id="allegato'.$array_completate[$i+0].'" type="text"  class="form-control" value ="'.$array_completate[$i+6].'"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                
					                                    </div>
			                                        </form>
			                                        <div style="text-align:right;">
				                                        <a href="" onclick="return false;" class=annulla id="'.$array_completate[$i+0].'">[Annulla]</a>
				                                        <a href="" onclick="return false;" class=conferma id="'.$array_completate[$i+0].'">[Conferma]</a>
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
            </div><!--col-lg-12-->
        </div>
                    </div>
                </div>
            </div>
        <hr />
            <div class="accordion-group">
                <div class="accordion-heading centered">
                    <h3>
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                            Centri indagini diagnostiche
                            <span ><i  class="icon-angle-down"></i></span>
                        </a>
                    </h3>
                </div>
                <div id="collapse2" class="acordion-body collapse">
                    <div class="accordion-inner">
                        <!--elenco dei careproviders che effettuano indagini diagnostiche-->
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        <h3>Studi specialistici</h3>
                                        <hr/>
                                    </div>	<!--panelbody-->
                                </div>	<!--panelwarning-->
                            </div>	<!--col lg4-->
                            <div class="col-lg-4">
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        <h3>Studi Radiologici</h3>
                                        <hr/>
                                    </div>	<!--panelbody-->
                                </div>	<!--panelwarning-->
                            </div>	<!--col lg4-->
                            <div class="col-lg-4">
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        <h3>Laboratori Analisi</h3>
                                        <hr/>
                                    </div>	<!--panelbody-->
                                </div>	<!--panelwarning-->
                            </div>	<!--col lg4-->
                        </div><!--row-->
                    </div>
                </div>
            </div>
        </div>
        <!-- END ACCORDION -->

    </div>
</div>
<!--END PAGE CONTENT -->
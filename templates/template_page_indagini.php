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

         global $n_indagini;             //numero totale di indagini
         $n_indagini = $this->get_var('indaginiNum');

         for ($i = 0; $i < $n_indagini; $i++){          //per ogni indagine...
             $stato = $this->get_var('ind.stato.'.$i);  //verifica lo stato
             switch ($stato){
                 case $stato_richiesta:
                     $array_richieste[$n_r + 0] = $this->get_var('ind.tipo.'.$i);       //tipo indagine
                     $array_richieste[$n_r + 1] = $this->get_var('ind.motivo.'.$i);     //motivazione
                     $array_richieste[$n_r + 2] = $this->get_var('ind.cpNome.'.$i);     //nome careprovider
                     $array_richieste[$n_r + 3] = $this->get_var('ind.cpCognome.'.$i);  //cognome careprovider
                     $array_richieste[$n_r + 4] = $this->get_var('ind.cpRep.'.$i);      //ruolo careprovider
                     $n_r = $n_r + 5;   //offset per prossima indagine
                     break;
                 case $stato_programmata:
                     $array_programmate[$n_p + 0] = $this->get_var('ind.tipo.'.$i);
                     $array_programmate[$n_p + 1] = $this->get_var('ind.motivo.'.$i);
                     $array_programmate[$n_p + 2] = $this->get_var('ind.cpNome.'.$i);
                     $array_programmate[$n_p + 3] = $this->get_var('ind.cpCognome.'.$i);
                     $array_programmate[$n_p + 4] = $this->get_var('ind.cpRep.'.$i);
                     $array_programmate[$n_p + 5] = $this->get_var('ind.data.'.$i);         //data indagine programmata
                     $array_programmate[$n_p + 6] = $this->get_var('ind.centroNome.'.$i);   //nome del centro per indagine
                     $array_programmate[$n_p + 7] = $this->get_var('ind.centroVia.'.$i);    //via del centro indagini
                     $array_programmate[$n_p + 8] = $this->get_var('ind.centroCitta.'.$i);  //citta del centro indagini
                     $n_p = $n_p + 9;   //offset per prossima indagine
                     break;
                 case $stato_completata:
                     $array_completate[$n_c + 0] = $this->get_var('ind.tipo.'.$i);
                     $array_completate[$n_c + 1] = $this->get_var('ind.motivo.'.$i);
                     $array_completate[$n_c + 2] = $this->get_var('ind.cpNome.'.$i);
                     $array_completate[$n_c + 3] = $this->get_var('ind.cpCognome.'.$i);
                     $array_completate[$n_c + 4] = $this->get_var('ind.cpRep.'.$i);
                     $array_completate[$n_c + 5] = $this->get_var('ind.data.'.$i);
                     $array_completate[$n_c + 6] = $this->get_var('ind.referto.'.$i);   //nome del file referto
                     $array_completate[$n_c + 7] = $this->get_var('ind.allegato.'.$i);  //nome del file allegato
                     $n_c = $n_c + 8;   //offset per prossima indagine
                     break;
             }
         }


        $array_centri = array();
        $n_s = 0;
        global $n_centri;             //numero totale di centri
        $n_centri = $this->get_var('centriNum');
        for ($i = 0; $i < $n_indagini; $i++) {          //per ogni centro...
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

        // --------------------------------------
        ?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="formscripts/jquery.js"></script>
        <script src="formscripts/jquery-ui.js"></script>
        <script src="formscripts/indagini.js"></script>

        <div class="row">
            <div class="col-lg-12">
                <h2>Indagini Diagnostiche</h2>
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
                            /*for($i=0; $i<$nDiagno; $i++){
                                $script=$script."$('#txt".$this->get_var('dia.id.'.$i)."').typeahead({
                                hint: true,highlight: true,minLength: 1},{name: 'cps',source: cpSuggest,
                                limit: 10});";
                            }*/
                            $script=$script."$('#nomeCpD').typeahead({ hint: true,highlight: true,
                            minLength: 1},{name: 'cps',source: cpSuggest,limit: 10});});</script>";
                            if ($cp_id == NULL){echo $script;}
                            ?>
                            <div <!--style="display:none;"--> >
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
                                    <label class="control-label col-lg-4">Stato:</label>
                                    <div class="col-lg-4">
                                        <select id="statoIndagine" class="form-control">
                                            <option selected value="0">Richiesta</option>
                                            <option value="1">Programmata</option>
                                            <option value="2">Completata</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Centro:</label>
                                    <div class="col-lg-4">
                                        <select id="centroIndagine" class="form-control">
                                            <?php
                                            for($i = 0; i < $n_s; $n_s+=9){
                                                echo '<option value="'.$array_centri[$i+0] .'">' .$array_centri[$i+1] .'</option>';
                                            }
                                            /*echo '<option selected value="'.0">Richiesta</option>
                                            <option value="1">Programmata</option>
                                            <option value="2">Completata</option>*/
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Care provider:</label>
                                    <div class="col-lg-4">
                                        <input id="nomeCpD" <?php if ($cp_id != NULL){echo 'value="'.$mioCpNome.' '.$mioCpCognome.'" readonly ';}?> class="form-control"/>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Data:</label>
                                    <div class="col-lg-4">
                                        <input id="data" type="date" placeholder="aaaa-mm-gg" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Referto:</label>
                                    <div class="col-lg-4">
                                        <input id="referto" type="text"  class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Allegato:</label>
                                    <div class="col-lg-4">
                                        <input id="allegato" type="text"  class="form-control"/>
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

                <!--------------------------------------------------------->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-warning">
                            <div class="panel-heading">Indagini Richieste</div>
                            <div class=" panel-body">
                                <div class="table-responsive" >
                                    <table class="table" id="tableRichieste">
                                        <thead>
                                        <tr>
                                            <th>Indagine</th>
                                            <th>Motivo</th>
                                            <th>Care provider</th>
                                            <th>Opzioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php   //popolamento tabella indagini richieste
                                        for($i = 0; $i < $n_r; $i+=5){
                                            echo '<tr><td>' . $array_richieste[$i+0] . '</td>';
                                            echo '<td>' . $array_richieste[$i+1] . '</td>';
                                            echo '<td>' . $array_richieste[$i+2] . ' ' . $array_richieste[$i+3] . '<br>(' .
                                            $array_richieste[$i+4] . ')</td>';
                                            echo '<td>buttonz</td></tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>	<!--paneldanger-->
                        </div>	<!--col lg12-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-danger">
                            <div class="panel-heading">Indagini Programmate</div>
                            <div class=" panel-body">
                                <div class="table-responsive" >
                                    <table class="table" id="tableProgrammate">
                                        <thead>
                                        <tr>
                                            <th>Indagine</th>
                                            <th>Motivo</th>
                                            <th>Care provider</th>
                                            <th>Data</th>
                                            <th>Centro</th>
                                            <th>Opzioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php   //popolamento tabella indagini programmate
                                        for($i = 0; $i < $n_p; $i+=9){
                                            echo '<tr><td>' . $array_programmate[$i+0] . '</td>';
                                            echo '<td>' . $array_programmate[$i+1] . '</td>';
                                            echo '<td>' . $array_programmate[$i+2] . ' ' . $array_programmate[$i+3] . '<br>(' .
                                                $array_programmate[$i+4] . ')</td>';
                                            echo '<td>' . $array_programmate[$i+5] . '</td>';
                                            echo '<td>' . $array_programmate[$i+6] . '<br>' . $array_programmate[$i+7] . ' - ' .
                                                $array_programmate[$i+8] . '</td>';
                                            echo '<td>buttonz</td></tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>	<!--paneldanger-->
                        </div>	<!--col lg12-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">Indagini completate</div>
                            <div class=" panel-body">
                                <div class="table-responsive" >
                                    <table class="table" id="tableCompletate">
                                        <thead>
                                        <tr>
                                            <th>Indagine</th>
                                            <th>Motivo</th>
                                            <th>Care provider</th>
                                            <th>Data</th>
                                            <th>Referto</th>
                                            <th>Allegati</th>
                                            <th>Opzioni</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php   //popolamento tabella indagini completate
                                        for($i = 0; $i < $n_c; $i+=8){
                                            echo '<tr><td>' . $array_completate[$i+0] . '</td>';
                                            echo '<td>' . $array_completate[$i+1] . '</td>';
                                            echo '<td>' . $array_completate[$i+2] . ' ' . $array_completate[$i+3] . '<br>(' .
                                                $array_completate[$i+4] . ')</td>';
                                            echo '<td>' . $array_completate[$i+5] . '</td>';
                                            echo '<td>' . $array_completate[$i+6] . '</td>';
                                            echo '<td>' . $array_completate[$i+7] . '</td>';
                                            echo '<td>buttonz</td></tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>	<!--paneldanger-->
                        </div>	<!--col lg12-->
                    </div>
                </div>
            </div><!--col-lg-12-->
        </div>




        <hr />




    </div>




</div>
<!--END PAGE CONTENT -->
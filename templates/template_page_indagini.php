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
	    									 "<br>";
	    					}	
	    						
        				}
        				else echo "<h5>Permesso negato<h5>";
}

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
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		echo '<button class="btn btn-primary" id="nuovoFile"><i class="icon-file-text-alt"></i> Nuova indagine</button>';
	}else{
		echo '<button disabled class="btn btn-primary" id="nuovoFile"><i class="icon-file-text-alt"></i> Nuova indagine</button>';
	}
	?>
<button class="btn btn-primary" id="concludi"><i class="icon-ok-sign"></i> Concludi indagine</button>
<button class="btn btn-primary" id="annulla"><i class="icon-trash"></i> Annulla indagine</button>

</div></div></div><br>

<form style="display:none;" id="formIndagini" action="formscripts/none.php" method="POST" class="form-horizontal" >
			<div class="tab-content">
				<div class="row">
				<div style="display:none;">
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label col-lg-4">ID Paziente:</label>
							<div class="col-lg-4">
								<input id="idPaziente" type="text"  readonly class="form-control" value="<?php echo $idPaziente; ?>"/>
							</div>
						</div>
					</div>
					
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label col-lg-4">ID Diagnosi:</label>
							<div class="col-lg-4">
								<input id="idDiagnosi" type="text"  readonly class="form-control" value="<?php echo $idDiagnosi; ?>"/>
							</div>
						</div>
					</div>
					
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label col-lg-4">Motivo:</label>
							<div class="col-lg-4">
								<input id="motivoIndagine" type="text"  readonly class="form-control" value="<?php echo 'verifica diagnosi di '.$nomePatologia; ?>"/>
							</div>
						</div>
					</div>
				</div>	
							
							
						
				
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
						<label class="control-label col-lg-4">Data:</label>
						<div class="col-lg-4">
						
								<input id="data" type="date" placeholder="aaaa-mm-gg" class="form-control"/>
								</div>
						</div>
						</div>
						
						
						<div class="col-lg-12" style="display:none;">
						<div class="form-group">
						<label class="control-label col-lg-4">cpId:</label>
						<div class="col-lg-4">
						
								<input id="cpId" readonly
								<?php if ($cp_id != NULL) {echo 'value="'.$this -> get_var('idUtenteCp').'" ';} else {echo 'value="-1"';} ?> class="form-control"/>
								</div>
						</div>
						</div>
						
						<div class="col-lg-12" style="display:none;">
						<div class="form-group">
						<label class="control-label col-lg-4">pzId:</label>
						<div class="col-lg-4">
						
						<input id="pzId" readonly
						<?php echo 'value="'.$this->get_var('idUtentePaz').'" '; ?> class="form-control"/>
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
					
					<div id="da_eliminare" style="display:none;">
					<div class="col-lg-12">
						<div class="form-group">	
							<label class="control-label col-lg-4">Care provider:</label>
								<div class="col-lg-4">
		<?php
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
		?>
			
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
					
					</div>
						
							
				</div>
			</div>
		</form><br>
		
		

		
		<div class="row">
									<div class="col-lg-12"> 
											<div class="panel panel-warning">
											<div class="panel-heading">Indagini effettuate per diagnosi di <?php echo $nomePatologia.' del '.$dataDiagnosi; ?></div>
												<div class="panel-body">
													
												
													<div class="table-responsive" >
                                                        <table class="table" id="tableIndagini">
                                                            <thead>
                                                                <tr>
                                                                	<th>Data</th>
                                                                    <th>Indagine</th>
                                                                    
																	<th>Referto</th> 
																	<th>Allegati</th>
																	  
                                                                </tr>
                                                            </thead>
                                                            <tbody>
													<?php

                                                        global $indaginiNum;
                                                        $indaginiNum = $this ->get_var('indaginiNum');
														for($i=0; $i<$indaginiNum; $i++)
														{
														echo '<tr>';
														echo '<td>'.$this->get_var('ind.data.'.$i).'</td>
															  <td>'.$this->get_var('ind.tipo.'.$i).'</td>
															  <td>'.$this->get_var('ind.referto.'.$i).'</td>
															  <td>'.$this->get_var('ind.allegato.'.$i).'</td>';
														echo '</tr>';
														}
															?>
															</tbody>
                                                        </table>
                                                    </div>
													
												</div> <!--panelbody-->	
											</div>	<!--paneldanger-->	
									</div>	<!--col lg12-->
									</div>
		

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
                                                <table class="table" id="tableRichieste">
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
                                                <table class="table" id="tableRichieste">
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
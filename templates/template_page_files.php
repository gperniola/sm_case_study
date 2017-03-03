<!--PAGE CONTENT -->

<!--sono predisposte diverse sezioni. In ciascuna è presente un form per il caricamento dei file ed un elemento nav per contenere i link ai file caricati
 27/10/15 la classe nav-file è ancora da modellare gli id nav-files... sono da utilizzare per aggiungere a ciascun elemento una lista dei files ottenuta dal db
 il file css in cui sono modellati gli elementi  è in assets/plugin/botstrap/css/bootstrap.css-->

<?php 
	require_once ($_SERVER['DOCUMENT_ROOT'].'modello PBAC/Utility.php');

	//determino se l'utente è un paziente o un careprovider e di conseguenza si determina il $pz_id
		if ( isset ($_GET["cp_Id"]))
		{
			$cp_id = $_GET["cp_Id"]; //inizializzo $cp_id col valore passato con GET
			$id_prop = $cp_id; // variabile da utilizzare nell'invio dei file per determinare da chi è stato inviato il file
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
		else	//se non è settatto pz_Id , $pz_id è l'id di chi si è loggato e ad inviare il file è il paziente
		{
			$pz_id  = getMyID();
			$id_prop = $pz_id;
		}

	
	if ( isset ($_GET["cp_Id"]))
	{
		$cp_id = $_GET["cp_Id"]; //inizializzo $cp_id col valore passato con GET
		
		$myRole = getRole($cp_id);
	}
	
	// ottengo l'id del paziente 
	
	
	//creo le liste dei file da caricare la funzione caricaDirectory e DIR_IMMAGINI sono da modificare 
	//errore nel caricamento della directory per percorso errato
		
	$lista_foto = caricaDirectory("files/uploads/foto",0, $pz_id,0,0,0 );
	
	$lista_registrazioni = caricaDirectory( "files/uploads/registrazioni",0, $pz_id,0,0,0 );
	$lista_videoPz = caricaDirectory( "files/uploads/videoPz",0, $pz_id,0,0,0 );
	$lista_dicom = caricaDirectory( "files/uploads/dicom",0, $pz_id,0,0,0 );
	$lista_scansioni = caricaDirectory( "files/uploads/scansioni",0, $pz_id,0,0,0 );
	$lista_videoStrum = caricaDirectory( "files/uploads/videoStrum",0, $pz_id,0,0,0 );
	$arrayFiles = $this->get_var("arrayFiles");
?>
        <div id="content">

            <div class="inner" style="min-height:1200px;">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>Files</h2>
						<p>In questa pagina sarà possibile visualizzare ed inviare files di immagini di lesioni cliniche immagini di indagini diagnostiche,
						registrazioni, brevi video, risultati di esami o documenti testuali. </p>
						<hr/>
<!-- ACCORDION -->
	<div class="accordion ac" id="accordion2">
		<div class="accordion-group">
		    <div class="accordion-heading centered">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
		            <h2>Files Caricati   &nbsp &nbsp &nbsp &nbsp
						<span >
                          <i  class="icon-angle-down"></i>
                        </span>           	
					</h2>
                </a>
			</div><!--accordion- group heading centered-->
			<div id="collapseOne" class="accordion-body collapse">
		        <div class="accordion-inner">	
					<div class="table table-striped table-bordered table-hover" >
				<div class="table-responsive" >
					<table class="table" >
						<thead>
							<tr>
								<th></th>
								<th>Nome File</th>
								<th>Commenti</th>
								<th>Data creazione</th>
								<th>Caricato da:</th>
								
								<?php if ( $role == "pz")
									echo '<th>Conf.</th>													
										  <th>Opzioni</th>';
								?>
							</tr>
						</thead>
						<tbody>
			<?php
					$i = 0;
					
							//da utilizzare per la verifica di arrayfiles
						foreach ($arrayFiles as $idFile){
								//$link = 'http://localhost'.':8181/'.'files/';//$link = 'http://localhost:8181/files/';//$link = "http://www.fsem.eu/files/";
								$link = 'files/';
								$idFiles = $this->get_var("idFile".$i);
								$dataCreaz = italianFormat($this->get_var("dataCreaz".$i) );
								$path 		= $this->get_var("path".$i);
								$nomeFile	= $this->get_var("nomeFile".$i);
								$propSurname= $this->get_var("propSurname".$i);
								$extension	= $this->get_var("extension".$i);
								$codConfidenzialita  = $this->get_var("codConfidenzialita".$i);
								$commento 	= $this->get_var("commento".$i);
								$link .= $path . $nomeFile ;
								$window = "window.open('".$link."')";
								
								echo   
									'<tr>
										<td><button class= "btn btn-default btn-success "  type = "submit" onclick ='. $window .'> <i class="icon-check"></i></button></td>
										<td><a href = " ' .$link . ' ">'.$nomeFile.'</a></td><td>'.$commento.'</td><td>'.$dataCreaz.'</td><td>'.$propSurname.'</td>';
										
										if ( $role == "pz")
											echo '<td id = "nomeFile_ ' .$i. 'conf">'. $codConfidenzialita.'</td>
												<td>
													<table>
														<tr>
															<td>
																<div class="dropdown">
																	  <button class="btn btn-info dropdown-toggle dropdown-toggle-set" type="button" id="dropdownMenuSet_'.$idFiles.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" value="'.$codConfidenzialita.$i.'_conf"> 
																		<i class="icon-check"></i>
																		<span class="caret"></span>
																	  </button>
																	  <ul id="setLevelFiles_'.$idFiles.'" class="dropdown-menu" aria-labelledby="dropdownMenu_'.$idFiles.'">
																		<li><a value="'.$idFiles.'" id="1" class="to_do" >Livello confidenzialit&agrave 1</a></li>
																		<li><a value="'.$idFiles.'" id="2" class="to_do">Livello confidenzialit&agrave 2</a></li>
																		<li><a value="'.$idFiles.'" id="3" class="to_do">Livello confidenzialit&agrave 3</a></li>
																		<li><a value="'.$idFiles.'" id="4" class="to_do">Livello confidenzialit&agrave  4</a></li>
																		<li><a value="'.$idFiles.'" id="5" class="to_do">Livello confidenzialit&agrave 5</a></li>
																		<li><a value="'.$idFiles.'" id="6" class="to_do">Livello confidenzialit&agrave 6</a></li>
																	  </ul>
																	</div>														
															</td>
															<td>
																<button id="buttonCpp_'.$i.'" value=" '.$idFiles.' " type="button" class="buttonDelete btn btn-default btn-danger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-remove"></i></button>
															</td>
															
														</tr>
													</table>
												</td>											
									</tr>';
								$i++;
							}
				?>
				
						</tbody>
					</table>
					<hr/>	
				</div><!--class="table-responsive"-->
			</div><!--class table table-striped table-bordered table-hover-->
				</div><!--accordion-inner-->
			</div><!--accordion-body collapse-->	
		</div><!--accordion-group-->
		<hr>
		<div class="accordion-group">
		    <div class="accordion-heading centered">
		         <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
					<h2>Carica nuovi files &nbsp 
						<span >
                          <i  class="icon-angle-down"></i>
                        </span>      
					</h2>
				</a>
			</div><!--accordion- group heading centered-->
			<div id="collapseTwo" class="accordion-body collapse">
		         <div class="accordion-inner">
					<div class="accordion ac"id = "collapseTwo_A" ><!--accordion ac interno-->
						<div class="accordion-group">
							<div class="accordion-heading centered">
								<div class ="row">
									<div class="col-lg-4"> 
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo_A" href="#collapseFoto">
											<h2>Foto del paziente </h2>
										</a>
									</div><!--col-lg-4-->
									<div class="col-lg-4"> 
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo_A" href="#collapseVideo">
											<h2>Video del paziente</h2>
										</a>
									</div><!--col-lg-4-->
									<div class="col-lg-4"> 
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo_A" href="#collapseReg">
											<h2>Registrazioni</h2>
										</a>
									</div><!--col-lg-4-->
									<hr>
									<div class="col-lg-4"> 	
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo_A" href="#collapseStrum">
											<h2>Video Esami Strumentali</h2>
										</a>
									</div><!--col-lg-4-->
									<div class="col-lg-4"> 	
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo_A" href="#collapseDicom">
											<h2>Immagini Dicom</h2>
										</a>
									</div><!--col-lg-4-->
									<div class="col-lg-4"> 	
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo_A" href="#collapseDocuments">
											<h2>Documenti di testo</h2>
										</a>
									</div><!--col-lg-4-->
									
								</div><!--row-->
								
									<div class="row">
									<div id = "collapseFoto" class="accordion-body collapse" >
										<div class="col-lg-12"> 
												<div class="panel panel-warning">
													<div class="panel-body">
														<h3>foto</h3>
														<hr/>
														<form method = "post" action = "../files/uploadFiles.php" enctype = "multipart/form-data"> 
															<input  type = "file" name = "nomefile"/>
															<br>
															<label for "comm">Note sul file caricato:
															</label>
															<textarea name="comm"  cols = "60" rows = "2"  > 
															</textarea>
															<br><br>
															<label for "conf_1">visibilità</label>
															<select name="conf_1">
																	<option value="1">nessuna restrizione</option>
																	<option value="2">basso</option>
																	<option value="3">moderato</option>
																	<option value="4" >normale</option>
																	<option value="5" selected = "true">riservato</option>
																	<option value="6"> strettamente riservato</option>
															  </select>
															  <br> <br>
															<input  type = "hidden" name = "fileClass1" value = "1"/> <!--classe per le foto -->
															<input  type = "hidden" name = "idPaz" value = "<?php echo $pz_id; ?>" />
															 <input  type = "hidden" name = "id_prop" value = "<?php echo $id_prop; ?>"  /> 
																
															<input type = "submit" name = "invia" value = "Invia"/>
															<input type='reset' value='Reset' name='reset'>
														</form>
													</div>	<!--panelbody-->
												</div>	<!--panelwarning-->	
										</div>	<!--col lg12-->
									</div><!--collapse foto-->	
									
								<div id = "collapseVideo" class="accordion-body collapse" >
									<div class="col-lg-12"> 
											<div class="panel panel-warning">
												<div class="panel-body">
													<h3>video</h3>
													<hr/>
													<form method = "post" action = "../files/uploadFiles.php" enctype = "multipart/form-data"> 
														<input  type = "file" name = "nomefile"/>
														<br>
														<label for "comm">Note sul file caricato:
														</label>
														<textarea name="comm"  cols = "60" rows = "2"  > 
														</textarea>
														<br><br>
														<label for "conf_1">visibilità</label>
														<select name="conf_1">
																<option value="1">nessuna restrizione</option>
																<option value="2">basso</option>
																<option value="3">moderato</option>
																<option value="4" >normale</option>
																<option value="5" selected = "true">riservato</option>
																<option value="6"> strettamente riservato</option>
														  </select>
														  <br> <br>
														<input  type = "hidden" name = "fileClass2" value = "2"/> <!--classe per i video non diagnostici -->
														<input  type = "hidden" name = "idPaz" value = "<?php echo $pz_id; ?>" />
														 <input  type = "hidden" name = "id_prop" value = "<?php echo $id_prop; ?>"  /> 
														<input type = "submit" name = "invia" value = "Invia"/>
														<input type='reset' value='Reset' name='reset'>
													</form>
												</div>	<!--panelbody-->
											</div>	<!--panelwarning-->	
									</div>	<!--col lg12-->
								
								</div><!--collapse video-->
								
								</div>
								<div class="row">
									<div id = "collapseReg" class="accordion-body collapse" >
										<div class="col-lg-12"> 
												<div class="panel panel-danger">
													<div class="panel-body">
														<h3>registrazioni</h3>
														<br/>
														<hr/>
														<form method = "post" action = "../files/uploadFiles.php" enctype = "multipart/form-data"> 
															<input  type = "file" name = "nomefile"/>
															<br>
															<label for "comm">Note sul file caricato:
															</label>
															<textarea name="comm"  cols = "60" rows = "2"  > 
															</textarea>
															<br><br>
															<label for "conf_1">visibilità</label>
															<select name="conf_1">
																	<option value="1">nessuna restrizione</option>
																	<option value="2">basso</option>
																	<option value="3">moderato</option>
																	<option value="4" >normale</option>
																	<option value="5" selected = "true">riservato</option>
																	<option value="6"> strettamente riservato</option>
															  </select>
															  <br> <br>
															<input  type = "hidden" name = "fileClass3" value = "3"/> <!--classe per le registrazioni -->
															<input  type = "hidden" name = "idPaz" value = "<?php echo $pz_id; ?>" />
															 <input  type = "hidden" name = "id_prop" value = "<?php echo $id_prop; ?>"  />
															<input type = "submit" name = "invia" value = "Invia"/>
															<input type='reset' value='Reset' name='reset'>
														</form>
													</div>	<!--panelbody-->
												</div>	<!--panel-danger-->
										</div>	<!--col lg12-->
									</div><!--collapse Reg-->
									<div id = "collapseStrum" class="accordion-body collapse" >	
										<div class="col-lg-12"> 
										<div class="panel panel-danger">
											<div class="panel-body">
											
												<h3>video di esami strumentali</h3>
												<p>coronarografie, esami endoscopici etc.</p>
												<hr/>
												<form method = "post" action = "../files/uploadFiles.php" enctype = "multipart/form-data"> 
														<input  type = "file" name = "nomefile"/>
														<br>
														<label for "comm">Note sul file caricato:
														</label>
														<textarea name="comm"  cols = "60" rows = "2"  > 
														</textarea>
														<br><br>
														<label for "conf_1">visibilità</label>
														<select name="conf_1">
																<option value="1">nessuna restrizione</option>
																<option value="2">basso</option>
																<option value="3">moderato</option>
																<option value="4" >normale</option>
																<option value="5" selected = "true">riservato</option>
																<option value="6"> strettamente riservato</option>
														  </select>
														  <br> <br>
														<input  type = "hidden" name = "fileClass5" value = "5"/> <!--classe per video diagnostici -->
														<input  type = "hidden" name = "idPaz" value = "<?php echo $pz_id; ?>" />
														 <input  type = "hidden" name = "id_prop" value = "<?php echo $id_prop; ?>"  />
														<input type = "submit" name = "invia" value = "Invia"/>
														<input type='reset' value='Reset' name='reset'>
												</form>
											</div>	<!--panelbody-->
										</div>	<!--panelinfo-->	
									</div>	<!--col lg12-->
									</div>
								</div><!--row-->
								
						<div class="row">
							<div id = "collapseDicom" class="accordion-body collapse" >
								<div class="col-lg-12"> 
									<div class="panel panel-info">
										<div class="panel-body">
										
										<h3>immagini dicom</h3>
										<br/>
											<p>immagini radiologiche ecografiche di cui in alcuni casi ai pazienti vengono forniti i cd.</p>
											<br/>
											<hr/>
											<form method = "post" action = "../files/uploadFiles.php" enctype = "multipart/form-data"> 
													<input  type = "file" name = "nomefile"/>
													<br>
													<label for "comm">Note sul file caricato:
													</label>
													<textarea name="comm"  cols = "60" rows = "2"  > 
													</textarea>
													<br><br>
													<label for "conf_1">visibilità</label>
													<select name="conf_1">
															<option value="1">nessuna restrizione</option>
															<option value="2">basso</option>
															<option value="3">moderato</option>
															<option value="4" >normale</option>
															<option value="5" selected = "true">riservato</option>
															<option value="6"> strettamente riservato</option>
													  </select>
													  <br> <br>
													<input  type = "hidden" name = "fileClass4" value = "4"/> <!--classe per files dicom -->
													<input  type = "hidden" name = "idPaz" value = "<?php echo $pz_id; ?>" />
													 <input  type = "hidden" name = "id_prop" value = "<?php echo $id_prop; ?>"  />
													<input type = "submit" name = "invia" value = "Invia"/>
													<input type='reset' value='Reset' name='reset'>
											</form>
										
												</div>	<!--panelbody-->
									</div>	<!--panelinfo-->	
								</div>	<!--col lg12-->
							</div>	<!--collapse Dicom-->
							
							<div id = "collapseDocuments" class="accordion-body collapse" >
								<div class="col-lg-12"> 
								<div class="panel panel-info">
									<div class="panel-body">
										<h3>referti-lettere di dimissione</h3>
										<h4>scansione di documenti clinici</h4>
										<p>accetta i formati: pdf, doc, docx ,txt, odt.
										Nel caso i files contengano informazioni sensibili &egrave raccomandata la protezione con password.</p>
										<hr/>
										<form method = "post" action = "../files/uploadFiles.php" enctype = "multipart/form-data"> 
												<input  type = "file" name = "nomefile"/>
												<br>
												<label for "comm">Note sul file caricato:
												</label>
												<textarea name="comm"  cols = "60" rows = "2"  > 
												</textarea>
												<br><br>
												<label for "conf_1">visibilità</label>
												<select name="conf_1">
														<option value="1">nessuna restrizione</option>
														<option value="2">basso</option>
														<option value="3">moderato</option>
														<option value="4" >normale</option>
														<option value="5" selected = "true">riservato</option>
														<option value="6"> strettamente riservato</option>
												  </select>
												  <br> <br>
												<input  type = "hidden" name = "fileClass6" value = "6"/> <!--classe per scansioni referti, lettere di dimissioni -->
												<input  type = "hidden" name = "idPaz" value = "<?php echo $pz_id; ?>" />
												 <input  type = "hidden" name = "id_prop" value = "<?php echo $id_prop; ?>"  />
												<input type = "submit" name = "invia" value = "Invia"/>
												<input type='reset' value='Reset' name='reset'>
										</form>
									</div>	<!--panelbody-->
								</div>	<!--panelwarning-->	
							</div>	<!--col lg12-->	
							</div> <!--collapse Documents-->
						</div>	<!--row-->
							</div><!--fine accordion heading centered collapseTwo_A-->
						</div><!--fine accordion-group collapseTwo_A-->
					</div><!--fine accordion ac interno collapseTwo_A-->
				</div><!--accordion-inner-->
			</div><!--accordion-body collapse-->
		</div><!--accordion group-->
	</div><!--accordion-->							
                        <?php
                       
						$myRole = getRole(getMyID());
						$maxConfidentiality = 0;
						$defaultPermissions = false;
						
						if($myRole == 'ass' or $myRole == 'emg'
						//verosimilmente è da correggere 'idutente = 1'
						or getInfo('idcpp', 'careproviderpaziente', 'idutente = 1') == getMyID()){ 
							$response = 'Permit';
							$maxConfidentiality = INF;
							$defaultPermissions = true;
						}
						
						else $response = getResponse('Files', 'Lettura');
      				
        				if ($response == 'Permit'){
        					setAuditAction(getMyID(), 'Accesso a Files');
        					
        					if ($maxConfidentiality == 0)
	    						$maxConfidentiality = policyInfo('Files', 'Confidenzialità');
	    						
	    					if (!$defaultPermissions){
	    							$obligations = policyInfo('Files', 'Obblighi');
	    								
	    							if ($obligations == 'In presenza del titolare' && $myRole != 'ass')
	    								echo "Questa sezione può essere consultata solo in presenza del titolare" . 
	    									 "<br>";
	    					}	
	    						
        				}
						
						//al 6/2/16 da verificare la riga successiva ; compare anche se il care provider è autorizzato
        				else echo "<h5>Permesso negato<h5>";
        				
 						?>
					</div>
                </div><!--row-->

                <hr />
			</div><!--inner-->

        </div><!--content-->
<!--END PAGE CONTENT -->
<!-- Custom javascript -->
	<?php include "formscripts/fileScript.php"?>
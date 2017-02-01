<!--PAGE CONTENT -->
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
				$pz_id = $_GET["pz_Id"]; //inizializzo $pz_id col valore passato con GET
		else	
				$pz_id  = getMyID();
?>
        <div id="content">

            <div class="inner" style="min-height:1200px;">
                <div class="row">
                    <div class="col-lg-12">


                        <h2>Vaccinazioni</h2>
                        
                        <?php
					
						//$myRole = getRole(getMyID());
						$maxConfidentiality = 0;
						$defaultPermissions = false;
						
						if($myRole == 'ass' or $myRole == 'emg' or $role == 'cp' //or $role == 'cp' permette a qualsiasi care provider di accedere
						or getInfo('idcpp', 'careproviderpaziente', 'idutente = ' . $pz_id) == getMyID()){ // l'istruzione contenuta nella riga prende solo il primo dei care provider 
							$response = 'Permit';
							$maxConfidentiality = INF;
							$defaultPermissions = true;
						}
						
						else $response = getResponse('Vaccinazioni', 'Lettura');
      				
        				if ($response == 'Permit'){
        					setAuditAction(getMyID(), 'Accesso a Vaccinazioni');
        					
        					if ($maxConfidentiality == 0)
	    						$maxConfidentiality = policyInfo('Vaccinazioni', 'Confidenzialità');
	    						
	    					if (!$defaultPermissions){
	    							$obligations = policyInfo('Vaccinazioni', 'Obblighi');
	    								
	    							if ($obligations == 'In presenza del titolare' && $myRole != 'ass')
	    								echo "Questa sezione può essere consultata solo in presenza del titolare" . 
	    									 "<br>";
	    					}	
	    						
        				}
        				else echo "<h5>Permesso negato<h5>";
        				
 						?>



                    </div>
                </div>

                <hr />

                <div class="row">
                <div class="col-lg-12">
                    <div class="box dark">
                        <header>
                        	<div class="toolbar">
                				<ul class="nav">
                   			 		<li><a href="#"><i class="icon-plus-sign icon-white"></i> Nuova Vaccinazione</a></li>
                           		</ul>
                            </div>
                        </header>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-vaccinazioni">
                                    <thead>
                                        <tr>
                                            <th>Codice</th>
                                            <th>Nome</th>
                                            <th>Descrizione</th>
                                            <th>Data vaccino<br/><small class="text-muted">(aaaa-mm-gg)</small></th>
                                            <th>Data fine copertura<br/><small class="text-muted">(aaaa-mm-gg)</small></th>
                                            <th>Reazioni</th>
                                            <th>Care Provider</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
												$num=$this->get_var('nvaccinazioni');
												for($i=1; $i<=$num ; $i++){
												  if ($this->is_set('vaccinazione_'.$i."_alert"))
													switch($this->get_var('vaccinazione_'.$i."_alert")){
															case 'danger': echo '<tr class="danger">'; break;
															case 'warning': echo '<tr class="warning">'; break;
															case 'success': echo '<tr class="success">'; break;
															default: echo '<tr>'; break;
														}
												   else echo '<tr>';					
														echo "<td>",$this->get_var('vaccinazione_'.$i.'_codice'),"</td>\n
															  <td>",$this->get_var('vaccinazione_'.$i),"</td>\n
															  <td>",$this->get_var('vaccinazione_'.$i.'_descrizione'),"</td>\n
															  <td>",$this->get_var('vaccinazione_'.$i.'_inizio'),"</td>\n						
															  <td>",$this->get_var('vaccinazione_'.$i.'_fine'),"</td>\n
															  <td>",$this->get_var('vaccinazione_'.$i.'_reazioni'),"</td>\n
															  <td>",$this->get_var('vaccinazione_'.$i.'_CP'),"</td>\n";
													    echo "</tr>\n"; 
												}
                         						?>
                                    </tbody>
                                </table>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>




            </div>




        </div>
<!--END PAGE CONTENT -->
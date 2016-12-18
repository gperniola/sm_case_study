<!-- MENU SECTION -->
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
  <div id="left">
            <div class="media user-media well-small">
                <a class="user-link" href="<?php echo $this->get_var('link_patientpic'); ?>">
                <?php
                	if ($this -> is_set('patientpic_path')){	
                ?>
                	<img class="media-object img-thumbnail user-img" alt="Immagine Utente" height="200" width="200" src="<?php                                echo $this->get_var('patientpic_path');?>" />
                <?php 
                	}
                    else{
                ?>
                    <img class="media-object img-thumbnail user-img" alt="Immagine Utente" src="assets/img/user.gif" />
                <?php
                	}
                ?>
                </a>
                <br />
                <div class="media-body">
                  <h5 class="media-heading"><?php echo $this->get_var('patient_cognome'); ?></h5>
                  <h5 class="media-heading"><?php echo $this->get_var('patient_nome'); ?></h5>
                </div>
                <br />
            </div>
            <!--ANAGRAFICA RIDOTTA-->
            <div class="well well-sm">         
                <ul class="list-small">
                  <li><strong>C.F.</strong>: <span><?php echo $this->get_var('patient_CF'); ?></span></li>
                  <li><strong>Data di nascita</strong>: <span><?php echo $this->get_var('patient_datanascita'); ?></span> <strong>Età</strong>: <span><?php echo $this->get_var('patient_eta'); ?></span></li>
                  <li><strong>Telefono</strong>: <?php echo $this->get_var('patient_tel'); ?></li>
                  <?php if( $role == "cp"){
				    echo  '<li><a  href="#" data-toggle="modal" data-target="#formModal">
							<i class="icon-envelope-alt"></i> <' . $this->get_var("patient_email") . ' >' .  $this->get_var("patient_email") . '
						  </a>
					  </li>';
				  }
				  ?>
                </ul>
            </div>
			<!--FINE ANAGRAFICA RIDOTTA-->
            <!--MODAL EMAIL-->
			<div class="col-lg-12">
                        <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="chiudiformmodalmail">&times;</button>
                                            <h4 class="modal-title" id="H2">Nuova Email</h4>
                                        </div>
                                        <form class="form-horizontal"  id="patmailform">
                                        <div class="modal-body">
                                        	<div class="form-group">
											<!--il getvar deve prendere nome e cognome del medico-->
                                        	    <label class="control-label col-lg-4">Da <?php echo $this->get_var('cp_cognome')," ",$this->get_var('cp_nome'); ?> :</label>
                                            	<div class="col-lg-8">
                                           	 	<input type="text" name="nomeutente" id="nomeutente" value="<?php echo $this->get_var('email_cp')?>" readonly class="form-control"/>
												</div>
                                        	</div>
                                        	<div class="form-group">
                                        	    <label class="control-label col-lg-4">A <?php echo $this->get_var('patient_cognome')," ",$this->get_var('patient_nome'); ?>:</label>
                                            	<div class="col-lg-8">
                                           	 	<input type="text" name="mail" id="mail" value="<?php echo $this->get_var('patient_email'); ?>" readonly class="form-control"/>
                                            	</div>
                                        	</div>
                                        	<div class="form-group">
                                            	<label for="oggettomail" class="control-label col-lg-4">Oggetto:</label>
                    							<div class="col-lg-8">
                        						<input type="text" name="oggettomail" id="oggettomail" class="form-control col-lg-6"/>
                    							</div>
                                        	</div>
                                        	<div class="form-group">
                                            	<label for="contenuto" class="control-label col-lg-4">Testo:</label>
                    							<div class="col-lg-8">
                        							<textarea name="contenuto" id="contenuto" class="form-control col-lg-6" rows="6"></textarea>
                   							 	</div>
                                        	</div>
                                         </div>
                                         <div class="modal-footer">
                                            	<button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
                                            	<button type="submit" class="btn btn-primary">Invia</button> 
                                         </div>
                                         </form>
                                    </div>
                                </div>
                            </div>
	
			</div>
          <!--  FINE MODAL EMAIL-->
            <div class="row">
            	<div class="well well-sm"> 
					<?php
                        if($this->get_var('visita_in_corso'))
                            echo '<a href="'.$this->get_var('link_visita').'" class="btn btn-success btn-block" id="btn_menu_nuovavisita"><i class="icon-stethoscope"></i> Visita in corso...';
                        else
                            echo '<a href="'.$this->get_var('link_visita').'" class="btn btn-primary btn-block" id="btn_menu_nuovavisita"><i class="icon-stethoscope"></i>  Visite';
                        echo '</a>';
                    ?>
            	</div>
           	</div>
            
    <ul id="menu" class="collapse">
              <li class="panel<?php if ($this->get_var('panel_active')=='patientsummary') echo ' active';?>"> <a href="<?php echo $this->get_var('link_patientsummary'); ?>"> <em class="icon-table"></em> Patient Summary esteso </a></li>
           
           <!-- ANAMNESI -->
           
              <li class="panel<?php if (($this->get_var('panel_active')=='anamnesifam')) echo ' active';?>"> <a href="<?php echo $this->get_var('link_anamnesifam'); ?>"> <em class="icon-archive"></em> Anamnesi </a></li>  
                    
                
                <!-- VACCINAZIONE -->
                 
                <!-- ALLERGIE / INTOLLERANZE -->
               
				<li class="panel<?php if (($this->get_var('panel_active')=='indagini') || ($this->get_var('panel_active')=='richieste')) echo ' active';?>">
                    <a id="diagnosticArrowLink" href="" onClick="return false;" data-parent="#menu" data-toggle="collapse" class="accordion-toggle collapsed" data-target="#form-nav">
                        <i class="icon-search"></i> Indagini Diagnostiche
                        <span class="pull-right">
                            <i id="diagnosticArrow" class="icon-angle-left"></i>
                        </span>
                    </a>
                    <ul class="<?php if (($this->get_var('panel_active')=='indagini') || ($this->get_var('panel_active')=='richieste')) echo 'in'; else echo 'collapse';?>" id="form-nav">
                        <li class="diagnostic"><a href="<?php echo $this->get_var('link_indagini'); ?>"><i class="icon-angle-right"></i> Diario Indagini Diagnostiche </a></li>
                        <li class="diagnostic"><a href="<?php echo $this->get_var('link_richieste'); ?>"><i class="icon-angle-right"></i> Richiesta Indagini Diagnostiche </a></li>
                    </ul>
                </li>
                <li class="panel<?php if ($this->get_var('panel_active')=='diagnosi') echo ' active';?>"> <a href="<?php echo $this->get_var('link_diagnosi'); ?>"> <em class="icon-file-text-alt"></em> Diagnosi </a></li>  
                  <li class="panel<?php if ($this->get_var('panel_active')=='terapie') echo ' active';?>"> <a href="<?php echo $this->get_var('link_terapie'); ?>"> <em class="icon-medkit"></em> Terapie Farmacologiche </a></li>
                <li class="panel<?php if ($this->get_var('panel_active')=='procedure') echo ' active';?>"> <a href="<?php echo $this->get_var('link_procedure'); ?>"> <em class="icon-hospital"></em> Procedure Terapeutiche </a></li>
                <li class="panel<?php if ($this->get_var('panel_active')=='dispositivi') echo ' active';?>"> <a href="<?php echo $this->get_var('link_dispositivi'); ?>"> <em class="icon-plus-sign-alt"></em> Dispositivi Medici </a></li>                 
                
				 <li class="panel<?php if ($this->get_var('panel_active')=='files') echo ' active';?>"> <a href="<?php echo $this->get_var('link_files'); ?>"> <em class="icon-file"></em> Files </a></li>
                <li class="panel<?php if ($this->get_var('panel_active')=='taccuino') echo ' active';?>"> <a href="<?php echo $this->get_var('link_taccuino'); ?>"> <em class="icon-book"></em> Taccuino Paziente </a></li>
				<!--diario visite deve diventare diario paziente-->
                <li class="panel<?php if ($this->get_var('panel_active')=='cproviders') echo ' active';?>"> <a href="<?php echo $this->get_var('link_cproviders'); ?>"> <em class="icon-user-md"></em> Care Providers </a></li>
                <li class="panel<?php if ($this->get_var('panel_active')=='calc') echo ' active';?>"> <a href="<?php echo $this->get_var('link_calc'); ?>"> <em class="icon-keyboard"></em> Calcolatrice Medica </a></li>
                <li class="panel<?php if ($this->get_var('panel_active')=='utility') echo ' active';?>"> <a href="<?php echo $this->get_var('link_utility'); ?>"> <em class="icon-tag"></em> Links </a></li><!--N.B nel testo si è sostituito 'Utility' con 'Links?-->
            </ul>

        </div>
<!--END MENU SECTION -->
<script>
window.onload = function() {
	//var anamnesiArrowLink = document.getElementById('anamnesiArrowLink');
	var anamnesiArrow = document.getElementById('anamnesiArrow');
	
	var anamnesiUl = document.getElementById('component-nav');
	
	var diagnosticUl = document.getElementById('form-nav');
	
	
	if(anamnesiUl.className == "in"){
		anamnesiArrow.className = "";
		anamnesiArrow.className = "icon-angle-down";
	}
	
	if(diagnosticUl.className == "in"){
		diagnosticArrow.className = "";
		diagnosticArrow.className = "icon-angle-down";
	}
};


var anamnesiArrow = document.getElementById('anamnesiArrow');
var anamnesiArrowLink = document.getElementById('anamnesiArrowLink');

anamnesiArrowLink.addEventListener('click', function() {
	diagnosticArrowLink.className = "";
	diagnosticArrowLink.className = "accordion-toggle collapsed";
	diagnosticArrow.className = "";
	diagnosticArrow.className = "icon-angle-left";
	
    if(anamnesiArrow.className == "icon-angle-left"){
		anamnesiArrow.className = "";
		anamnesiArrow.className = "icon-angle-down";
	}else{
		anamnesiArrow.className = "";
		anamnesiArrow.className = "icon-angle-left";
	}
});

var diagnosticArrow = document.getElementById('diagnosticArrow');
var diagnosticArrowLink = document.getElementById('diagnosticArrowLink');

diagnosticArrowLink.addEventListener('click', function() {
	anamnesiArrowLink.className = "";
	anamnesiArrowLink.className = "accordion-toggle collapsed";
	anamnesiArrow.className = "";
	anamnesiArrow.className = "icon-angle-left";
	
    if(diagnosticArrow.className == "icon-angle-left"){
		diagnosticArrow.className = "";
		diagnosticArrow.className = "icon-angle-down";
	}else{
		diagnosticArrow.className = "";
		diagnosticArrow.className = "icon-angle-left";
	}
});

var anamnesi = document.getElementsByClassName("anamnesi");

for(var i=0; i<anamnesi.length; i++){
        anamnesi[i].addEventListener('click', function() {
	anamnesiArrow.className = "";
	anamnesiArrow.className = "icon-angle-down";
	}, false);
}

var diagnostic = document.getElementsByClassName("anamnesi");

for(var i=0; i<diagnostic.length; i++){
        diagnostic[i].addEventListener('click', function() {
	diagnosticArrow.className = "";
	diagnosticArrow.className = "icon-angle-down";
	}, false);
}
</script>
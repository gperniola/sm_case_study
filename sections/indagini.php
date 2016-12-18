<?php

$pag_indagini= new templatemanager('templates/');
//$pag_indagini->set_var('','');
$var = $_POST['a'];
$pag_indagini->set_var('ciao', $var);
$pag_indagini->out('template_page_indagini');
?>
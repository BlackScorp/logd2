<?php
// translator ready
// addnews ready
// mail ready

function increment_specialty($colorcode, $spec=false){
	global $session;
	if ($spec !== false) {
		$revertspec = $session['user']['specialty'];
		$session['user']['specialty'] = $spec;
	}
	Translator::tlschema("skills");
	if ($session['user']['specialty']!=""){
		$specialties = Modules::modulehook("incrementspecialty",
				array("color"=>$colorcode));
	}else{
		OutputClass::output("`7You have no direction in the world, you should rest and make some important decisions about your life.`0`n");
	}
	Translator::tlschema();
	if ($spec !== false) {
		$session['user']['specialty'] = $revertspec;
	}
}
?>
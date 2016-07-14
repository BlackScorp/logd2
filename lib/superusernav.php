<?php
// translator ready
// addnews ready
// mail ready
function superusernav()
{
	global $SCRIPT_NAME, $session;
	Translator::tlschema("nav");
	OutputClass::addnav("Navigation");
	if ($session['user']['superuser'] &~ SU_DOESNT_GIVE_GROTTO) {
		$script = substr($SCRIPT_NAME,0,strpos($SCRIPT_NAME,"."));
		if ($script != "superuser") {
			$args = modulehook("grottonav");
			if (!array_key_exists('handled',$args) || !$args['handled']) {
				OutputClass::addnav("G?Return to the Grotto", "superuser.php");
			}
		}
	}
	$args = modulehook("mundanenav");
	if (!array_key_exists('handled',$args) || !$args['handled'])
		OutputClass::addnav("M?Return to the Mundane", "village.php");
	Translator::tlschema();
}
?>

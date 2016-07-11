<?php
// translator ready
// addnews ready
// mail ready
function getmountname()
{
	global $playermount;
	Translator::tlschema("mountname");
	$name = '';
	$lcname = '';
	if (isset($playermount['mountname'])) {
		$name = sprintf_translate("Your %s", $playermount['mountname']);
		$lcname = sprintf_translate("your %s", $playermount['mountname']);
	}
	Translator::tlschema();
	if (isset($playermount['newname']) && $playermount['newname'] != "") {
		$name = $playermount['newname'];
		$lcname = $playermount['newname'];
	}
	return array($name, $lcname);
}
?>

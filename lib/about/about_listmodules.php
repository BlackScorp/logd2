<?php
/**
 * Page displaying active modules
 * 
 * This page is part of the about system
 * and displays the name, version, author
 * and download location of all the active
 * modules on the server. Modules are sorted
 * by category, and are displayed in a table.
 * 
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2009, Dragonprime Development Team
 * @version Lotgd 1.1.2 DragonPrime Edition
 * @package Core
 * @subpackage Library
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
OutputClass::addnav("About LoGD");
OutputClass::addnav("About LoGD","about.php");
OutputClass::addnav("Game Setup Info","about.php?op=setup");
OutputClass::addnav("License Info", "about.php?op=license");
$sql = "SELECT * from " . db_prefix("modules") . " WHERE active=1 ORDER BY category,formalname";
$result = db_query($sql);
$mname = Translator::translate_inline("Module Name");
$mver = Translator::translate_inline("Version");
$mauth = Translator::translate_inline("Module Author");
$mdown = Translator::translate_inline("Download Location");
OutputClass::rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>",true);
OutputClass::rawoutput("<tr class='trhead'><td>$mname</td><td>$mver</td><td>$mauth</td><td>$mdown</td></tr>",true);
if (db_num_rows($result) == 0) {
	OutputClass::rawoutput("<tr class='trlight'><td colspan='4' align='center'>");
	OutputClass::output("`i-- No modules installed --`i");
	OutputClass::rawoutput("</td></tr>");
}
$cat = "";
$i=0;
while ($row = db_fetch_assoc($result)) {
	$i++;
	if ($cat != $row['category']) {
		OutputClass::rawoutput("<tr class='trhead'><td colspan='4' align='left'>");
		OutputClass::output($row['category']);
		OutputClass::rawoutput(":</td></tr>");
		$cat = $row['category'];
	}

	OutputClass::rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
	OutputClass::rawoutput("<td valign='top'>");
	OutputClass::output_notl("`&%s`0", $row['formalname']);
	OutputClass::rawoutput("<td valign='top'>",true);
	OutputClass::output_notl("`^%s`0", $row['version']);
	OutputClass::rawoutput("</td><td valign='top'>");
	OutputClass::output_notl("`^%s`0", $row['moduleauthor'], true);
	OutputClass::rawoutput("</td><td nowrap valign='top'>");
	if ($row['download'] == "core_module") {
		OutputClass::rawoutput("<a href='http://dragonprime.net/index.php?module=Downloads;catd=4' target='_blank'>");
		OutputClass::output("Core Distribution");
		OutputClass::rawoutput("</a>");
	} elseif ($row['download']) {
		// We should check all legeal protocols
		$protocols = array("http","https","ftp","ftps");
		$protocol = explode(":",$row['download'],2);
		$protocol = $protocol[0];
		// This will take care of download strings such as: not publically released or contact admin
		if (!in_array($protocol,$protocols)){
			OutputClass::output("`\$Contact Admin for Release");
		}else{
			OutputClass::rawoutput("<a href='{$row['download']}' target='_blank'>");
			OutputClass::output("Download");
			OutputClass::rawoutput("</a>");
		}
	} else {
		OutputClass::output("`\$Not publically released.`0");
	}
	OutputClass::rawoutput("</td>");
	OutputClass::rawoutput("</tr>");
}
OutputClass::rawoutput("</table>");
?>
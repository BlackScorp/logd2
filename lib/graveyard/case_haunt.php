<?php
OutputClass::output("`\$%s`) is impressed with your actions, and grants you the power to haunt a foe.`n`n",$deathoverlord);
$search = Translator::translate_inline("Search");
OutputClass::rawoutput("<form action='graveyard.php?op=haunt2' method='POST'>");
OutputClass::addnav("","graveyard.php?op=haunt2");
OutputClass::output("Who would you like to haunt? ");
OutputClass::rawoutput("<input name='name' id='name'>");
OutputClass::rawoutput("<input type='submit' class='button' value='$search'>");
OutputClass::rawoutput("</form>");
OutputClass::rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");
OutputClass::addnav("Places");
OutputClass::addnav("S?Land of the Shades","shades.php");
OutputClass::addnav("G?The Graveyard","graveyard.php");
OutputClass::addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
?>
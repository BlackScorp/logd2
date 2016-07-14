<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/nltoappon.php");
require_once("lib/commentary.php");
require_once("lib/systemmail.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");

Translator::tlschema("clans");


OutputClass::addnav("Village");
villagenav();
OutputClass::addnav("Clan Options");
OutputClass::addnav("C?List Clans","clan.php?op=list");
Commentary::addcommentary();
$gold = Settings::getsetting("goldtostartclan",10000);
$gems = Settings::getsetting("gemstostartclan",15);
$ranks = array(CLAN_APPLICANT=>"`!Applicant`0",CLAN_MEMBER=>"`#Member`0",CLAN_OFFICER=>"`^Officer`0",CLAN_LEADER=>"`&Leader`0", CLAN_FOUNDER=>"`\$Founder");
$args = Modules::modulehook("clanranks", array("ranks"=>$ranks, "clanid"=>$session['user']['clanid']));
$ranks = Translator::translate_inline($args['ranks']);

$apply_short = "`@Clan App: `&%s`0";
$apply_subj = array($apply_short, $session['user']['name']);

$op = Http::httpget('op');

$detail = Http::httpget('detail');
if ($detail>0){
	require_once("lib/clan/detail.php");
}elseif ($op=="list"){
	require_once("lib/clan/list.php");
} elseif ($op == "waiting") {
	require_once("lib/clan/waiting.php");
}elseif ($session['user']['clanrank']==CLAN_APPLICANT){
	require_once("lib/clan/applicant.php");
}else{
	require_once("lib/clan/clan_start.php");
}


PageParts::page_footer();

function clanform(){
	OutputClass::rawoutput("<form action='clan.php?op=new&apply=1' method='POST'>");
	OutputClass::addnav("","clan.php?op=new&apply=1");
	OutputClass::output("`b`cNew Clan Application Form`c`b");
	OutputClass::output("Clan Name: ");
	OutputClass::rawoutput("<input name='clanname' maxlength='50' value=\"".htmlentities(stripslashes(httppost('clanname')), ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."\">");
	OutputClass::output("`nShort Name: ");
	OutputClass::rawoutput("<input name='clanshort' maxlength='5' size='5' value=\"".htmlentities(stripslashes(httppost('clanshort')), ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."\">");
	OutputClass::output("`nNote, color codes are permitted in neither clan names nor short names.");
	OutputClass::output("The clan name is shown on player bios and on clan overview pages while the short name is displayed next to players' names in comment areas and such.`n");
	$apply = Translator::translate_inline("Apply");
	OutputClass::rawoutput("<input type='submit' class='button' value='$apply'></form>");
}
?>
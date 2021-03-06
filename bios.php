<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/systemmail.php");
require_once("lib/http.php");

Translator::tlschema("bio");
SuAccess::check_su_access(SU_EDIT_COMMENTS);

$op = Http::httpget('op');
$userid = Http::httpget('userid');
if ($op=="block"){
	$sql = "UPDATE " . db_prefix("accounts") . " SET bio='`iBlocked for inappropriate usage`i',biotime='9999-12-31 23:59:59' WHERE acctid='$userid'";
	$subj = array("Your bio has been blocked");
	$msg = array("The system administrators have decided that your bio entry is inappropriate, so it has been blocked.`n`nIf you wish to appeal this decision, you may do so with the petition link.");
	SystemMailClass::systemmail($userid, $subj, $msg);
	db_query($sql);
}
if ($op=="unblock"){
	$sql = "UPDATE " . db_prefix("accounts") . " SET bio='',biotime='0000-00-00 00:00:00' WHERE acctid='$userid'";
	$subj = array("Your bio has been unblocked");
	$msg = array("The system administrators have decided to unblock your bio.  You can once again enter a bio entry.");
	SystemMailClass::systemmail($userid,$subj,$msg);
	db_query($sql);
}
$sql = "SELECT name,acctid,bio,biotime FROM " . db_prefix("accounts") . " WHERE biotime<'9999-12-31' AND bio>'' ORDER BY biotime DESC LIMIT 100";
$result = db_query($sql);
PageParts::page_header("User Bios");
$block = Translator::translate_inline("Block");
OutputClass::output("`b`&Player Bios:`0`b`n");
$number=db_num_rows($result);
for ($i=0;$i<$number;$i++){
	$row = db_fetch_assoc($result);
	if ($row['biotime']>$session['user']['recentcomments'])
		OutputClass::rawoutput("<img src='images/new.gif' alt='&gt;' width='3' height='5' align='absmiddle'> ");
	OutputClass::output_notl("`![<a href='bios.php?op=block&userid={$row['acctid']}'>$block</a>]",true);
	OutputClass::addnav("","bios.php?op=block&userid={$row['acctid']}");
	OutputClass::output_notl("`&%s`0: `^%s`0`n", $row['name'], Censor::soap($row['bio']));
}
db_free_result($result);
require_once("lib/superusernav.php");
SuperUserNavClass::superusernav();

OutputClass::addnav("Moderation");

if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
	OutputClass::addnav("Return to Comment Moderation","moderate.php");

OutputClass::addnav("Refresh","bios.php");
$sql = "SELECT name,acctid,bio,biotime FROM " . db_prefix("accounts") . " WHERE biotime>'9000-01-01' AND bio>'' ORDER BY biotime DESC LIMIT 100";
$result = db_query($sql);
OutputClass::output("`n`n`b`&Blocked Bios:`0`b`n");
$unblock = Translator::translate_inline("Unblock");
$number=db_num_rows($result);
for ($i=0;$i<$number;$i++){
	$row = db_fetch_assoc($result);

	OutputClass::output_notl("`![<a href='bios.php?op=unblock&userid={$row['acctid']}'>$unblock</a>]",true);
	OutputClass::addnav("","bios.php?op=unblock&userid={$row['acctid']}");
	OutputClass::output_notl("`&%s`0: `^%s`0`n", $row['name'], Censor::soap($row['bio']));
}
db_free_result($result);
PageParts::page_footer();
?>
<?php
require_once("lib/installer/installer_functions.php");
if (httppostisset("DB_PREFIX") > ""){
	$session['dbinfo']['DB_PREFIX'] = Http::httppost("DB_PREFIX");
}
if ($session['dbinfo']['DB_PREFIX'] > "" && substr($session['dbinfo']['DB_PREFIX'],-1)!="_")
$session['dbinfo']['DB_PREFIX'] .= "_";

$descriptors = descriptors($session['dbinfo']['DB_PREFIX']);
$unique=0;
$game=0;
$missing=0;
$conflict = array();

$link = mysqli_connect($session['dbinfo']['DB_HOST'],$session['dbinfo']['DB_USER'],$session['dbinfo']['DB_PASS']);
mysqli_select_db($link,$session['dbinfo']['DB_NAME']);
$sql = "SHOW TABLES";
$result = mysqli_query($link,$sql);
while ($row = mysqli_fetch_assoc($result)){
	list($key,$val)=each($row);
	if (isset($descriptors[$val])){
		$game++;
		array_push($conflict,$val);
	}else{
		$unique++;
	}
}
$missing = count($descriptors)-$game;
if ($missing*10 < $game){
	//looks like an upgrade
	$upgrade=true;
}else{
	$upgrade=false;
}
if (Http::httpget("type")=="install") $upgrade=false;
if (Http::httpget("type")=="upgrade") $upgrade=true;
$session['dbinfo']['upgrade']=$upgrade;
	if ($upgrade){
	OutputClass::output("`@This looks like a game upgrade.");
	OutputClass::output("`^If this is not an upgrade from a previous version of LoGD, <a href='installer.php?stage=5&type=install'>click here</a>.",true);
	OutputClass::output("`2Otherwise, continue on to the next step.");
}else{
	//looks like a clean install
	$upgrade=false;
	OutputClass::output("`@This looks like a fresh install.");
	OutputClass::output("`2If this is not a fresh install, but rather an upgrade from a previous version of LoGD, chances are that you installed LoGD with a table prefix.  If that's the case, enter the prefix below.  If you are still getting this message, it's possible that I'm just spooked by how few tables are common to the current version, and in which case, I can try an upgrade if you <a href='installer.php?stage=5&type=upgrade'>click here</a>.`n",true);
	if (count($conflict)>0){
		OutputClass::output("`n`n`\$There are table conflicts.`2");
		OutputClass::output("If you continue with an install, the following tables will be overwritten with the game's tables.  If the listed tables belong to LoGD, they will be upgraded, otherwise all existing data in those tables will be destroyed.  Once this is done, this cannot be undone unless you have a backup!`n");
		OutputClass::output("`nThese tables conflict: `^".join(", ",$conflict)."`2`n");
		if (Http::httpget("op")=="confirm_overwrite") $session['sure i want to overwrite the tables']=true;
		if (!$session['sure i want to overwrite the tables']){
			$session['stagecompleted']=4;
			OutputClass::output("`nIf you are sure that you wish to overwrite these tables, <a href='installer.php?stage=5&op=confirm_overwrite'>click here</a>.`n",true);
		}
	}
	OutputClass::output("`nYou can avoid table conflicts with other applications in the same database by providing a table name prefix.");
	OutputClass::output("This prefix will get put on the name of every table in the database.");
}
OutputClass::rawoutput("<form action='installer.php?stage=5' method='POST'>");
OutputClass::output("`nTo provide a table prefix, enter it here.");
OutputClass::output("If you don't know what this means, you should either leave it blank, or enter an intuitive value such as \"logd\".`n");
OutputClass::rawoutput("<input name='DB_PREFIX' value=\"".htmlentities($session['dbinfo']['DB_PREFIX'], ENT_COMPAT, Settings::getsetting("charset", "ISO-8859-1"))."\"><br>");
$submit = Translator::translate_inline("Submit your prefix.");
OutputClass::rawoutput("<input type='submit' value='$submit' class='button'>");
OutputClass::rawoutput("</form>");
if (count($conflict)==0){
	OutputClass::output("`^It looks like you can probably safely skip this step if you don't know what it means.");
}
OutputClass::output("`n`n`@Once you have submitted your prefix, you will be returned to this page to select the next step.");
OutputClass::output("If you don't need a prefix, just select the next step now.");
?>

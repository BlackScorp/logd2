<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");
require_once("lib/names.php");

Translator::tlschema("lodge");

addcommentary();

$op = Http::httpget('op');
if ($op == "") checkday();

$pointsavailable =
	$session['user']['donation']-$session['user']['donationspent'];
$entry = ($session['user']['donation'] > 0) || ($session['user']['superuser'] & SU_EDIT_COMMENTS);
if ($pointsavailable < 0) $pointsavailable = 0; // something weird.

PageParts::page_header("Hunter's Lodge");
addnav("Referrals", "referral.php");
if ($op != "" && $entry)
	addnav("L?Back to the Lodge", "lodge.php");
addnav("Describe Points","lodge.php?op=points");
villagenav();


if ($op==""){
	OutputClass::output("`b`c`!The Hunter's Lodge`0`c`b");
	OutputClass::output("`7You follow a narrow path away from the stables and come across a rustic Hunter's Lodge.");
	OutputClass::output("A guard stops you at the door and asks to see your membership card.`n`n");

	if ($entry){
		OutputClass::output("Upon showing it to him, he says, `3\"Very good %s, welcome to the J. C. Petersen Hunting Lodge.", Translator::translate_inline($session['user']['sex']?"ma'am":"sir"));
		OutputClass::output("You have earned `^%s`3 points and have `^%s`3 points available to spend,\"`7 and admits you in.`n`n", $session['user']['donation'], $pointsavailable);
		OutputClass::output("You enter a room dominated by a large fireplace at the far end.");
		OutputClass::output("The wood-panelled walls are covered with weapons, shields, and mounted hunting trophies, including the heads of several dragons that seem to move in the flickering light.`n`n");
		OutputClass::output("Many high-backed leather chairs fill the room.");
		OutputClass::output("In the chair closest to the fire sits J. C. Petersen, reading a heavy tome entitled \"Alchemy Today.\"`n`n");
		OutputClass::output("As you approach, a large hunting dog at his feet raises her head and looks at you.");
		OutputClass::output("Sensing that you belong, she lays down and goes back to sleep.`n`n");
		commentdisplay("Nearby some other rugged hunters talk:`n", "hunterlodge","Talk quietly",25);
		addnav("Use Points");
		modulehook("lodge");
	}else{
		$iname = Settings::getsetting("innname", LOCATION_INN);
		OutputClass::output("You pull out your Frequent Boozer Card from %s, with 9 out of the 10 slots punched out with a small profile of %s`0's Head.`n`n", $iname,Settings::getsetting('barkeep','`tCedrik'));
		OutputClass::output("The guard glances at it, advises you not to drink so much, and directs you down the path.");
	}
}else if ($op=="points"){
	OutputClass::output("`b`3Points:`b`n`n");
	$points_messages = modulehook(
		"donator_point_messages",
		array(
			'messages'=>array(
				'default'=>tl("`7For each $1 donated, the account which makes the donation will receive 100 contributor points in the game.")
			)
		)
	);
	foreach($points_messages['messages'] as $id => $message){
		output_notl($message, true);
	}
	OutputClass::output("\"`&But what are points,`7\" you ask?");
	OutputClass::output("Points can be redeemed for various advantages in the game.");
	OutputClass::output("You'll find access to these advantages in the Hunter's Lodge.");
	OutputClass::output("As time goes on, more advantages will likely be added, which can be purchased when they are made available.`n`n");
	OutputClass::output("Donating even one dollar will gain you a membership card to the Hunter's Lodge, an area reserved exclusively for contributors.");
	OutputClass::output("Donations are accepted in whole dollar increments only.`n`n");
	OutputClass::output("\"`&But I don't have access to a PayPal account, or I otherwise can't donate to your very wonderful project!`7\"`n");
           // yes, "referer" is misspelt here, but the game setting was also misspelt
	if (Settings::getsetting("refereraward", 25)) {
		OutputClass::output("Well, there is another way that you can obtain points: by referring other people to our site!");
		OutputClass::output("You'll get %s points for each person whom you've referred who makes it to level %s.", Settings::getsetting("refereraward", 25), Settings::getsetting("referminlevel", 4));
		OutputClass::output("Even one person making it to level %s will gain you access to the Hunter's Lodge.`n`n", Settings::getsetting("referminlevel", 4));
	}
	OutputClass::output("You can also gain contributor points for contributing in other ways that the administration may specify.");
	OutputClass::output("So, don't despair if you cannot send cash, there will always be non-cash ways of gaining contributor points.`n`n");
	OutputClass::output("`b`3Purchases that are currently available:`0`b`n");
	$args = modulehook("pointsdesc", array("format"=>"`#&#149;`7 %s`n", "count"=>0));
	if ($args['count'] == 0) {
		OutputClass::output("`#&#149;`7None -- Please talk to your admin about creating some.`n", true);
	}
}

page_footer();
?>
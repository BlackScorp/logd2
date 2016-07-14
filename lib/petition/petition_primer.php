<?php
Translator::tlschema("faq");
popup_header("New Player Primer");
$c = translate_inline("Return to Contents");
rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
OutputClass::output("`n`n`^Welcome to the Legend of the Green Dragon New Player Primer`n`n");
OutputClass::output("`^`bThe village square`b`n");
OutputClass::output("`@Legend of the Green Dragon (LotGD) is turning out to be a fairly expansive game, with a lot of areas to explore.");
OutputClass::output("It's easy to get lost with all that there is to do out there, so keep in mind that the village square is pretty much the center of the village you start in.");
OutputClass::output("This area will give you access to most other areas that you can get to, with a few exceptions (we'll talk about those in a little while).");
OutputClass::output("If you ever get lost, or are not sure what's going on, head to the village square and regain your bearings.`n`n");
OutputClass::output("`^`bYour first day`b`n");
OutputClass::output("`@Your first day in the world can be very confusing!");
OutputClass::output("You're presented with a lot of information, and you don't need almost any of it!  It's true!");
OutputClass::output("One thing you should probably keep an eye on though, are your hit points.  This is found under \"Vital Info.\"");
OutputClass::output("No matter what profession you choose, in the end, you are some kind of warrior or fighter, and so you need to learn how to do battle.");
OutputClass::output("The best way to do this is to look for creatures to kill in the forest.");
OutputClass::output("When you find one, check it out, and make sure that it's not a higher level than you, because if it is, you might not live through the fight.");
OutputClass::output("Keep in mind that you can always try to run away from something that you encountered, but sometimes it might take several tries before you get away.");
OutputClass::output("You might want to buy armor and weapons in the village square in order to give yourself a better chance against these creatures out in the forest.`n`n");
OutputClass::output("Once you have defeated a creature, you'll notice that you're probably a little hurt.");
OutputClass::output("Head on over to the Healer's Hut, and you can get patched up in short order.");
OutputClass::output("While you're level 1, healing is free, but as you advance, it becomes more and more expensive.");
OutputClass::output("Also keep in mind that it's more expensive to heal 1 point, then later heal 1 point again than it is to heal 2 in one shot.");
OutputClass::output("So if you're trying to save up some money, and you're barely hurt, you might risk a fight or two while you're a little hurt, and heal the damage from several fights in one shot.`n`n");
OutputClass::output("After you've killed a few creatures, you should head back to the village, into Bluspring's Warrior Training, and talk to your master.");
OutputClass::output("Your master will tell you when you are ready to challenge him, and when you are ready, you should give him a shot (make sure you're healed up first though!).");
OutputClass::output("Your master won't kill you if you lose, instead he'll give you a complimentary healing potion and send you on your way.");
if (Settings::getsetting("multimaster",1) == 0) {
	OutputClass::output(" You can only challenge your master once a day.");
}
OutputClass::output("`n`n`^`bDeath`b`n");
OutputClass::output("`@Death is a natural part of any game that contains some kind of combat.");
OutputClass::output("In Legend of the Green Dragon, being dead is only a temporary condition.");
OutputClass::output("When you die, you'll lose any money that you had on hand (money in the bank is safe!), and some of the experience you've accumulated.");
OutputClass::output("While you're dead, you can explore the land of the shades and the graveyard.");
OutputClass::output("In the graveyard, you'll find %s`@ the Overlord of Death.",Settings::getsetting('deathoverlord','`$Ramius'));
OutputClass::output("He has certain things that he would like you to do for him, and in return, he may grant you special powers or favors.");
OutputClass::output("The graveyard is one of those areas that you can't get to from the main Square.");
OutputClass::output("In fact, while you're dead, you can't go to the village square at all!`n`n");
OutputClass::output("Unless you can convince %s`@ to resurrect you, you'll remain dead until the next game day.",Settings::getsetting('deathoverlord','`$Ramius'));
OutputClass::output("There are %s game days each real day.", Settings::getsetting("daysperday", 2));
OutputClass::output("These occur when the clock in the village square reaches midnight.`n`n");
OutputClass::output("`^`bNew Days`b`n");
OutputClass::output("`@As stated just above, there are %s game days each real day.", Settings::getsetting("daysperday", 2));
OutputClass::output("These occur when the clock in the village square reaches midnight.");
OutputClass::output("When you get a new day, you'll be granted new forest fights, interest on gold you have in the bank (if the bankers are pleased with your performance!), and a lot of your other statistics will be refreshed.");
OutputClass::output("You'll also be resurrected if you were dead, and get another chance to take on the world.");
OutputClass::output("If you don't log on over the course of an entire game day, you'll miss your opportunity to partake in that game day (this means that new game days are only assigned when you actually log on, being away from the game for a few days won't grant you a whole bunch of new days).");
OutputClass::output("Forest fights, PvP battles, special power usages and other things that get refreshed on a daily basis do NOT get carried over from one day to the next (you can't build up a whole bunch of them).`n`n");
if (Settings::getsetting("pvp",1)){
	OutputClass::output("`^`bPvP (Player versus Player)`b`n");
	OutputClass::output("`@Legend of the Green Dragon contains a PvP element, where players can attack each other.");
	OutputClass::output("As a new player, you are protected from PvP for your first %s game days or until you accumulate %s experience, unless you choose to attack another player.", Settings::getsetting("pvpimmunity", 5), Settings::getsetting("pvpminexp", 1500));
	OutputClass::output("Some servers might have the PvP aspect turned off, in which case there is no chance that you'll be attacked by any other players.");
	OutputClass::output("You can tell if the server you play on has PvP turned off by looking in the village square for \"Slay Other Players.\"");
	OutputClass::output("If it's not there, you can't engage (or be engaged) in PvP.`n`n");
	OutputClass::output("When you are attacked and die in PvP, you only lose gold you had on hand, and %s%% of your experience.", Settings::getsetting("pvpdeflose", 5));
	OutputClass::output("You won't lose any turns in the forest, or any other stats.");
	OutputClass::output("If you attack someone else in PvP, you'll get %s%% of the experience they had, and any gold they had on hand.", Settings::getsetting("pvpattgain", 10));
	OutputClass::output("If you attack someone else and lose, however, you'll lose %s%% of your experience, and you'll lose any gold that you had on hand.", Settings::getsetting("pvpattlose", 15));
	OutputClass::output("If someone else attacks you and they lose, you'll gain the gold they had on hand, and %s%% of their experience.", Settings::getsetting("pvpdefgain", 10));
	OutputClass::output("You can only attack someone who is close to your level, so don't worry that as a level 1, some big level 15 player is going to come along and beat on you.`n`n");
	OutputClass::output("If you buy a room in the inn when you decide to quit the game, you'll protect yourself somewhat from casual attacking.");
	OutputClass::output("The only way for someone to attack you when you're in the inn is for them to bribe the bartender, which can be a costly procedure.");
	OutputClass::output("Quitting to the fields means that someone can attack you without having to pay money or gems to the bartender.");
	OutputClass::output("You cannot be attacked while you are online, only while you are offline, so the more  you play, the more protected you are ;-).");
	OutputClass::output("Also, if you are attacked and die, no one else can attack you again until you log on again, so don't worry that you'll be attacked 30 or 40 times in one night.");
	OutputClass::output("Logging back into the game will make you a viable PvP target again if you've already been killed today.`n`n");
}
OutputClass::output("`^`bReady to take on the world!`b`n");
OutputClass::output("`@You should now have a pretty good idea of how the basics of the game work, how to advance, and how to protect yourself.");
OutputClass::output("There's a whole lot more to the world, so explore it!");
OutputClass::output("Don't be afraid of dying, particularly when you're young, as even when you're dead, there's yet more stuff to do!");
rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
?>
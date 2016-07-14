<?php
Translator::tlschema("faq");
popup_header("Specific and Technical Questions");
$c = Translator::translate_inline("Return to Contents");
OutputClass::rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
OutputClass::output("`n`n`c`bSpecific and technical questions`b`c`n");
OutputClass::output("`^1.a. How can I have been killed by another player while I was currently playing?`n");
OutputClass::output("`@The biggest cause of this is someone who began attacking you while you were offline, and completed the fight while you were online.");
OutputClass::output("This can even happen if you have been playing nonstop for the last hour.");
OutputClass::output("When someone starts a fight, they are forced by the game to finish it at some point.");
OutputClass::output("If they start a fight with you, and close their browser, the next time they log on, they will have to finish the fight.");
OutputClass::output("You will lose the lesser of the gold you had on hand when they attacked you, or the gold on hand when they finished the fight.");
OutputClass::output("So if you logged out with 1 gold on hand, they attack you, you log on, accumulate 2000 gold on hand, and they complete the fight, they will only come away from it with 1 gold.");
OutputClass::output("The same is true if you logged out with 2000 gold, and when they completed killing you, you only had 1 gold.`n`n");
OutputClass::output("`^1.b. Why did it say I was killed in the fields when I slept in the inn?`n");
OutputClass::output("`@The same thing can happen where someone started attacking you when you were in the fields, and finished after you had retired to the inn for the day.");
OutputClass::output("Keep in mind that if you are idle on the game for too long, you become a valid target for others to attack you in the fields.");
OutputClass::output("If you're going to go away from your computer for a few minutes, it's a good idea to head to the inn for your room first so that you don't risk someone attacking you while you're idle.`n`n");
OutputClass::output("`^2. The game tells me that I'm not accepting cookies, what are they and what do I do?`n");
OutputClass::output("`@Cookies are little bits of data that websites store on your computer so they can distinguish you from other players.");
OutputClass::output("Sometimes if you have a firewall it will block cookies, and some web browsers will let you block cookies.");
OutputClass::output("Check the documentation for your browser or firewall, or look around in its preferences for settings to modify whether or not you accept cookies.");
OutputClass::output("You need to at least accept session cookies to play the game, though all cookies are better.`n`n");
OutputClass::output("`^3. What do`n&nbsp;&nbsp;`iWarning: mysql_pconnect(): Lost connection to MySQL server during query in /home/lotgd/public_html/dbwrapper.php on line 82`i`nand`n&nbsp;&nbsp;`iWarning: mysql_error(): supplied argument is not a valid MySQL-Link resource in /home/lotgd/public_html/dbwrapper.php on line 54`i`nmean?`n", true);
OutputClass::output("`@It's a secret message from your computer telling you to stop staring at a screen and to go play outside.`n");
OutputClass::output("Actually, it's a common temporary error, usually having to do with server load.");
OutputClass::output("Don't worry about it, just reload the page (it may take a few tries).`n`n");
OutputClass::output("`^4. Nothing is responding for hours now - what should I do ?`n");
OutputClass::output("`@Go outside play a bit in Real Life (tm). When you get back it will work again - if not it's a serious problem.");
OutputClass::output("Any server problems are caught less then 5 minutes after occurring, so if there is a problem, it's known - and we are working on it.");
OutputClass::output("Every mail and ye olde mail reporting the same problem is just making it harder for us to work.`n`n");
OutputClass::output("`^5. Why is the site giving me so many popups?`n");
OutputClass::output("`@Please turn off your popup blocker. These aren't ads.`n");
OutputClass::output("We use popup windows in the game for the following purposes:`n");
OutputClass::output("a) To file a petition.`n");
OutputClass::output("b) To write and receive Ye Olde Mail.`n");
OutputClass::output("c) To make sure you see our newest Message of the Day (MoTD).`n");
OutputClass::output("That last one is very important, since until you've viewed it the window will continue to try to open on every page load. These messages are for server announcements such as outages, current known bugs (which you really don't have to petition about, since we already know of them), and other things that the staff think you need to know about right away.`n`n");
OutputClass::rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
?>
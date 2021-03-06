<?php
// translator ready
// addnews ready
// mail ready
class VillageNavClass
{
    public static function villagenav($extra = false)
    {
        global $session;
        $loc = $session['user']['location'];
        if ($extra === false) {
            $extra = "";
        }
        $args = Modules::modulehook("villagenav");
        if (array_key_exists('handled', $args) && $args['handled']) {
            return;
        }
        Translator::tlschema("nav");
        if ($session['user']['alive']) {
            OutputClass::addnav(array("V?Return to %s", $loc), "village.php$extra");
        } else {
            // user is dead
            OutputClass::addnav("S?Return to the Shades", "shades.php");
        }
        Translator::tlschema();
    }

}
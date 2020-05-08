<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

includeAllFile('lib/methods/*.php');
require_once('lib/VimeAPI-Utils.php');

function includeAllFile($path) {
    foreach (glob($path) as $filename)
    {
        require_once $filename;
    }
}

class VimeAPI_Include extends VimeAPI_Utils {
    use giveVimers;

    use changePassword;
    use disableTwoFA;
    use enableTwoFA;

    use getVimers;
    use getOperationsHistory;
    use getInformations;
}
?>
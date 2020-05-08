<?php
define('Grusha-VimeAPI', true);
require('VimeAPI.class.php');

$VimeAPI = new VimeAPI('XXXX', 'XXXX');

$res = $VimeAPI->getOperationsHistory();
var_dump ($res->data);
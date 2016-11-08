<?php
require_once 'MegaFilterClientFactory.php';

$client = MegaFilterClientFactory::getInstance('ttdbl');

$client->setDebug(true);
$result = $client->textCheck('448', 'mega', '吴国兄弟加我微信刷元宝13915039261');

var_dump($result);

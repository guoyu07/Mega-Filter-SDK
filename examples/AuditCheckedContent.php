<?php
require "vendor/autoload.php";

$client = MegaFilterClientFactory::getInstance('ttdbl');

try
{
    $data = $client->auditCheckedContent(1, \MegaFilter\MegaFilterClient::ADVERTISING_CHECKED_TYPE);

    //返回code为200时代表成功,其余都代表失败。
    if($data['code'] === 200) {
        print_r($data['result']);
        echo PHP_EOL;
    } else {
        echo "code:{$data['code']} err_msg: {$data['err_msg']}" . PHP_EOL;
    }

} catch (\MegaFilter\ApiRequestException $e) {
    echo $e->getCode(), ' ' ,$e->getMessage(), PHP_EOL;
}


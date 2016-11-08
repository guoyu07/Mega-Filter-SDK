<?php
require "../vendor/autoload.php";

$client = MegaFilterClientFactory::getInstance('ttdbl');

//$client->setDebug(true);

try
{
    $data = $client->textCheck('448', 'mega', 'mega-filter高性能内容过滤服务');

    //返回code为200时代表成功,其余都代表失败。
    if($data['code'] === 200) {
        print_r($data['result']);
    } else {
        echo "code:{$data['code']} err_msg: {$data['err_msg']}" . PHP_EOL;
    }

} catch (\MegaFilter\ApiRequestException $e) {
    echo $e->getCode(), ' ' ,$e->getMessage(), PHP_EOL;
}


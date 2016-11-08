<?php
namespace MegaFilter;

class MegaFilterClient
{
    private $api;

    function __construct($host, $project, $token)
    {
        $this->api = new MegaFilterApi($host, $project, $token);
    }

    /**
     * 开启调试信息
     *
     * 开启调试信息后，SDK会将每次请求微博API所发送的POST Data、Headers以及请求信息、返回内容输出出来。
     *
     * @access public
     * @param bool $enable 是否开启调试信息
     * @return void
     */
    public function setDebug($enable)
    {
        $this->api->debug = $enable;
    }

    public function textCheck($uid, $name, $content, $replacement = '*')
    {
        $params = [
            'uid' => $uid,
            'name' => $name,
            'content' => $content,
            'replacement' => $replacement
        ];

        return $this->api->post('text/check', $params);
    }
}
<?php
namespace MegaFilter;

class MegaFilterClient
{
    private $api;

    // 检验类型: 0:全部类型, 1:正常，2：可疑，3：广告，4:非法内容，5:黑名单，6:刷屏
    const ALL_AI_TYPE = 0;
    const NORMAL_AI_TYPE = 1;
    const SUSPICION_AI_TYPE = 2;
    const ADVERTISING_AI_TYPE = 3;
    const ILLEGAL_AI_TYPE = 4;
    const BAD_GUYS_AI_TYPE = 5;
    const REPEAT_AI_TYPE = 6;

    // 人工审核类型： 0:未处理，1:不是广告，2:是广告
    const NON_CHECKED_TYPE = 0;
    const HEALTH_CHECKED_TYPE = 1;
    const ADVERTISING_CHECKED_TYPE = 2;

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

    /**
     * 检验文本API
     *
     * @param string|int $uid
     * @param string $name
     * @param string $content
     * @param string $app 应用标识
     * @param array $ext 额外参数
     * @param string $replacement
     * @return mixed
     */
    public function textCheck($uid, $name, $content, $app = null, $ext = null, $replacement = '*')
    {
        $params = [
            'uid' => $uid,
            'name' => $name,
            'content' => $content,
            'replacement' => $replacement
        ];

        if ($app !== null)
            $params['app'] = $app;

        if ($ext !== null)
            $params['ext'] = $ext;

        return $this->api->post('text/check', $params);
    }

    /**
     * 根据ID获取已检测内容API
     *
     * @param $id
     * @return mixed
     */
    public function getCheckedContent($id)
    {
        return $this->api->get("text/checked/{$id}");
    }

    /**
     * 获取已检测内容列表API
     *
     * @param int $type
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getCheckedContents($type = self::ALL_AI_TYPE, $page = 1, $limit = 30)
    {
        $params = [
            'page' => $page,
            'limit' => $limit,
            'type' => $type
        ];

        return $this->api->get('text/checked', $params);
    }

    /**
     * 审核已检测的内容API
     *
     * @param $id
     * @param $type
     * @return mixed
     */
    public function auditCheckedContent($id, $type)
    {
        $params = [
            'id' => $id,
            'type' => $type
        ];

        return $this->api->post('text/checked', $params);
    }

    /**
     * 获取所有关键字API
     *
     * @param int $page
     * @param int $limit
     * @return mixed|string
     */
    public function getKeywords($page = 1, $limit = 30)
    {
        $params = [
            'page' => $page,
            'limit' => $limit,
        ];
        return $this->api->get('keywords', $params);
    }

    /**
     * 添加关键字API
     *
     * @param array $keywords
     * @return mixed
     */
    public function addKeywords(array $keywords)
    {
        $params = [
            'keywords' => $keywords
        ];
        return $this->api->post('keywords', $params);
    }

    /**
     * 删除关键字API
     *
     * @param $keywords
     * @return mixed
     */
    public function deleteKeywords(array $keywords)
    {
        $params = [
            'keywords' => $keywords
        ];
        return $this->api->delete("keywords", $params);
    }

    /**
     * 获取关键字排名
     * @return mixed
     */
    public function getKeywordsRank()
    {
        return $this->api->get('keywords/rank');
    }

    /**
     * 获取单条广告API
     *
     * @param $id
     * @return mixed
     */
    public function getAdvertisement($id)
    {
        return $this->api->get("advertisements/{$id}");
    }

    /**
     * 获取广告列表API
     *
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getAdvertisements($page = 1, $limit = 30)
    {
        $params = [
            'page' => $page,
            'limit' => $limit,
        ];

        return $this->api->get('advertisements', $params);
    }

    /**
     * 列为广告API
     *
     * @param $content
     * @return mixed
     */
    public function addAdvertisement($content)
    {
        $params = [
            'content' => $content
        ];

        return $this->api->post('advertisements', $params);
    }

    /**
     * 删除广告API
     *
     * @param $id
     * @return mixed
     */
    public function deleteAdvertisement($id)
    {
        return $this->api->delete("advertisements/{$id}");
    }

    /**
     * 获取黑名单API
     *
     * @return mixed
     */
    public function getBadGuys()
    {
        return $this->api->get('bad-guys');
    }

    /**
     * 添加黑名单API
     *
     * @param $uid
     * @param $name
     * @param int $expiresIn
     * @return mixed
     */
    public function addBadGuy($uid, $name, $expiresIn = 0)
    {
        $params = [
            'guys' => [
                'uid' => $uid,
                'name' => $name,
                'expires_in' => $expiresIn
            ]
        ];

        return $this->api->post('bad-guys', $params);
    }

    /**
     * 批量添加黑名单API
     *
     * @param array $guys
     * @return mixed
     * @throws InvalidParamException
     */
    public function addBadGuys(array $guys)
    {
        $params = [
            'guys' => []
        ];

        foreach ($guys as $guy) {
            if (!isset($guy['uid']) || !isset($guy['name'])) {
                throw new InvalidParamException('invalid guys item');
            }

            $params['guys'][] = [
                'uid' => $guy['uid'],
                'name' => $guy['name'],
                'expires_in' => isset($guy['expires_in']) ? $guy['expires_in'] : 0
            ];
        }

        return $this->api->post('bad-guys', $params);
    }

    /**
     * 删除黑名单API
     *
     * @param $id
     * @return mixed
     */
    public function deleteBadGuy($id)
    {
        $params = [
            'guys' => [
                'uid' => $id
            ]
        ];

        return $this->api->post('bad-guys', $params);
    }

    /**
     * 批量删除黑名单API
     *
     * @param $ids
     * @return mixed
     */
    public function deleteBadGuys($ids)
    {
        $params = [
            'guys' => []
        ];

        foreach ($ids as $id) {
            $params['guys'][] = [
                'uid' => $id,
            ];
        }

        return $this->api->post('bad-guys', $params);
    }

    /**
     * 获取白名单API
     *
     * @return mixed
     */
    public function getGoodGuys()
    {
        return $this->api->get('good-guys');
    }

    /**
     * 添加白名单API
     *
     * @param $uid
     * @param $name
     * @return mixed
     */
    public function addGoodGuy($uid, $name)
    {
        $params = [
            'guys' => [
                'uid' => $uid,
                'name' => $name,
            ]
        ];

        return $this->api->post('good-guys', $params);
    }

    /**
     * 批量添加白名单API
     *
     *
     * @param $guys
     * @return mixed
     * @throws InvalidParamException
     */
    public function addGoodGuys($guys)
    {
        $params = [
            'guys' => []
        ];

        foreach ($guys as $guy) {
            if (!isset($guy['uid']) || !isset($guy['name'])) {
                throw new InvalidParamException('invalid guys item');
            }

            $params['guys'][] = [
                'uid' => $guy['uid'],
                'name' => $guy['name'],
            ];
        }

        return $this->api->post('good-guys', $params);
    }

    /**
     * 删除白名单API
     *
     * @param $id
     * @return mixed
     */
    public function deleteGoodGuy($id)
    {
        $params = [
            'guys' => [
                'uid' => $id
            ]
        ];

        return $this->api->post('good-guys', $params);
    }

    /**
     * 批量删除白名单API
     *
     * @param $ids
     * @return mixed
     */
    public function deleteGoodGuys($ids)
    {
        $params = [
            'guys' => []
        ];

        foreach ($ids as $id) {
            $params['guys'][] = [
                'uid' => $id,
            ];
        }

        return $this->api->post('good-guys', $params);
    }

    /**
     * 验证签名
     * @param $sign
     * @param $uri
     * @param $timestamp
     * @return bool
     */
    public function signVerify($sign, $uri, $timestamp)
    {
        $result = $this->api->sign($uri, $timestamp);
        return $sign === $result;
    }

}
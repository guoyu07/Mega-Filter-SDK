<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 17/2/9
 * Time: 下午2:55
 */

namespace MegaFilter;

/**
 * 负责管理mega全局服务的api客户端
 *
 * Class MegaFilterCenterApi
 * @package MegaFilter
 */
class MegaFilterCenterApi
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

    const ALL_PROJECT = null;

    function __construct($host, $token)
    {
        $this->api = new MegaFilterApi($host, 'mega', $token);
    }

    /**
     * 获取已检测内容列表API
     *
     * @param string $project
     * @param int $type
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getCheckedContents($project = null, $type = self::ALL_AI_TYPE, $page = 1, $limit = 30)
    {
        $params = [
            'page' => $page,
            'limit' => $limit,
            'type' => $type
        ];

        if ($project)
            $params['project'] = $project;

        return $this->api->get('text/checked', $params);
    }

    /**
     * 审核已检测的内容API
     *
     * @param $id
     * @param $type
     * @return mixed
     */
    public function auditCheckedContent($project, $id, $type)
    {
        $params = [
            'project' => $project,
            'id' => $id,
            'type' => $type
        ];

        return $this->api->post('text/checked', $params);
    }

}
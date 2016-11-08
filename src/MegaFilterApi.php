<?php
namespace MegaFilter;

class MegaFilterApi
{
    public $token;

    public $project;

    public $host = "127.0.0.1";

    public $ssl_verifypeer = FALSE;

    public $timeout = 30;

    public $connectTimeOut = 30;

    public $http_code;

    public $http_info;

    public $http_header;

    public $url;

    public $debug = false;

    public $postData;

    public $format = 'json';

    public $decode_json = true;     //将json解析为数组

    public function __construct($host, $project, $token)
    {
        $this->host = $host;
        $this->project = $project;
        $this->token = $token;
    }

    public function getPathUrl()
    {
        return $this->host . '/' . $this->project . '/';
    }

    public function get($url, $parameters = [])
    {
        $response = $this->request($url, 'GET', $parameters);
        if ($this->format === 'json' && $this->decode_json && $response) {
            return json_decode($response, true);
        }
        return $response;
    }

    function post($url, $parameters = [], $multi = false) {
        $response = $this->request($url, 'POST', $parameters, $multi);
        if ($this->format === 'json' && $this->decode_json && $response) {
            return json_decode($response, true);
        }
        return $response;
    }

    function put($url, $parameters = []) {
        $response = $this->request($url, 'PUT', $parameters);
        if ($this->format === 'json' && $this->decode_json && $response) {
            return json_decode($response, true);
        }
        return $response;
    }

    function delete($url, $parameters = []) {
        $response = $this->request($url, 'DELETE', $parameters);
        if ($this->format === 'json' && $this->decode_json && $response) {
            return json_decode($response, true);
        }
        return $response;
    }

    function request($url, $method, $parameters, $multi = false)
    {
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0)
        {
            $url = $this->getPathUrl() . $url . "?token={$this->token}";
        }

        switch ($method)
        {
            case 'GET':
                if (!empty($parameters))
                    $url = $url . '&' . http_build_query($parameters);
                $response = $this->http($url, 'GET');
                break;
            default:
                $headers = [];
                if (!$multi && (is_array($parameters) || is_object($parameters))) {
                    $body = json_encode($parameters);
                } else {
                    $body = $parameters;
                }
                $response = $this->http($url, $method, $body, $headers);
        }

        if ($this->http_code !== 200)
        {
            throw new ApiRequestException($response, $this->http_code);
        }

        return $response;
    }

    function http($url, $method, $postFields = null, $headers = [])
    {
        $this->http_info = [];
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeOut);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        if (version_compare(phpversion(), '5.4.0', '<')) {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        } else {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
        }
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, [$this, 'getHeader']);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        $headers[] = "Content-Type: application/json";
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postFields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postFields);
                    $this->postData = $postFields;
                }
                break;
            case 'PUT':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($postFields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postFields);
                    $this->postData = $postFields;
                }
                break;
            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        if ($this->debug) {
            echo "=====post data======\r\n";
            var_dump($postFields);
            echo "=====headers======\r\n";
            print_r($headers);
            echo '=====request info=====' . "\r\n";
            print_r(curl_getinfo($ci));
            echo '=====response=====' . "\r\n";
            print_r($response);
            echo "\r\n", '==================' , "\r\n";
        }
        curl_close($ci);
        return $response;
    }

    function getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
}
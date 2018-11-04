<?php

namespace Swoft\Support;

use Psr\Http\Message\RequestInterface;
use Swoft\Http\Message\Server\Request;

class HttpInput
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $all;

    /**
     * 所有请求参数
     *
     * @var array
     */
    protected $params = [];

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;

        $this->params['query'] = $this->request->query();

        $json = $this->request->json();
        $this->params['post'] = array_merge($this->request->post(), $json ?: []);
    }

    /**
     * 判断字段是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        if (isset($this->params['post'][$key])) {
            return true;
        }
        return isset($this->params['query'][$key]) ? true : false;
    }

    /**
     * 获取所有GET/POST参数
     *
     * @return array
     */
    public function all()
    {
        if ($this->all) {
            return $this->all;
        }

        return $this->all =
            array_merge($this->params['query'], Arr::merge($this->params['post'], (array)$this->request->file(), true));
    }

    /**
     * 获取POST或GET参数
     *
     * @param string|null $key
     * @param mixed $def
     * @return mixed
     */
    public function request(string $key = null, $def = null)
    {
        if ($key === null) {
            return $this->all();
        }

        if (isset($this->params['post'][$key])) {
            return $this->params['post'][$key];
        }

        return isset($this->params['query'][$key]) ? $this->params['query'][$key] : $def;
    }

    /**
     * 获取上传的文件
     *
     * @param string|null $key
     * @param null $default
     * @return array|null|\Swoft\Http\Message\Upload\UploadedFile
     */
    public function file(string $key = null, $default = null)
    {
        return $this->request->file($key, $default);
    }

    /**
     * 获取GET参数
     *
     * @param string $key
     * @param mixed $def
     * @return mixed
     */
    public function get(string $key = null, $def = null)
    {
        if ($key === null) {
            return $this->params['query'];
        }

        return isset($this->params['query'][$key]) ? $this->params['query'][$key] : $def;
    }

    /**
     * 获取POST参数
     *
     * @param string $key
     * @param mixed $def
     * @return mixed
     */
    public function post(string $key = null, $def = null)
    {
        if ($key === null) {
            return $this->params['post'];
        }
        return isset($this->params['post'][$key]) ? $this->params['post'][$key] : $def;
    }
}

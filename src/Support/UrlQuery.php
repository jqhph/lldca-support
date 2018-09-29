<?php

namespace Swoft\Support;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class UrlQuery
{
    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var array
     */
    protected $query = [];

    /**
     * UrlQuery constructor.
     * @param string $url
     * @param array $newQuery
     * @throws \Exception
     */
    public function __construct($url = '', array $newQuery = [])
    {
        if (!$url) {
            $url = Url::full();
        }

        if ($url instanceof UriInterface) {
            $this->parseUrl((string) $url);
        } elseif ($url instanceof RequestInterface) {
            $this->parseUrl((string) $url->getUri());
        } elseif (is_string($url)) {
            $this->parseUrl(Url::to($url));
        } else {
            throw new \Exception("url格式不正确");
        }

        if ($newQuery) {
            $this->add($newQuery);
        }
    }

    /**
     * @param string $url
     * @return void
     */
    protected function parseUrl(string $url)
    {
        if (strpos($url, '?') === false) {
            $this->url = $url;
            return;
        }
        list($url, $queryString) = explode('?', $url);

        parse_str(urldecode($queryString), $this->query);

        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * 增加query字段
     *
     * @param string|array $key
     * @param string|int|array $value
     * @return $this
     */
    public function add($key, $value = '')
    {
        if (is_array($key)) {
            foreach ($key as $k => &$v) {
                $this->add($k, $v);
            }
            return $this;
        }

        if (isset($this->query[$key]) && is_array($this->query[$key]) && !is_array($value)) {
            $this->query[$key][] = $value;
        } elseif (isset($this->query[$key]) && is_array($this->query[$key]) && is_array($value)) {
            $this->query[$key] = array_merge($this->query[$key], $value);
        } else {
            $this->query[$key] = $value;
        }
        return $this;
    }

    /**
     * 移除query字段
     *
     * @param string|array $key
     * @return $this
     */
    public function delete($key)
    {
        array_forget($this->query, $key);

        return $this;
    }

    /**
     * 获取拼接好的url
     *
     * @return string
     */
    public function build()
    {
        if ($this->query) {
            return $this->url .'?' . http_build_query($this->query);
        }
        return $this->url;
    }

    /**
     * 转化为字符串
     *
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

}

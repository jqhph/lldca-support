<?php

namespace Swoft\Support;

use Psr\Http\Message\RequestInterface;
use Swoft\Core\RequestContext;

class Input
{
    /**
     *
     * @param RequestInterface|null $request
     * @return HttpInput
     */
    public static function create(RequestInterface $request = null)
    {
        return new HttpInput($request ?: RequestContext::getRequest());
    }

    /**
     * 获取HttpInput单例
     *
     * @return HttpInput
     */
    public static function make()
    {
        if ($input = RequestContext::getContextDataByKey('__httpinput__')) {
            return $input;
        }
        $input = static::create();

        RequestContext::setContextDataByKey('__httpinput__', $input);

        return $input;
    }

    /**
     * 判断字段是否存在
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key)
    {
        return static::make()->has($key);
    }

    /**
     * 获取所有GET/POST参数
     *
     * @return array
     */
    public static function all()
    {
        return static::make()->all();
    }

    /**
     * 获取POST或GET参数
     *
     * @param string|null $key
     * @param mixed $def
     * @return mixed
     */
    public static function request(string $key = null, $def = null)
    {
        return static::make()->request($key, $def);
    }

    /**
     * 获取上传的文件
     *
     * @param string|null $key
     * @param null $default
     * @return array|null|\Swoft\Http\Message\Upload\UploadedFile
     */
    public static function file(string $key = null, $default = null)
    {
        return static::make()->file($key, $default);
    }

    /**
     * 获取GET参数
     *
     * @param string $key
     * @param mixed $def
     * @return mixed
     */
    public static function get(string $key = null, $def = null)
    {
        return static::make()->get($key, $def);
    }

    /**
     * 获取POST参数
     *
     * @param string $key
     * @param mixed $def
     * @return mixed
     */
    public static function post(string $key = null, $def = null)
    {
        return static::make()->post($key, $def);
    }
}

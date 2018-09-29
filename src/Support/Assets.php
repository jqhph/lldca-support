<?php

namespace Swoft\Support;

class Assets
{
    // 别名标识符
    const SYMBOL = '@';

    /**
     * 别名配置
     *
     * @var array
     */
    protected static $alias = [];


    /**
     * 解析静态资源路径
     *
     * @param $path
     * @return string
     */
    public static function alias(string $path)
    {
        $config = static::getAlias();

        return static::parsePath($path, $config);
    }

    /**
     * 设置别名
     *
     * @param string|array $name
     * @param string $dir
     * @throws \Exception
     */
    public static function setAlias($name, string $dir = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => &$v) {
                static::setAlias($k, $v);
            }
            return;
        }
        if (static::exist($name)) {
            throw new \Exception("资源路径别名[$name]已存在,请勿重复设置");
        }
        self::$alias[$name] = $dir;
    }

    /**
     * 检测别名是否已存在
     *
     * @param string $name
     * @return bool
     */
    public static function exist(string $name)
    {
        return isset(self::$alias[$name]);
    }

    /**
     * 解析css路径别名
     *
     * @param string $path
     * @return string
     */
    public static function css(string $path)
    {
        $path = static::alias($path);

        if (strpos($path, '.css') === false) {
            $path .= '.css';
        }
        if (strpos($path, '.?') === false) {
            $path .= '?v='.config('assets.css-version');
        }

        return $path;
    }

    /**
     * 解析js路径别名
     *
     * @param string $path
     * @return string
     */
    public static function js(string $path)
    {
        $path = static::alias($path);

        if (strpos($path, '.js') === false) {
            $path .= '.js';
        }
        if (strpos($path, '.?') === false) {
            $path .= '?v='.config('assets.js-version');
        }

        return $path;
    }

    /**
     * 路径前缀解析
     *
     * @param string $path
     * @return string
     */
    protected static function parsePath(string $path, array &$config)
    {
        if (strpos($path, self::SYMBOL) !== 0) {
            return $path;
        }
        $path = explode('/', ltrim($path, self::SYMBOL));

        if (!empty($config[$path[0]])) {
            $path[0] = $config[$path[0]];
        }
        return join('/', $path);
    }

    /**
     * 静态资源别名配置
     *
     * @return array
     */
    protected static function getAlias()
    {
        if (static::$alias) {
            return static::$alias;
        }

        $server = rtrim(config('assets.resource-server'), '/');

        $paths = (array)config('assets.alias');

        foreach ($paths as &$path) {
            if (is_valid_url($path)) {
                continue;
            }
            $path = $server ? ($server . (strpos($path, '/') === 0 ? $path : "/$path")) : Url::to($path);
        }

        return static::$alias = array_merge($paths, self::$alias);
    }

}

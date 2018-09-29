<?php

namespace Swoft\Support;

class Url
{
    /**
     * Get the full URL for the current request.
     *
     * @param array $newQuery Set new query columns
     * @return string
     */
    public static function full(array $newQuery = [])
    {
        $url = UrlGenerator::create()->full();
        if (!$newQuery) {
            return $url;
        }

        return (new UrlQuery($url, $newQuery))->build();
    }

    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public static function current()
    {
        return UrlGenerator::create()->current();
    }

    /**
     * Get the URL for the previous request.
     *
     * @param  mixed  $fallback
     * @return string
     */
    public static function previous($fallback = false)
    {
        return UrlGenerator::create()->previous($fallback);
    }

    /**
     * @param string $url
     */
    public static function setPrevious(string $url)
    {
        UrlGenerator::setPrevious($url);
    }

    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public static function to($path, $extra = [], $secure = null)
    {
        return UrlGenerator::create()->to($path, $extra, $secure);
    }

    /**
     * Generate a secure, absolute URL to the given path.
     *
     * @param  string  $path
     * @param  array   $parameters
     * @return string
     */
    public static function secure($path, $parameters = [])
    {
        return static::to($path, $parameters, true);
    }

    /**
     * @param $url
     * @param array $newQuery
     * @return UrlQuery
     */
    public static function query($url = null, array $newQuery = [])
    {
        return new UrlQuery($url, $newQuery);
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     * @return bool
     */
    public static function isValidUrl($path)
    {
        if (! preg_match('~^(#|//|https?://|mailto:|tel:)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }

}

<?php

namespace Swoft\Support;

use Swoft\Core\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Swoft\Session\SessionInterface;
use Swoft\Session\SessionStore;

class Redirector
{
    /**
     * Create a new redirect response to the previous location.
     *
     * @param  int    $status
     * @param  array  $headers
     * @param  mixed  $fallback
     * @return ResponseInterface
     */
    public static function back(int $status = 302, $headers = [], $fallback = false)
    {
        return static::createRedirect(Url::previous($fallback), $status, $headers);
    }

    /**
     * Create a new redirect response to the current URI.
     *
     * @param  int    $status
     * @param  array  $headers
     * @return ResponseInterface
     */
    public static function refresh(int $status = 302, $headers = [])
    {
        return static::to(RequestContext::getRequest()->getUri()->getPath(), $status, $headers);
    }

    /**
     * Create a new redirect response, while putting the current URL in the session.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return ResponseInterface
     */
    public function guest($path, int $status = 302, $headers = [], $secure = null)
    {
        static::getSession()->put('url.intended', Url::full());

        return static::to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the previously intended location.
     *
     * @param  string  $default
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return ResponseInterface
     */
    public static function intended($default = '/', int $status = 302, $headers = [], $secure = null)
    {
        $session = static::getSession();

        $path = $session->get('url.intended', $default);

        $session->remove('url.intended');

        return static::to($path, $status, $headers, $secure);
    }

    /**
     * @return SessionInterface
     */
    protected static function getSession()
    {
        $sessionManager = \Swoft\App::getBean('sessionManager');

        return $sessionManager->getSession();
    }

    /**
     * Create a new redirect response to the given path.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return ResponseInterface
     */
    public static function to($path, int $status = 302, $headers = [], $secure = null)
    {
        return static::createRedirect(Url::to($path, [], $secure), $status, $headers);
    }

    /**
     * Create a new redirect response to an external URL (no validation).
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return ResponseInterface
     */
    public static function away($path, int $status = 302, $headers = [])
    {
        return static::createRedirect($path, $status, $headers);
    }

    /**
     * Create a new redirect response to the given HTTPS path.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return ResponseInterface
     */
    public static function secure($path, int $status = 302, $headers = [])
    {
        return static::to($path, $status, $headers, true);
    }

    /**
     * Create a new redirect response.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return ResponseInterface
     */
    protected static function createRedirect($path, int $status, array $headers)
    {
        $response = RequestContext::getResponse();

        foreach ($headers as $k => &$v) {
            $response = $response->withHeader($k, $v);
        }

        return $response->withStatus($status)->withHeader('Location', $path);
    }


    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isRedirection(ResponseInterface $response): bool
    {
        $code = $response->getStatusCode();

        return $code >= 300 && $code < 400;
    }
}

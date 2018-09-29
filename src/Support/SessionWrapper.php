<?php

namespace Swoft\Support;

use Swoft\Session\SessionInterface;
use SessionHandlerInterface;

/**
 *
 * @method string getName()
 * @method string getId()
 * @method void gsetId($id)
 * @method bool start()
 * @method bool save()
 * @method array all()
 * @method bool exists($key)
 * @method bool has($key)
 * @method mixed get($key, $default = null)
 * @method void put($key, $value = null)
 * @method string token()
 * @method mixed remove($key)
 * @method void forget($keys)
 * @method void flush()
 * @method bool migrate($destroy = false)
 * @method bool isStarted()
 * @method string|null previousUrl()
 * @method void setPreviousUrl($url)
 * @method SessionHandlerInterface getHandler()
 * @method bool handlerNeedsRequest()
 * @method void setRequestOnHandler($request)
 */
class SessionWrapper
{
    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Push a value onto a session array.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = (array)$this->session->get($key, []);

        $array[] = $value;

        $this->session->put($key, $array);
    }

    /**
     * Get the value of a given key and then forget it.
     *
     * @param  string  $key
     * @param  string  $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        if (!$this->session->has($key)) {
            return $default;
        }
        return $this->session->remove($key);
    }


    public function __call($method, $arguments)
    {
        return $this->session->$method(...$arguments);
    }
}

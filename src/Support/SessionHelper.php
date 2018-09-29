<?php

namespace Swoft\Support;

use Swoft\App;
use Swoft\Session\SessionInterface;

class SessionHelper
{
    /**
     * @return SessionWrapper|null
     */
    public static function wrap()
    {
        try {
            return new SessionWrapper(App::getBean('sessionManager')->getSession());
        } catch (\Throwable $e) {
            consolelog("检测到session并未初始化，请启用SessionMiddleware中间件");

            return null;
        }
    }

}

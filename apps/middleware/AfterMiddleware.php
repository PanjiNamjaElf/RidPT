<?php

namespace apps\middleware;

/**
 * 后置中间件
 */
class AfterMiddleware
{

    public function handle($callable, \Closure $next)
    {
        // 添加中间件执行代码
        $response = $next();
        list($controller, $action) = $callable;
        // ...
        // 返回响应内容
        return $response;
    }

}

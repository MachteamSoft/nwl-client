<?php

namespace Mach\Bundle\NwlBundle\Mail\Interceptor;


class MockInterceptor implements InterceptorInterface
{
    public function intercept($nwlShortname, array $data)
    {
        return $data;
    }

    public function batchIntercept($nwlShortname, array $data)
    {
        return $data;
    }
}
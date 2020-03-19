<?php

namespace Mach\Bundle\NwlBundle\Mail\Interceptor;


interface InterceptorInterface
{
    /**
     * @return array
     */
    public function intercept($nwlShortname, array $data);

    /**
     * @return array
     */
    public function batchIntercept($nwlShortname, array $data);
}
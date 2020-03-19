<?php

namespace Mach\Bundle\NwlBundle\Mail\Interceptor;



class ChainInterceptor implements InterceptorInterface
{
    /**
     * @var InterceptorInterface[]
     */
    private $interceptors = array();

    public function addInterceptor(InterceptorInterface $interceptor, $priority)
    {
        if (isset($this->interceptors[$priority])) {
            throw new \LogicException(sprintf('Interceptor is already set'));
        }
        $this->interceptors[$priority] = $interceptor;
        krsort($this->interceptors);
    }

    public function intercept($nwlShortname, array $data)
    {
        foreach ($this->interceptors as $interceptor) {
            $data = $interceptor->intercept($nwlShortname, $data);
        }

        return $data;
    }

    public function batchIntercept($nwlShortname, array $data)
    {
        foreach ($this->interceptors as $interceptor) {
            $data = $interceptor->batchIntercept($nwlShortname, $data);
        }

        return $data;
    }
}
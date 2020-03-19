<?php

namespace Mach\Bundle\NwlBundle\Tests;


use Mach\Bundle\NwlBundle\Mail\Interceptor\ChainInterceptor;
use Mach\Bundle\NwlBundle\Mail\Interceptor\InterceptorInterface;
use Mach\Bundle\NwlBundle\Mail\Interceptor\MockInterceptor;

class ChainInterceptorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException     \LogicException
     */
    public function testAddRowWithSamePriority()
    {
        $chain = new ChainInterceptor();
        $chain->addInterceptor(new MockInterceptor(), 5);
        $chain->addInterceptor(new MockInterceptor(), 5);
    }

    public function testIntercept()
    {
        $data = array('test' => 'bbb');
        $interceptor = \Mockery::mock(InterceptorInterface::class)
            ->shouldReceive('intercept')
            ->withAnyArgs()
            ->twice()
            ->andReturn($data)
            ->getMock();

        $chain = new ChainInterceptor();
        $chain->addInterceptor($interceptor, 5);
        $chain->addInterceptor($interceptor, 10);
        $chain->intercept('test', $data);
    }
}
<?php

namespace Mach\Bundle\NwlBundle\Tests\Sender;


use Mach\Bundle\NwlBundle\Mail\Interceptor\InterceptorInterface;
use Mach\Bundle\NwlBundle\Mail\Interceptor\MockInterceptor;
use Mach\Bundle\NwlBundle\Sender\InterceptableProgressiveSender;
use Mach\Bundle\NwlBundle\Sender\ProgressiveSenderInterface;

class InterceptableProgressiveSenderTest extends \PHPUnit\Framework\TestCase
{
    private $mockData = array('aaa' => 'bbb');

    public function testAddRow()
    {
        $decoratedSender = $this->getDecoratedSenderMock();

        $sender = new InterceptableProgressiveSender($decoratedSender, $this->getMockInterceptor());
        $sender->setBufferLimit(2);
        $sender->addRow($this->mockData);
        $sender->addRow($this->mockData);
        $sender->addRow($this->mockData);
        $sender->addRow($this->mockData);
        \Mockery::close();
    }

    public function testFlush()
    {
        $decoratedSender = $this->getDecoratedSenderMock(3);
        $decoratedSender->shouldReceive('flush')
            ->withNoArgs()
            ->andReturnNull()
            ->once();

        $sender = new InterceptableProgressiveSender($decoratedSender, $this->getMockInterceptor());
        $sender->setBufferLimit(2);
        $sender->addRow($this->mockData);
        $sender->addRow($this->mockData);
        $sender->addRow($this->mockData);
        $sender->flush();
        \Mockery::close();
    }

    public function testCount()
    {
        $decoratedSender = $this->getDecoratedSenderMock();

        $sender = new InterceptableProgressiveSender($decoratedSender, $this->getMockInterceptor());
        $sender->setBufferLimit(2);
        $sender->addRow($this->mockData);
        $sender->addRow($this->mockData);
        $sender->addRow($this->mockData);
        $this->assertEquals($sender->count(), 3);
    }

    private function getMockInterceptor()
    {
        return new MockInterceptor();
    }

    private function getDecoratedSenderMock($addRowCallNrs = 4, $countReturn = 2)
    {
        $decoratedSender = \Mockery::mock(ProgressiveSenderInterface::class)
            ->shouldReceive('addRow')
            ->withAnyArgs()
            ->times($addRowCallNrs)
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('getNwlShortname')
            ->withNoArgs()
            ->andReturn('test')
            ->getMock()
            ->shouldReceive('count')
            ->withNoArgs()
            ->andReturn($countReturn)
            ->getMock();

        return $decoratedSender;
    }
}
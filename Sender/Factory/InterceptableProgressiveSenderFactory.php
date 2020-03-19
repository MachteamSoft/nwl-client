<?php

namespace Mach\Bundle\NwlBundle\Sender\Factory;


use Mach\Bundle\NwlBundle\Mail\Interceptor\InterceptorInterface;
use Mach\Bundle\NwlBundle\Sender\InterceptableProgressiveSender;
use Mach\Bundle\NwlBundle\Sender\ProgressiveSenderInterface;

class InterceptableProgressiveSenderFactory implements SenderFactoryInterface
{
    private $decoratedSenderFactory;
    private $chainInterceptor;

    public function __construct(SenderFactoryInterface $decoratedSenderFactory, InterceptorInterface $chainInterceptor)
    {
        $this->decoratedSenderFactory = $decoratedSenderFactory;
        $this->chainInterceptor = $chainInterceptor;
    }


    public function create($nwlShortname, $batchLimit = 1000, array $allowedContentIds = array(), $fieldAsOffset = false)
    {
        $decoratedSender = $this->decoratedSenderFactory->create($nwlShortname, $batchLimit, $allowedContentIds, $fieldAsOffset);

        return new InterceptableProgressiveSender($decoratedSender, $this->chainInterceptor);
    }
}
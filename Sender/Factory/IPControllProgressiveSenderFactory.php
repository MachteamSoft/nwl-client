<?php
namespace Mach\Bundle\NwlBundle\Sender\Factory;

use Mach\Bundle\NwlBundle\ProgressiveVolumeCalculator;
use Mach\Bundle\NwlBundle\Client;
use Mach\Bundle\NwlBundle\Sender\IPControllProgressiveSender;

class IPControllProgressiveSenderFactory implements SenderFactoryInterface
{
    protected $progressiveVolumeCalculator;
    protected $decoratedSenderFactory;

    public function __construct(ProgressiveVolumeCalculator $progressiveVolumeCalculator, SenderFactoryInterface $decoratedSenderFactory)
    {
        $this->progressiveVolumeCalculator = $progressiveVolumeCalculator;
        $this->decoratedSenderFactory = $decoratedSenderFactory;
    }

    public function create($nwlShortname, $batchLimit = 1000, array $allowedContentIds = array(), $fieldAsOffset = false)
    {
        $decoratedSender = $this->decoratedSenderFactory->create($nwlShortname, $batchLimit, $allowedContentIds, $fieldAsOffset);

        return new IPControllProgressiveSender($this->progressiveVolumeCalculator, $decoratedSender, $fieldAsOffset);
    }
}
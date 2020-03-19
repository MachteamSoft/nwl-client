<?php

namespace Mach\Bundle\NwlBundle\Tests;


use Mach\Bundle\NwlBundle\Entity\NwlMailSendings;
use Mach\Bundle\NwlBundle\Entity\NwlProgressiveSender;
use Mach\Bundle\NwlBundle\Entity\SendgroupLimits;
use Mach\Bundle\NwlBundle\ProgressiveVolumeCalculator;
use Mach\Bundle\NwlBundle\Repository\NwlsMailSendings;
use Mach\Bundle\NwlBundle\Repository\NwlsProgressiveSender;

class ProgressiveVolumeCalculatorTest extends \PHPUnit\Framework\TestCase
{
    public function testGetLimit()
    {
        $shortname = 'testShortname1';
        $date = new \DateTime('2015-08-08');
        $progressiveCalculator = $this->createProgressiveCalculator($shortname, $date);
        $limits = $progressiveCalculator->getLimit($shortname, true);
        $this->assertEquals($limits['send-group-1'], 29);
        $this->assertEquals($limits['send-group-2'], 58);
        $this->assertEquals($limits['send-group-3'], 2);

        $shortname = 'testShortname2';
        $date = new \DateTime('2015-08-08');
        $progressiveCalculator = $this->createProgressiveCalculator($shortname, $date, 72);
        $limits = $progressiveCalculator->getLimit($shortname);
        $this->assertEquals($limits, 0);
    }

    public function testAlreadySent()
    {
        $shortname = 'testShortname3';
        $date = new \DateTime('2015-08-08');
        $progressiveCalculator = $this->createProgressiveCalculator($shortname, $date, 0);
        $limits = $progressiveCalculator->getLimit($shortname, true);
        $this->assertEquals($limits['send-group-1'], 47);
        $this->assertEquals($limits['send-group-2'], 94);
        $this->assertEquals($limits['send-group-3'], 4);

        $progressiveCalculator = $this->createProgressiveCalculator($shortname, $date, 40);
        $limits = $progressiveCalculator->getLimit($shortname, true);
        $this->assertEquals($limits['send-group-1'], 7);
        $this->assertEquals($limits['send-group-2'], 94);
        $this->assertEquals($limits['send-group-3'], 4);

        $progressiveCalculator = $this->createProgressiveCalculator($shortname, $date, 87);
        $limits = $progressiveCalculator->getLimit($shortname, true);
        $this->assertTrue(empty($limits['send-group-1']));
        $this->assertEquals($limits['send-group-2'], 54);
        $this->assertEquals($limits['send-group-3'], 4);

        $progressiveCalculator = $this->createProgressiveCalculator($shortname, $date, 143);
        $limits = $progressiveCalculator->getLimit($shortname);
        $this->assertEquals($limits, 2);
    }

    private function createProgressiveCalculator($shortname, $date, $alreadySent = 0)
    {
        return new ProgressiveVolumeCalculator(
            $this->getClientMock(),
            $this->getNwlProgresiveSendersMock(),
            $this->getNwlMailSendingsMock($shortname, $date, $alreadySent)
        );
    }

    private function getClientMock()
    {
        return \Mockery::mock('Mach\Bundle\NwlBundle\Client');
    }

    private function getNwlMailSendingsMock($shortname, \DateTime $sendingDate ,$alreadySent)
    {
        $mailSending = new NwlMailSendings($shortname, $sendingDate, $alreadySent);
        return \Mockery::mock(NwlsMailSendings::class)
            ->shouldReceive('findOneBy')
            ->withAnyArgs()
            ->andReturn($mailSending)
            ->getMock();
    }

    private function getNwlProgresiveSendersMock()
    {
        $sendGroups = $this->getSendgroupLimits();
        $senders = [
            $this->createNwlProgressiveSender('testShortname1', 0, 5, $sendGroups),
            $this->createNwlProgressiveSender('testShortname2', 0, 4, $sendGroups),
            $this->createNwlProgressiveSender('testShortname3', 0, 8, $sendGroups),
        ];

        return \Mockery::mock(NwlsProgressiveSender::class)
            ->shouldReceive('getAllSenders')
            ->andReturn($senders)
            ->getMock();
    }

    private function getSendgroupLimits()
    {
        return  [
            new SendgroupLimits('send-group-1', 100),
            new SendgroupLimits('send-group-2', 200),
            new SendgroupLimits('send-group-3', 10),
        ];
    }

    private function createNwlProgressiveSender($shortname, $offset, $priority, array $sendGroups)
    {
        $sender = new NwlProgressiveSender($shortname, $offset, $priority);
        $sender->setSendgroupLimits($sendGroups);

        return $sender;
    }
}
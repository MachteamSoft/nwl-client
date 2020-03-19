<?php

namespace Mach\Bundle\NwlBundle\Sender\Factory;

use Mach\Bundle\NwlBundle\Client;
use Mach\Bundle\NwlBundle\Sender\ProgressiveSender;

/**
 * @author Rares Vlasceanu
 */
class ProgressiveSenderFactory implements SenderFactoryInterface
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $nwlShortname
     * @param int $batchLimit
     * @param array $allowedContentIds
     * @return ProgressiveSender
     */
    public function create($nwlShortname, $batchLimit = 1000, array $allowedContentIds = array(), $fieldAsOffset = false)
    {
        return new ProgressiveSender($batchLimit, $this->client, $nwlShortname, $allowedContentIds);
    }
}

<?php

namespace Mach\Bundle\NwlBundle\Sender\Factory;


use Mach\Bundle\NwlBundle\Sender\ProgressiveSenderInterface;

interface SenderFactoryInterface
{
    /**
     * @return ProgressiveSenderInterface
     */
    public function create($nwlShortname, $batchLimit = 1000, array $allowedContentIds = array(), $fieldAsOffset = false);
}
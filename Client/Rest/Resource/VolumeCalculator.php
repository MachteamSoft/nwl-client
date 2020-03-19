<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * @author Adrian Ciausu
 */
class VolumeCalculator extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/volume-calculator';
    }

    public function get($shortName, $minimal)
    {
        return $this->performGet(array('id' => $shortName, 'minimal' => $minimal));
    }
}
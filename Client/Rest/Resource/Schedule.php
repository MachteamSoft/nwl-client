<?php


namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;


class Schedule extends AbstractResource
{
    /**
     * @return string
     */
    public function getUri()
    {
        return '/nwl-sending-schedule';
    }

    /**
     * Update schedule parameters
     *
     * @return array
     */
    public function put(string $nwlIdentifier, array $params)
    {
        return $this->performPut(array('nwl' => $nwlIdentifier) + $params);
    }

    public function get(int $id)
    {
        return $this->performGet(array('id' => $id));
    }

}
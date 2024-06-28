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

    public function post(string $nwlIdentifier, int $queryId, string $startDateTime, $type = 'oneTime', $cron = null, $endDate = null)
    {
        return $this->performPost(array(
            'nwl' => $nwlIdentifier,
            'query' => $queryId,
            'startDateTime' => $startDateTime,
            'type' => $type,
            'cron' => $cron,
            'endDate' => $endDate
        ));
    }

}
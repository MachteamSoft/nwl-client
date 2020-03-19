<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * Class Subscribers
 * @package Mach\Bundle\NwlBundle\Client\Rest\Resource
 * @author Marius Gherasie <marius.gherasie@machteamsoft.ro>
 */
class Subscribers extends AbstractResource
{
    /**
     * @return string
     */
    public function getUri()
    {
        return '/subscribers';
    }

    public function getAll($limit, $lastId = 0, \DateTime $fromDate = null, $excludeGroup = true)
    {
        return $this->handle('all', $limit, $lastId, $fromDate, $excludeGroup);
    }

    /**
     * @param $limit
     * @param int $lastId
     * @param \DateTime|null $fromDate
     * @param bool|true $excludeGroup
     * @param array $excludeSites - list of site shortnames
     * @return mixed
     */
    public function getWithAction($limit, $lastId = 0, \DateTime $fromDate = null, $excludeGroup = true, $excludeSites = array())
    {
        return $this->handle('withAction', $limit, $lastId, $fromDate, $excludeGroup, $excludeSites);
    }

    private function handle($id, $limit, $lastId = 0, \DateTime $fromDate = null, $excludeGroup = true, $excludeSites = array())
    {
        $options = array(
            'id'           => $id,
            'limit'        => $limit,
            'lastId'       => $lastId,
            'excludeGroup' => $excludeGroup,
            'excludeSites' => $excludeSites
        );
        if (!empty($fromDate)) {
            $options['fromDate'] = $fromDate;
        }

        return $this->performGet($options);
    }
}
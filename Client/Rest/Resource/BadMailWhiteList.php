<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * Class BadMailWhiteList
 * @package Mach\Bundle\NwlBundle\Client\Rest\Resource
 * @author Marius Gherasie <marius.gherasie@machteamsoft.ro>
 */
class BadMailWhiteList extends AbstractResource
{
    /**
     * @return string
     */
    public function getUri()
    {
        return '/bad-mail-white-list';
    }

    /**
     * Get the status for an email
     *
     * @param string $email
     * @return array - array('message' => false, 'status' => 'OK')
     * @return mixed
     */
    public function get($email)
    {
        return $this->performGet(array('id' => $email));
    }

    /**
     * Adds and email to badmails list
     *
     * @param string $email
     * @return array
     */
    public function post($email)
    {
        return $this->performPost(array('id' => $email));
    }

    /**
     * Removes an email from badmails
     *
     * @param string $email
     * @return array
     */
    public function delete($email)
    {
        return $this->performDelete(array('id' => $email));
    }
}
<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * @author Catalin Costache
 */
class User extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/user';
    }

    /**
     * Get user last activity and spam flag
     *
     * @param string $email
     * @return array
     */
    public function get($email)
    {
        return $this->performGet(array('id' => $email));
    }

    /**
     * Update Email and/or reset Spam Flag
     *
     * @param string $email
     * @param array $params resetSpam, newEmail
     * @return array
     */
    public function put($email, $params = array('resetSpam' => false, 'newEmail' => null))
    {
        return $this->performPut(array('id' => $email) + $params);
    }

    public function subscribe($email)
    {
        $uri = sprintf('%s/subscribe', $this->getUri());

        return $this->getRestClient()->performPost($uri, array('email' => $email));
    }
}
<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * @author Catalin Costache
 */
class BadMail extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/bad-mail';
    }

    /**
     * Get the status for an email
     *
     * @param mixed $email
     * @return array - array('message' => false, 'status' => 'OK')
     * @return mixed
     */
    public function get($email, $type = null)
    {
        return $this->performGet(array('id' => $email, 'type' => $type));
    }

    /**
     * Adds and email to badmails list
     *
     * @param string $email
     * @param string $type hard, soft
     * @return array
     */
    public function post($email, $type = null)
    {
        return $this->performPost(array('id' => $email, 'type' => $type));
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
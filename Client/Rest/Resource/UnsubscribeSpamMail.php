<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

class UnsubscribeSpamMail extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/unsubscribe-spam-mails';
    }

    /**
     * Get the status for an email
     *
     * @param mixed $email
     * @return mixed
     */
    public function get($emails)
    {
        return $this->performGet(array('id' => $emails));
    }
}
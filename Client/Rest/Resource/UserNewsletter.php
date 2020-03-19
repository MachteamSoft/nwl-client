<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * @author Catalin Costache
 */
class UserNewsletter extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/user-newsletter';
    }

    /**
     * Return the periodicity settings for all mails specific to this user
     *
     * @param string $email
     * @return array
     */
    public function all($email)
    {
        return $this->performGet(array('email' => $email));
    }

    /**
     * Gets the periodicity settings for given email
     *
     * @param string $email
     * @param string $nwlShortname
     * @return array
     */
    public function get($email, $nwlShortname)
    {
        return $this->performGet(array(
                    'id' => $email,
                    'nwl' => $nwlShortname
                ));
    }

    /**
     * Gets the periodicity settings for multiple users at once
     *
     * @param array $emails
     * @param string $nwlShortname
     * @return array
     */
    public function getBatchSettings($emails, $nwlShortname)
    {
        return $this->performGet(array(
                    'id' => $emails,
                    'nwl' => $nwlShortname
                ));
    }

    /**
     * Sets the periodicity settings for a nwl
     *
     * @param string $email
     * @param int $nwlShortname
     * @param string $type instant, daily or weekly
     * @param string $hour HH:mm format
     * @param int $dow
     * @return array
     */
    public function post($email, $nwlShortname, $type, $hour, $dow = null)
    {
        return $this->performPost(array(
                    'id' => $email,
                    'nwl' => $nwlShortname,
                    'type' => $type,
                    'hour' => $hour,
                    'dow' => $dow)
        );
    }

    /**
     * Update periodicity settings for a nwl
     *
     * @param string $email
     * @param int $nwlShortname
     * @param array $params keys can be: type, hour and dow
     * @return array
     */
    public function put($email, $nwlShortname, $params = array('type' => null, 'hour' => null, 'dow' => null))
    {
        return $this->performPut(array('id' => $email, 'nwl' => $nwlShortname) + $params);
    }

    /**
     * Unsubscribe from a newsletter
     *
     * @param string $email
     * @param int $nwlShortname
     * @return array
     */
    public function delete($email, $nwlShortname)
    {
        return $this->performDelete(array('id' => $email, 'nwl' => $nwlShortname));
    }

}
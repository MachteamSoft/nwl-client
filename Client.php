<?php

namespace Mach\Bundle\NwlBundle;

use Mach\Bundle\NwlBundle\Client\Rest\ClientInterface;
use Mach\Bundle\NwlBundle\Mail\Interceptor\InterceptorInterface;
use Mach\Bundle\NwlBundle\Nwl\Attachment;
use Monolog\Logger;
use Mach\Bundle\NwlBundle\Client\Rest\Resource;

class Client
{
    const SEND_GROUP_MAIL_FIELD = 'send_group';
    protected $client = null;
    protected $logger = null;
    protected $chainInterceptor;

    /**
     * @var Client\Rest\Resource\Instant
     */
    protected $instantResource;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client, Logger $logger, InterceptorInterface $chainInterceptor)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->chainInterceptor = $chainInterceptor;
    }

    public function getRestClient()
    {
        return $this->client;
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\BadMail
     */
    public function badmail()
    {
        return new Resource\BadMail($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\UnsubscribeSpamMail
     */
    public function unsubscribespammails()
    {
        return new Resource\UnsubscribeSpamMail($this->client, $this->logger);
    }
    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\BadMailWhiteList
     */
    public function badmailwhitelist()
    {
        return new Resource\BadMailWhiteList($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\Instant
     */
    public function instant()
    {
        if (!$this->instantResource) {
            $this->instantResource = new Resource\Instant($this->client, $this->logger, $this->chainInterceptor);
        }

        return $this->instantResource;
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\NwlContent
     */
    public function nwlcontent()
    {
        return new Resource\NwlContent($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\PushNotificationContent
     */
    public function pushNotificationContent()
    {
        return new Resource\PushNotificationContent($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\Query
     */
    public function query()
    {
        return new Resource\Query($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\Schedule
     */
    public function schedule()
    {
        return new Resource\Schedule($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\Sending
     */
    public function sending()
    {
        return new Resource\Sending($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\User
     */
    public function user()
    {
        return new Resource\User($this->client, $this->logger);
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\UserNewsletter
     */
    public function usernewsletter()
    {
        return new Resource\UserNewsletter($this->client, $this->logger);
    }

    /**
     * @return Resource\VolumeCalculator
     */
    public function volumeCalculator()
    {
        return new Resource\VolumeCalculator($this->client, $this->logger);
    }

    /**
     * @return Resource\Subscribers
     */
    public function subscribers()
    {
        return new Resource\Subscribers($this->client, $this->logger);
    }

    /**
     * Convenient method for creating compatible attachments
     *
     * @param string $fileName
     * @param mixed $data
     * @param string $contentType
     * @return Attachment
     */
    public function createAttachment($fileName, $data = null, $contentType = 'application/octet-stream')
    {
        return new Attachment($fileName, $data, $contentType);
    }
}
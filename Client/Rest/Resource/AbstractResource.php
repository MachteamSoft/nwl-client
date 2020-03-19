<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

use Mach\Bundle\NwlBundle\Client\Rest\ClientInterface;
use Monolog\Logger;

/**
 * REST client decorator
 *
 * @author Rares Vlasceanu
 */
abstract class AbstractResource
{
    protected $client;
    protected $logger;

    /**
     * @param ClientInterface $restClient
     * @param \Monolog\Logger $logger
     */
    public function __construct(ClientInterface $restClient, Logger $logger)
    {
        $this->client = $restClient;
        $this->logger = $logger;
    }

    /**
     * @abstract
     * @return string
     */
    public abstract function getUri();

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Rest\ClientInterface
     */
    public function getRestClient()
    {
        return $this->client;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function performGet(array $params)
    {
        return $this->getRestClient()->performGet($this->getUri(), $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function performPost(array $params)
    {
        $this->logger->info('Nwl client post '.$this->getUri(), $params);
        return $this->getRestClient()->performPost($this->getUri(), $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function performPut(array $params)
    {
        return $this->getRestClient()->performPut($this->getUri(), $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function performDelete(array $params)
    {
        return $this->getRestClient()->performDelete($this->getUri(), $params);
    }
}

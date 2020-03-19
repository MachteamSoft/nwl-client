<?php

namespace Mach\Bundle\NwlBundle\Client\Rest;

/**
 * @author Rares Vlasceanu
 */
interface ClientInterface
{
    /**
     * Performs a PUT request and returns response
     *
     * @abstract
     * @param string $uri
     * @param array $params
     * @return mixed
     */
    public function performPut($uri, $params = array());

    /**
     * Performs a POST request and returns response
     *
     * @abstract
     * @param string $uri
     * @param array $params
     * @return mixed
     */
    public function performPost($uri, $params = array());

    /**
     * Performs a GET request and returns response
     *
     * @abstract
     * @param string $uri
     * @param array $params
     * @return mixed
     */
    public function performGet($uri, $params = array());

    /**
     * Performs a DELETE request and returns response
     *
     * @abstract
     * @param string $uri
     * @param array $params
     * @return mixed
     */
    public function performDelete($uri, $params = array());

    /**
     * Offers access to transport layer
     *
     * @return \Mach\Bundle\NwlBundle\Client\Transport\HttpTransportInterface
     */
    public function getTransport();

}

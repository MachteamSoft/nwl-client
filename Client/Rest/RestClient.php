<?php

namespace Mach\Bundle\NwlBundle\Client\Rest;

use Mach\Bundle\NwlBundle\Client\Transport;
use Mach\Bundle\NwlBundle\Debug\DebugInfo;

/**
 * Use a HTTP transport client to perform REST requests
 *
 * @author Catalin Costache
 * @author Rares Vlasceanu
 */
class RestClient implements ClientInterface, Transport\HttpProtocolInterface
{

    /**
     * @var string
     */
    protected $restServiceUri;

    /**
     * @var \Mach\Bundle\NwlBundle\Client\Transport\HttpTransportInterface
     */
    protected $transport;

    /**
     * @var \Mach\Bundle\NwlBundle\Debug\DebugInfo|null
     */
    protected $debug = null;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param string $restServiceUri
     * @param \Mach\Bundle\NwlBundle\Client\Transport\HttpTransportInterface $transport
     * @param \Mach\Bundle\NwlBundle\Debug\DebugInfo|null $debug
     * @return \Mach\Bundle\NwlBundle\Client\Rest\RestClient
     */
    public function __construct($restServiceUri, Transport\HttpTransportInterface $transport, DebugInfo $debug = null)
    {
        $this->restServiceUri = $restServiceUri;
        $this->transport = $transport;
        $this->debug = $debug;
    }

    /**
     * @return \Mach\Bundle\NwlBundle\Client\Transport\HttpTransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param string $user
     * @param string $password
     */
    public function authenticate($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|string
     */
    public function performPut($uri, $params = array())
    {
        return $this->performRequest($uri, $params, self::METHOD_PUT);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|string
     */
    public function performPost($uri, $params = array())
    {
        return $this->performRequest($uri, $params, self::METHOD_POST);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|string
     */
    public function performGet($uri, $params = array())
    {
        return $this->performRequest($uri, $params, self::METHOD_GET);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|string
     */
    public function performDelete($uri, $params = array())
    {
        return $this->performRequest($uri, $params, self::METHOD_DELETE);
    }

    /**
     * @param $uri
     * @param array $params
     * @param $method
     * @return mixed|string
     * @throws \Exception|boolean (true)
     */
    private function performRequest($uri, array $params, $method)
    {
        $params += array(
            'debug_info' => $this->getDebugInfo()
        );

        $this->transport->setParameter($params);
        $this->transport->setUri($this->restServiceUri . $uri);
        $this->transport->setMethod($method);
        $this->transport->addCookie('__nwlRequest', 1);
        $this->transport->setTimeout(180);

        if ($this->user) {
            $this->transport->setAuthorization($this->user, $this->password);
        }

        $body = json_decode($this->transport->getResponseBody(), true);
        if ($body === null) {
            $body = $this->transport->getResponseBody();
        }

        $check = $this->checkForErrors($body);
        $this->transport->reset();
        if ($check instanceof \Exception) {
            throw $check;
        }

        return $body;
    }

    /**
     * @param string $body
     * @return \Exception | boolean (true)
     */
    private function checkForErrors($body)
    {
        if (is_string($body)) {
            return new \LogicException('REST response must be a valid json. String given: ' . $body);
        }

        if (empty($body['status']) || $body['status'] != 'OK') {
            $message = null;
            if (isset($body['error'])) {
                $message = $body['error'];
            } elseif (isset($body['message'])) {
                $message = $body['message'];
            } else {
                $message = var_export($body, true);
            }
            return new \Exception('The request was not completed successfuly: ' . $message);
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getDebugInfo()
    {
        if (!$this->debug) {
            return array();
        }

        return $this->debug->getDebugInfo();
    }

}
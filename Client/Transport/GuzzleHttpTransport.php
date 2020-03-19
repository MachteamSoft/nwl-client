<?php

namespace Mach\Bundle\NwlBundle\Client\Transport;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestFactory;
use Guzzle\Http\EntityBody;

class GuzzleHttpTransport implements HttpProtocolInterface, HttpTransportInterface
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Guzzle\Http\Message\Request
     */
    protected $request;

    /**
     * @var \Guzzle\Http\Message\Response
     */
    protected $response = null;

    /**
     * @var string
     */
    protected $method = self::METHOD_GET;

    protected $uri;

    protected $auth = array('', '');

    protected $fileUpload = array();

    /**
     * @var array
     */
    protected $params = array();

    public function __construct()
    {
        $this->client = new Client();
    }

    public function setAuthorization($user, $password = '')
    {
        $this->auth = array($user, $password);
    }

    public function getResponseBody()
    {
        $this->_getResponse();

        return $this->response->getBody(true);
    }

    public function getResponseHeaders()
    {
        $this->_getResponse();

        return $this->response->getHeaders();
    }

    public function getResponseStatus()
    {
        $this->_getResponse();

        return $this->response->getStatusCode();
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setParameter($name, $value = null)
    {
        if (is_array($name)) {
            $this->params += $name;

            return;
        }
        $this->params[$name] = $value;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function setFileUpload($filename, $formname, $data = null, $ctype = null)
    {
        $this->fileUpload = array($filename, $formname, $data, $ctype);
    }

    public function reset()
    {
        $this->request = null;
        $this->response = null;
        $this->params = array();
        $this->fileUpload = null;
    }

    private function _getResponse()
    {
        if ($this->response) {
            return $this->response;
        }

        $this->request = RequestFactory::getInstance()->create($this->method, $this->uri);
//        $body = EntityBody::factory($this->fileUpload[0]);
//        $client = new Client($this->uri);

        if ($this->method == self::METHOD_POST) {
            $this->client->setParameterPost($this->params);
        } else {
            $this->client->setParameterGet($this->params);
        }
        $response = $this->client->request();
        $this->reset();

        $this->response = $response;
    }

    public function addCookie($key, $val)
    {
        throw new \BadMethodCallException('addCookie: Method not implemented');
    }

    public function setTimeout($timeout)
    {
        throw new \BadMethodCallException('addCookie: Method not implemented');
    }
}
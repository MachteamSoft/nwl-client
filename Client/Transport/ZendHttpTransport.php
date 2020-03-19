<?php

namespace Mach\Bundle\NwlBundle\Client\Transport;

/**
 * Zend_Http_Client based implementation of the Client interface
 *
 * @author Catalin Costache
 */
class ZendHttpTransport implements HttpProtocolInterface, HttpTransportInterface
{

    /**
     * @var \Zend_Http_Client
     */
    protected $client;

    /**
     * @var \Zend_Http_Response
     */
    protected $response = null;

    /**
     * @var string
     */
    protected $method = self::METHOD_GET;

    /**
     * @var array
     */
    protected $params = array();

    public function __construct()
    {
        $this->client = new \Zend_Http_Client();
    }

    public function addCookie($key, $val)
    {
        $cookiesString = $this->client->getHeader('cookie');
        if (strpos($cookiesString, sprintf('%s=', $key)) !== false) {
            return $this;
        }

        $this->client->setCookie($key, $val);
        return $this;
    }

    public function setTimeout($timeout = 10)
    {
        if (empty($timeout) || !is_numeric($timeout)) {
            throw new \LogicException(sprintf("%s is not a valid timeout", $timeout));
        }

        $this->client->setConfig(array('timeout' => $timeout));

        return $this;
    }

    public function setAuthorization($user, $password = '')
    {
        $this->client->setAuth($user, $password);
    }

    public function getResponseBody()
    {
        $this->_getResponse();
        return $this->response->getBody();
    }

    public function getResponseHeaders()
    {
        $this->_getResponse();
        return $this->response->getHeaders();
    }

    public function getResponseStatus()
    {
        $this->_getResponse();
        return $this->response->getStatus();
    }

    public function setMethod($method)
    {
        $this->client->setMethod($method);
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
        $this->client->setUri($uri);
    }

    public function setFileUpload($filename, $formname, $data = null, $ctype = null)
    {
        $this->client->setFileUpload($filename, $formname, $data, $ctype);
    }

    public function reset()
    {
        $this->client->resetParameters();
        $this->response = null;
        $this->params = array();
    }

    private function _getResponse()
    {
        if ($this->response) {
            return $this->response;
        }

        if ($this->method == self::METHOD_POST) {
            $this->client->setParameterPost($this->params);
        } else {
            $this->client->setParameterGet($this->params);
        }
        $response = $this->client->request();
        $this->reset();

        $this->response = $response;
    }

}
<?php

namespace Mach\Bundle\NwlBundle\Client\Transport;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Zend_Http_Client based implementation of the Client interface
 *
 * @author Catalin Costache
 */
class HttpClientTransport implements HttpProtocolInterface, HttpTransportInterface
{
    protected $client;

    protected $response = null;

    protected $method = self::METHOD_GET;

    protected $params = array();
    protected $uri;
    protected $authBasic;
    protected $fileUpload = array();

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function addCookie($key, $val)
    {
        $this->client->withOptions(array('headers' => array('Cookie' => new Cookie($key, $val))));
        return $this;
    }

    public function setTimeout($timeout = 10)
    {
        if (empty($timeout) || !is_numeric($timeout)) {
            throw new \LogicException(sprintf("%s is not a valid timeout", $timeout));
        }
        $this->client->withOptions(array('timeout' => $timeout));
        return $this;
    }

    public function setAuthorization($user, $password = '')
    {
        $this->authBasic = array($user, $password);
    }

    public function getResponseBody()
    {
        $this->_getResponse();
        return $this->response->getContent();
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
        $this->fileUpload = [
            'filename' => $filename,
            'formname' => $formname,
            'data' => $data,
            'ctype' => $ctype,
        ];
        $this->params[$formname] = new DataPart($data, $filename);
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
        $options = array('auth_basic' => $this->authBasic);
        if ($this->method !== self::METHOD_POST) {
            $options['query'] = $this->params;
        } else {
            if (empty($this->fileUpload)) {
                $options['body'] = $this->params;
            } else {
                $formData = new FormDataPart($this->params);
                $options['body'] = $formData->bodyToIterable();
                $options['headers'] = $formData->getPreparedHeaders()->toArray();
            }
        }
        $response = $this->client->request($this->method, $this->uri, $options);
        $this->reset();

        $this->response = $response;
    }

}

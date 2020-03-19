<?php

namespace Mach\Bundle\NwlBundle\Client\Transport;

/**
 * This mock class is intended to be used in tests
 *
 * @author Rares Vlasceanu
 */
class MockClient implements HttpTransportInterface
{

    /**
     * @var \ArrayObject
     */
    protected $data;

    /**
     * @var string
     */
    protected $response;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->response ? : $this->data->getArrayCopy();
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return array();
    }

    public function getResponseStatus()
    {
        return 200;
    }

    public function reset()
    {
        $this->data = new \ArrayObject();
        $this->response = null;
    }

    /**
     * @param string $user
     * @param string $password
     */
    public function setAuthorization($user, $password = '')
    {
        $this->data['authorization'] = func_get_args();
    }

    /**
     * @param string $filename
     * @param string $formname
     * @param string $data
     * @param string $ctype
     */
    public function setFileUpload($filename, $formname, $data = null, $ctype = null)
    {
        $this->data['file_upload'] = func_get_args();
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->data['method'] = $method;
    }

    /**
     * @param type $nameOrArray
     * @param type $value
     */
    public function setParameter($nameOrArray, $value = null)
    {
        if (!isset($this->data['params'])) {
            $this->data['params'] = array();
        }
        if (is_array($nameOrArray)) {
            $this->data['params'] = array_merge($this->data['params'], $nameOrArray);
        } else {
            $this->data['params'][$nameOrArray] = $value;
        }
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->data['uri'] = $uri;
    }

    /**
     * @param string $responseBody
     */
    public function setResponseBody($responseBody)
    {
        $this->response = $responseBody;
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
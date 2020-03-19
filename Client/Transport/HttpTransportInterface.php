<?php

namespace Mach\Bundle\NwlBundle\Client\Transport;

/**
 * A minimal stateful client interface used by all resources
 * For now the protocol is assumed to be allways http
 *
 * @author Catalin Costache
 */
interface HttpTransportInterface
{
    /**
     * Set the full uri of the request
     *
     * @example: http://www.machteamsoft.ro/api/rest
     */
    public function setUri($uri);

    /**
     * Set the method of this request
     * One of the METHOD_ constants
     *
     * @param string $method
     */
    public function setMethod($method);

    /**
     * Set parameters
     * If the name parameter is an array then all parameters
     * must be assigned as key => value pairs
     *
     * @param mixed $nameOrArray - string or array
     * @param mixed $value
     */
    public function setParameter($nameOrArray, $value = null);

    /**
     * Add file upload to request
     *
     * - If $data is empty, $filename is treated as a local file and its contents
     *   will be sent as attachment
     *
     * - If $data is not empty, basename from $filename will be attachment name
     *   and $data will represent its content
     *
     * @param string $filename
     * @param $formname
     * @param mixed $data
     * @param string $ctype
     * @return void
     */
    public function setFileUpload($filename, $formname, $data = null, $ctype = null);

    /**
     * Set http credentials
     *
     * @param string $user
     * @param string $password
     */
    public function setAuthorization($user, $password = '');

    /**
     * Reset the state of the client parameters
     *
     * This method shold not only reset URI and parameters, but also file
     * uploads added using setFileUpload method
     *
     * @see Client::setFileUpload
     */
    public function reset();

    /**
     * @abstract
     * @return string
     */
    public function getResponseBody();

    /**
     * @abstract
     * @return array
     */
    public function getResponseHeaders();

    /**
     * @abstract
     * @return string
     */
    public function getResponseStatus();

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function addCookie($key, $value);

    /**
     * @param $timeout
     * @return mixed
     */
    public function setTimeout($timeout);
}
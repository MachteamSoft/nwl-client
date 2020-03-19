<?php

namespace Mach\Bundle\NwlBundle\Nwl;

/**
 * Simple class that holds information about file or data that will be send as
 * attachment in a mail using NwlClient
 *
 * @author Rares Vlasceanu
 * @package Mach
 * @subpackage Nwl
 * @version 2.0
 */
class Attachment
{

    /**
     * @var boolean
     */
    protected $isFile = false;
    /**
     * @var string
     */
    protected $fileName;
    /**
     * @var mixed
     */
    protected $data;
    /**
     * @var string
     */
    protected $contentType;

    /**
     * Create new attachment holder
     *
     * @param string $fileName
     * @param mixed $data
     * @param string $contentType
     * @throws \InvalidArgumentException
     */
    public function __construct($fileName, $data = null, $contentType = 'application/octet-stream')
    {
        if (empty($fileName)) {
            throw self::invalidFile();
        }
        if ($data === null) {
            if (!is_file($fileName)) {
                throw self::invalidFile($fileName);
            }
            $this->isFile = true;
        } else {
            $this->data = $data;
            $this->contentType = $contentType;
            $fileName = basename($fileName);
        }

        $this->fileName = $fileName;
    }

    /**
     * Returns true if $fileName exists in the filesystem
     * (attachment $data will be loaded from it)
     *
     * @return boolean
     */
    public function isFile()
    {
        return $this->isFile;
    }

    /**
     * Set attachment filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->fileName = $filename;
    }

    /**
     * Get attachment filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->fileName;
    }

    /**
     * Set attachment data
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get attachment data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set attachment mime type
     *
     * @param string $contentType 
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Get attachment mime type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $file
     * @return \InvalidArgumentException
     */
    public static function invalidFile($file = null)
    {
        if (empty($file)) {
            return new \InvalidArgumentException('Filename cannot be empty!');
        }
        return new \InvalidArgumentException('"' . $file . '" is invalid! (either is not readable or is not a file)');
    }
}
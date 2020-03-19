<?php

namespace Mach\Bundle\NwlBundle\Client\Transport;

/**
 * @author Rares Vlasceanu
 */
interface HttpProtocolInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
}

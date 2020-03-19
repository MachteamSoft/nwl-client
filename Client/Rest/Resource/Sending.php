<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

use Mach\Bundle\NwlBundle\Sender\FileQueueBuilder;

/**
 * @author Catalin Costache
 */
class Sending extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/sending';
    }

    /**
     * List all sendings
     *
     * @param int $nwl
     * @return array
     */
    public function all($nwl = null)
    {
        return $this->performGet(array('nwl' => $nwl));
    }

    /**
     * Get info about a specific sending
     *
     * @param int $sending
     * @return array
     */
    public function get($sending)
    {
        return $this->performGet(array('id' => $sending));
    }

    /**
     * Add a new sending
     *
     * @param int $nwl
     * @param int | FileQueueBuilder $source
     * @param array $contentIds
     * @return array
     */
    public function post($nwl, $source, array $contentIds = array())
    {
        $params = array(
            'nwl' => $nwl,
            'contentIds' => $contentIds
        );
        if ($source instanceof FileQueueBuilder) {
            $formName = 'file_queue_source';
            $this->getRestClient()
                ->getTransport()
                ->setFileUpload('file-queue', $formName, $source->getContents(), 'text/plain');
            $params['file_queue'] = $formName;
        }
        else {
            $params['query'] = $source;
        }

        return $this->performPost($params);
    }

    /**
     * Update the status for a sending
     *
     * @param int $sending
     * @param string $newStatus one of: 'new','pre-pause','pre-stop'
     * @return array
     */
    public function put($sending, $newStatus)
    {
        return $this->performPut(array('id' => $sending, 'newStatus' => $newStatus));
    }

    /**
     * List all sendings
     *
     * @param int $sending
     * @return array
     */
    public function delete($sending)
    {
        return $this->performDelete(array('id' => $sending));
    }

}
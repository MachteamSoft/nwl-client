<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * @author Catalin Costache
 */
class NwlContent extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/nwl-content';
    }

    /**
     * @param int $contentId
     * @return mixed
     */
    public function get($contentId)
    {
        return $this->performGet(array('id' => $contentId));
    }

    /**
     * Create a new nwl content
     *
     * @param int $nwlShortname
     * @param string $subject
     * @param string $body
     * @param int $layoutId
     * @param string $type - nwl type: instant, aggregate, newsletter
     * @param boolean $main - set content as default (for provided language)
     * @return string
     */
    public function post($nwlShortname, $subject, $body, $layoutId, $type = 'instant', $main = false)
    {
        return $this->performPost(array(
                    'nwl' => $nwlShortname,
                    'subject' => $subject,
                    'body' => $body,
                    'layout' => $layoutId,
                    'type' => $type,
                    'main' => $main
                ));
    }

    /**
     * Update the content
     *
     * @param int $contentId - content id
     * @param array $params - array with the column => value pairs to update
     *  One of: nwl, layout, lang, subject, body, main
     * @return mixed
     */
    public function put($contentId, array $params)
    {
        return $this->performPut(array('content' => $contentId) + $params);
    }

    /**
     * Delete the content
     *
     * @param integer $contentId
     * @return array
     */
    public function delete($contentId)
    {
        return $this->performDelete(array('id' => $contentId));
    }

}
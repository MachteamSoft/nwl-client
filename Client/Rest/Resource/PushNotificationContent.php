<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

class PushNotificationContent extends AbstractResource
{
    const TYPE_WEB = 'web';
    const TYPE_APP = 'app';

    public function getUri()
    {
        return '/push-notification-content';
    }

    public function get($contentId)
    {
        return $this->performGet(['id' => $contentId]);
    }

    public function post($nwlShortname, $subject, $body, $targetUrl, $type, $main = false, $icon = null, $image = null,
                         $isSticky = null, $actionsTitle = null)
    {
        return $this->performPost([
            'nwl' => $nwlShortname,
            'subject' => $subject,
            'body' => $body,
            'target-url' => $targetUrl,
            'type' => $type,
            'icon' => $icon,
            'image' => $image,
            'actions-title' => $actionsTitle,
            'isSticky' => $isSticky,
            'main' => $main
        ]);
    }

    public function put($contentId, array $params)
    {
        return $this->performPut(['content' => $contentId] + $params);
    }

}
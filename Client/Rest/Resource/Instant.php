<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

use Mach\Bundle\NwlBundle\Client\Rest\ClientInterface;
use Mach\Bundle\NwlBundle\Mail\Filter\MailFilterInterface;
use Mach\Bundle\NwlBundle\Mail\Interceptor\InterceptorInterface;
use Mach\Bundle\NwlBundle\Nwl\Attachment;
use Monolog\Logger;

/**
 * @author Catalin Costache
 */
class Instant extends AbstractResource
{
    /**
     * @var array
     */
    public static $priorities = array('normal', 'high', 'low');

    const ATTACHMENTS_FORM_NAME = 'attachments';

    private $chainInterceptor;

    public function __construct(ClientInterface $restClient, Logger $logger, InterceptorInterface $chainInterceptor)
    {
        parent::__construct($restClient, $logger);
        $this->chainInterceptor = $chainInterceptor;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return '/instant';
    }

    /**
     * Add new attachment to request
     *
     * @param Attachment $attach
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\Instant
     */
    public function addAttachment(Attachment $attach)
    {
        $transport = $this->getRestClient()->getTransport();
        $formName = self::ATTACHMENTS_FORM_NAME . '[]';

        if ($attach->isFile()) {
            $transport->setFileUpload($attach->getFilename(), $formName);
        }
        else {
            $transport->setFileUpload($attach->getFilename(), $formName, $attach->getData(), $attach->getContentType());
        }

        return $this;
    }

    /**
     * Set extra mail headers (to be merged over defaults)
     *
     * @param array $headers
     * @param array $headers
     * @return \Mach\Bundle\NwlBundle\Client\Rest\Resource\Instant
     */
    public function setMailHeaders(array $headers = array())
    {
        $this->getRestClient()->getTransport()->setParameter('headers', $headers);

        return $this;
    }

    /**
     * Start sending an instant mail
     *
     * @param string $nwlShortname
     * @param string $email
     * @param array $userData
     * @param array $data
     * @param string $priority - one of: low, medium, high
     * @param string $sendGroup - mail sendgroup
     * @param array $allowedContentIds
     * @throws \InvalidArgumentException
     * @return array
     */
    public function post($nwlShortname, $email, array $userData, $data = array(), $priority = null, $sendGroup = null, $allowedContentIds = array())
    {
        if (!empty($priority) && !in_array($priority, self::$priorities)) {
            throw new \InvalidArgumentException('Invalid priority!');
        }
        $userData = $this->chainInterceptor->intercept($nwlShortname, $userData);

        return $this->performPost(array(
            'id' => $nwlShortname,
            'priority' => $priority,
            'sendgroup' => $sendGroup,
            'data' => json_encode(array(array(
                'email' => $email,
                'userData' => $userData,
                'data' => $data
            ))),
            'allowedContentIds' => $allowedContentIds
        ));
    }

    /**
     * Send mail to multiple users at once
     *
     * @example for $data array:
     *
     * array(
     *    0 => array(
     *        'email' => 'test@email.com',
     *        'data' => array(
     *            'var1' => 'val1',
     *            'var2' => 'val2'
     *        ),
     *        'userData' => array(
     *            'userid' => 1,
     *            'username' => 'test',
     *            'firstname' => 'Ion'
     *            'lastname' => 'Popescu',
     *            'lang' => 'ro'
     *         )
     *     ),
     *
     *     ...
     *
     * );
     *
     * @param int $nwlShortname
     * @param array $data
     * @param string $priority
     * @param string $sendGroup
     * @param array $allowedContentIds - which content ids will be used for this mail
     * @throws \InvalidArgumentException
     * @return array
     */
    public function batch($nwlShortname, $data, $priority = null, $sendGroup = null, $allowedContentIds = array())
    {
        foreach ($data as $key => $d) {
            if (!isset($d['userData'])) {
                throw new \InvalidArgumentException('User data not found for item in ' . print_r($data, true));
            }
        }
        $data = $this->chainInterceptor->batchIntercept($nwlShortname, $data);

        return $this->performPost(array(
            'id' => $nwlShortname,
            'priority' => $priority,
            'sendgroup' => $sendGroup,
            'data' => json_encode($data),
            'allowedContentIds' => $allowedContentIds
        ));
    }
}
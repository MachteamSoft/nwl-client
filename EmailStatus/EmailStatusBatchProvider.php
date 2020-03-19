<?php

namespace Mach\Bundle\NwlBundle\EmailStatus;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EmailStatusBatchProvider
{
    CONST UNSUBSCRIBED = 'unsubscribed';
    CONST REPORTED_AS_SPAM = 'reportedAsSpam';

    private $currentProviderIndex;
    private $batchEmails;
    protected $providers;

    public function addProvider(EmailStatusProviderInterface $provider)
    {
        $this->providers[] = $provider;
        $this->currentProviderIndex = 0;
        $this->batchEmails = array();
    }

    public function getNextBatch($limit)
    {
        $this->batchEmails = $this->getBatchEmails($limit);
        if (!$this->hasNextBatch()) {
            $this->batchEmails = $this->getBatchEmails($limit, true);
        }

        return $this->batchEmails;
    }

    private function hasNextBatch()
    {
        return boolval(count($this->batchEmails));
    }

    private function getBatchEmails($limit, $incrementProviderIndex = false)
    {
        if ($incrementProviderIndex) {
            $this->currentProviderIndex++;
        }

        if (!($this->currentProviderIndex < count($this->providers))) {
            return array();
        }

        return $this->providers[$this->currentProviderIndex]->getNextBatch($limit);
    }
}
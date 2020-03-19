<?php

namespace Mach\Bundle\NwlBundle\Sender;

use Mach\Bundle\NwlBundle\ProgressiveVolumeCalculator;
use Mach\Bundle\NwlBundle\Client;

class IPControllProgressiveSender implements ProgressiveSenderInterface
{
    private $progressiveVolumeCalculator;
    private $sendgroups = array();
    private $currentSendgroup;
    private $currentSendgroupOffset = 0;
    private $currentSendgroupLimit = 0;
    private $fieldAsOffset = 0;
    private $lastOffset = 0;
    private $progressiveSender;

    /**
     * @param integer $limit
     * @param Client $nwlClient
     * @param string $nwlShortname
     * @param array $fieldAsOffset if set to something after each flush will update offset field with the
     *                                greatest value from this field (passed through addRow
     * @param array $allowedContentIds
     * @throws \InvalidArgumentException
     */
    public function __construct(ProgressiveVolumeCalculator $progressiveVolumeCalculator,
                                ProgressiveSenderInterface $progressiveSender , $fieldAsOffset = false)
    {
        $this->progressiveVolumeCalculator = $progressiveVolumeCalculator;
        $this->progressiveSender = $progressiveSender;
        $this->fieldAsOffset = $fieldAsOffset;
    }

    public function addRow(array $rowData)
    {
        $rowData[Client::SEND_GROUP_MAIL_FIELD] = $this->getCurrentSendgroup();
        if ($this->fieldAsOffset) {
            if (!isset($rowData[$this->fieldAsOffset])) {
                throw new \InvalidArgumentException(
                    sprintf('%s was set as an offset field and is not present in current row', $this->fieldAsOffset)
                );
            }
            if ($rowData[$this->fieldAsOffset] > $this->lastOffset) {
                $this->lastOffset = $rowData[$this->fieldAsOffset];
            }
        }

        $result = $this->progressiveSender->addRow($rowData);
        if ($result) {
            $this->updateOffsetAndAlreadySent($result);
        }

        return $result;
    }

    public function resetOffset()
    {
        $this->lastOffset = 0;
        $this->progressiveVolumeCalculator->setOffset($this->getNwlShortname(), 0);
    }

    public function flush()
    {
        $alreadySent = $this->count();
        $result = $this->progressiveSender->flush();
        $this->updateOffsetAndAlreadySent($alreadySent);

        return $result;
    }

    public function count()
    {
        return $this->progressiveSender->count();
    }

    public function getNwlShortname()
    {
        return $this->progressiveSender->getNwlShortname();
    }

    private function updateOffsetAndAlreadySent($alreadySent)
    {
        $this->progressiveVolumeCalculator->setOffset($this->getNwlShortname(), $this->lastOffset);
        $this->progressiveVolumeCalculator->incrementAlreadySent($this->getNwlShortname(), $alreadySent);
    }

    private function getCurrentSendgroup()
    {
        if (empty($this->sendgroups)) {
            $this->sendgroups = $this->getSendgroupLimits();
        }
        if (!$this->currentSendgroup || $this->currentSendgroupOffset > $this->currentSendgroupLimit) {
            $currentSendgroup = $this->getNextSendgroup();
            $this->currentSendgroupLimit = $this->sendgroups[$currentSendgroup];
            $this->currentSendgroupOffset = 0;

            return $this->currentSendgroup = $currentSendgroup;
        }
        $this->currentSendgroupOffset++;

        return $this->currentSendgroup;
    }

    private function getNextSendgroup()
    {
        if (!$this->currentSendgroup) {
            reset($this->sendgroups);

            return $this->currentSendgroup = key($this->sendgroups);
        }
        $stop = false;
        foreach ($this->sendgroups as $sendgroup => $limit) {
            if ($this->currentSendgroup == $sendgroup) {
                $stop = true;
                continue;
            }
            if ($stop) {
                return $sendgroup;
            }
        }

        throw new \OverflowException(sprintf('Mail quota for nwl %s exceded for today', $this->getNwlShortname()));
    }

    private function getSendgroupLimits()
    {
        $sendgroupLimits = $this->progressiveVolumeCalculator->getLimit($this->getNwlShortname(), true);
        if (empty($sendgroupLimits)) {
            throw new \LogicException(sprintf('No sendgroups available for nwl %s', $this->getNwlShortname()));
        }

        return $sendgroupLimits;
    }
}
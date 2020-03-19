<?php

namespace Mach\Bundle\NwlBundle;

use Mach\Bundle\NwlBundle\Entity\NwlProgressiveSender;
use Mach\Bundle\NwlBundle\Repository\NwlsMailSendings;
use Mach\Bundle\NwlBundle\Repository\NwlsProgressiveSender;

class ProgressiveVolumeCalculator
{
    protected $client;
    protected $nwlsProgressiveSender;
    protected $mailSendings;
    protected $latestVolume = array();

    public function __construct(Client $client, NwlsProgressiveSender $nwlsProgressiveSender, NwlsMailSendings $mailSendings)
    {
        $this->client = $client;
        $this->nwlsProgressiveSender = $nwlsProgressiveSender;
        $this->mailSendings = $mailSendings;
    }

    public function getOffset($nwlShortname)
    {
        return $this->nwlsProgressiveSender->getOffset($nwlShortname);
    }

    public function getLimit($nwlShortname, $bySendgroup = false)
    {
        $senders = $this->nwlsProgressiveSender->getAllSenders();

        $today = new \DateTime();
        $mailSending = $this->mailSendings->findOneBy(array('nwlShortname' => $nwlShortname, 'sendingDate' => $today));
        $alreadySent = $mailSending ? $mailSending->getAlreadySent() : 0;
        $limitsBySendGroup = $this->calculateLimits($senders, $nwlShortname);

        if (!$alreadySent) {
            return $bySendgroup ? $limitsBySendGroup : array_sum($limitsBySendGroup);
        }
        foreach ($limitsBySendGroup as $key => $limit) {
            if ($limit > $alreadySent) {
                $limitsBySendGroup[$key] = $limit - $alreadySent;
                break;
            }
            unset($limitsBySendGroup[$key]);

            $alreadySent -= $limit;
        }

        return $bySendgroup ? $limitsBySendGroup : array_sum($limitsBySendGroup);
    }

    public function getAllLimits()
    {
        $senders = $this->nwlsProgressiveSender->getAllSenders();
        $limits = array();

        /** @var NwlProgressiveSender $sender*/
        foreach ($senders as $sender) {
            $shortname = $sender->getNwlShortname();
            $limits[$shortname] = $this->calculateLimits($senders, $shortname);
        }

        return $limits;
    }

    public function setOffset($nwlShortname, $offset)
    {
        $this->nwlsProgressiveSender->setOffset($nwlShortname, $offset);
    }

    public function incrementAlreadySent($nwlShortname, $alreadySent)
    {
        $today = new \DateTime();
        $today->setTime(0,0);
        $this->mailSendings->incrementAlreadySentWith($nwlShortname, $today, $alreadySent);
    }

    private function calculateLimits(array $senders, $nwlShortname)
    {
        $sendgroupPrioritySums = array();
        $limitsBySendGroup = array();
        $limits = array();
        $existsShortname = false;

        /** @var NwlProgressiveSender $sender*/
        foreach ($senders as $sender) {
            if ($nwlShortname == $sender->getNwlShortname()) {
                $existsShortname = true;
            }
            if (!$sender->isActive()) {
                continue;
            }
            foreach ($sender->getSendgroupLimits() as $limit) {
                if (isset($sendgroupPrioritySums[$limit->getSendgroup()])) {
                    $sum = $sendgroupPrioritySums[$limit->getSendgroup()];
                } else {
                    $sum = 0;
                }
                $sendgroupPrioritySums[$limit->getSendgroup()] = $sum + $sender->getPriority();
                $limits[$limit->getSendgroup()] = $limit->getLimit();
            }
        }

        if (!$existsShortname) {
            throw new \LogicException(sprintf('Nwl by shortname %s not found', $nwlShortname));
        }

        /** @var NwlProgressiveSender $sender*/
        foreach ($senders as $sender) {
            if (!$sender->isActive()) {
                continue;
            }

            if ($nwlShortname != $sender->getNwlShortname()) {
                continue;
            }
            foreach ($sender->getSendgroupLimits() as $limit) {
                if (!isset($sendgroupPrioritySums[$limit->getSendgroup()])) {
                    continue;
                }
                $currentLimit = (int) floor($limit->getLimit() * $sender->getPriority() / $sendgroupPrioritySums[$limit->getSendgroup()]);
                $limitsBySendGroup[$limit->getSendgroup()] = $currentLimit;
            }
        }

        return $limitsBySendGroup;
    }
}

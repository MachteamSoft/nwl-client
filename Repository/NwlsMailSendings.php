<?php
namespace Mach\Bundle\NwlBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Mach\Bundle\NwlBundle\Entity\NwlMailSendings;

class NwlsMailSendings extends EntityRepository
{
    public function incrementAlreadySentWith($nwlShortname, $sendingDate, $incrementValue)
    {
        /** @var NwlMailSendings $nwlMailSendingEntry */
        $nwlMailSendingEntry = $this->findOneBy(['nwlShortname' => $nwlShortname, 'sendingDate' => $sendingDate]);

        if ($nwlMailSendingEntry) {
            $nwlMailSendingEntry->incrementAlreadySent($incrementValue);
            $this->_em->flush();
            return;
        }

        $nwlMailSendingEntry = new NwlMailSendings($nwlShortname, $sendingDate, $incrementValue);
        $this->_em->persist($nwlMailSendingEntry);
        $this->_em->flush();
    }
}
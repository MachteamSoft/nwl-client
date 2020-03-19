<?php
namespace Mach\Bundle\NwlBundle\Repository;

use Doctrine\ORM\EntityRepository;

class NwlsProgressiveSender extends EntityRepository
{
    public function getAllSenders()
    {
        $qb = $this->createQueryBuilder('ps')
                ->select('ps, sl')
                ->innerJoin('ps.sendgroupLimits', 'sl');

        return $qb->getQuery()->getResult();
    }

    public function getOffset($nwlShortname)
    {
        return $this->getNwlProgressiveEntry($nwlShortname)->getOffset();
    }

    public function setOffset($nwlShortname, $offset)
    {
        $this->_em->clear();
        $nwlProgressive = $this->getNwlProgressiveEntry($nwlShortname);
        $nwlProgressive->setOffset($offset);
        $this->_em->persist($nwlProgressive);
        $this->_em->flush();
    }

    protected function getNwlProgressiveEntry($nwlShortname)
    {
        $nwlProgressive = $this->findOneBy(['nwlShorname' => $nwlShortname]);
        if (!$nwlProgressive) {
            throw new \LogicException(sprintf('Nwl by shortname %s not found'), $nwlShortname);
        }

        return $nwlProgressive;
    }

    public function reset($nwlShortname)
    {
        $qb = $this->createQueryBuilder('v')
            ->update('MachNwlBundle:NwlProgressiveSender', 'v')
            ->set('v.offset', 0)
            ->where('v.nwlShorname = :shortname')
            ->setParameter('shortname', $nwlShortname);

        return $qb->getQuery()->execute();
    }
} 
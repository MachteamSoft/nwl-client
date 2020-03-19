<?php
namespace Mach\Bundle\NwlBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Mach\Bundle\NwlBundle\Entity\NwlMailSendings;

class EmailsStatus extends EntityRepository
{
    protected $insertValues = [];

    public function flipTables()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        try {
            $connection->beginTransaction();
            $sqlRename = "RENAME TABLE nwl_emails.emails_status TO nwl_emails.emails_status_tmp,
                                nwl_emails.emails_status_new TO nwl_emails.emails_status,
                                nwl_emails.emails_status_tmp TO nwl_emails.emails_status_new";
            $connection->exec($sqlRename);
            $sqlRemove = "TRUNCATE nwl_emails.emails_status_new";
            $connection->exec($sqlRemove);
            $connection->commit();
            $connection->close();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();
            return false;
        }

        return true;
    }

    public function rawInsert()
    {
        $sql = "INSERT IGNORE INTO nwl_emails.emails_status_new (email, bad_mail, unsubscribed, reported_spam) VALUES";
        if (!count($this->insertValues)) {
            return;
        }

        foreach ($this->insertValues as $row) {
            $sql = sprintf('%s %s', $sql, sprintf('("%s", "%s", "%s", "%s"),', $row['email'], $row['bad_mail'],
                $row['unsubscribed'], $row['reported_spam']));
        }

        $sql[strlen($sql)-1] = ';';
        $connection = $this->getEntityManager()->getConnection();
        $connection->getConfiguration()->setSQLLogger(null);
        $connection->exec($sql);
        $this->getEntityManager()->clear();
        $connection->close();
        $this->resetInsertValues();
    }

    public function addInsertRow($email, $isBadMail, $hasUnsubed, $hasReportedSpam)
    {
        $this->insertValues[] = array('email' => $email, 'bad_mail' => $isBadMail, 'unsubscribed' => $hasUnsubed,
            'reported_spam' => $hasReportedSpam);
    }

    public function resetInsertValues()
    {
        $this->insertValues = array();
    }

    public function getBatch(array $emails, $hydrate = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder('e INDEX BY e.email')
            ->select('e')
            ->where('e.email IN (:emails)')
            ->setParameter('emails', $emails);

        return $qb->getQuery()->getResult($hydrate);
    }

    public function update($email, $field, $value)
    {
        $qb = $this->createQueryBuilder('e')
            ->update('MachNwlBundle:EmailStatus', 'e')
            ->set(sprintf('e.%s', $field), ':value')
            ->where('e.email = :email')
            ->setParameter('value', $value)
            ->setParameter('email', $email);

        $qb->getQuery()->execute();
    }
}
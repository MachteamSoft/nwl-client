<?php

namespace Mach\Bundle\NwlBundle\Command;

use Doctrine\ORM\EntityManager;
use Mach\Bundle\NwlBundle\Client;
use Mach\Bundle\NwlBundle\EmailStatus\EmailStatusBatchProvider;
use Mach\Bundle\NwlBundle\Entity\EmailStatus;
use Mach\Bundle\NwlBundle\Repository\EmailsStatus;
use Mach\Bundle\NwlBundle\Sender\IPControllProgressiveSender;
use Senti\Bundle\UserBundle\Repository\Profiles;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailStatusSyncCommand extends ContainerAwareCommand
{
    CONST BATCH_LIMIT = 150;
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var Client
     */
    protected $nwlClient;

    /**
     * @var EmailsStatus
     */
    protected $emailStatusRepo;

    /**
     * @var EmailStatusBatchProvider
     */
    protected $emailStatusProvider;
    protected $totalProcessed;
    protected $batchesSaved;

    protected function configure()
    {
        $this->setName('emails:status-sync')
            ->setDescription('Syncs the email statuses from the nwl system');

    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;
        $this->nwlClient = $this->getContainer()->get('nwl.client');
        $this->emailStatusRepo = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository("MachNwlBundle:EmailStatus");
        $this->emailStatusProvider = $this->getContainer()->get('nwl.email_status_provider');
        $this->totalProcessed = 0;
        $this->batchesSaved = 0;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output->writeln('Started');
        while ($records = $this->emailStatusProvider->getNextBatch(self::BATCH_LIMIT)) {
            $this->output->writeln(sprintf('The starting userid for the next batch is: %s',
                $records[count($records) - 1]['userid']));
            $emails = $this->extractUserEmails($records);
            $badMailsFiltration = $this->tryExecuteRequest(function () use ($emails) {
                return $this->nwlClient->badmail()->get($emails);
            });
            $unsubscribeSpamFiltration = $this->tryExecuteRequest(function () use ($emails) {
                return $this->nwlClient->unsubscribespammails()->get($emails);
            });

            $this->persistMails($badMailsFiltration['mails'], $unsubscribeSpamFiltration['mails']);
            gc_collect_cycles();
        }
        $this->emailStatusRepo->flipTables();
    }

    private function tryExecuteRequest(callable $runnable, $maxRetries = 3)
    {
        $retries = 0;
        $lastException = null;
        do {
            try {
                return $runnable();
            } catch (\Exception $e) {
                $lastException = $e;
                $this->output->writeln($e->getMessage());
            }
            $retries++;
            $sleepSeconds = 15 * $retries;
            $this->output->writeln(sprintf("Sleep %s seconds", $sleepSeconds));
            sleep($sleepSeconds);
        } while ($retries <= $maxRetries);
        throw $lastException;
    }

    protected function persistMails($badMailsFiltration, $unsubscribeSpamFiltration)
    {
        foreach ($badMailsFiltration as $email => $isBadMail) {
            if (!$isBadMail && $this->shouldInsertRow($unsubscribeSpamFiltration, $email)) {
                continue;
            }
            $this->emailStatusRepo->addInsertRow($email, $isBadMail,
                isset($unsubscribeSpamFiltration[$email][EmailStatusBatchProvider::UNSUBSCRIBED]),
                isset($unsubscribeSpamFiltration[$email][EmailStatusBatchProvider::REPORTED_AS_SPAM]));
            $this->totalProcessed += 1;
        }

        $this->emailStatusRepo->rawInsert();
        $this->output->writeln('Memory used: ' . round(memory_get_usage() / (1024 * 1024), 2) . 'MB');
        $this->output->writeln(sprintf('Processed %s', $this->totalProcessed));
        gc_collect_cycles();
    }

    protected function extractUserEmails($records)
    {
        $emails = array();
        foreach ($records as $record) {
            $emails[] = $record['email'];
        }

        return $emails;
    }

    private function shouldInsertRow($unsubscribeSpamFiltration, $email) {
        return !isset($unsubscribeSpamFiltration[$email][EmailStatusBatchProvider::UNSUBSCRIBED]) &&
        !isset($unsubscribeSpamFiltration[$email][EmailStatusBatchProvider::REPORTED_AS_SPAM]);
    }
}

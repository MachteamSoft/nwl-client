<?php

namespace Mach\Bundle\NwlBundle\EmailStatus;

use Mach\Bundle\NwlBundle\Entity\NwlProgressiveSender;
use Mach\Bundle\NwlBundle\Repository\NwlsMailSendings;
use Mach\Bundle\NwlBundle\Repository\NwlsProgressiveSender;

interface EmailStatusProviderInterface
{
    public function getNextBatch($limit);
}
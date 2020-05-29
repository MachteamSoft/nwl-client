<?php
namespace Mach\Bundle\NwlBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="nwl_emails.emails_status")
 * @ORM\Entity(repositoryClass="Mach\Bundle\NwlBundle\Repository\EmailsStatus")
 */
class EmailStatus
{
    /**
     * @ORM\Column(name="email", type="string", nullable=false)
     * @ORM\Id
     */
    protected $email;

    /**
     * @var boolean $badMail
     *
     * @ORM\Column(name="bad_mail", type="boolean", nullable=true)
     */
    protected $badMail;

    /**
     * @var boolean $unsubscribed
     *
     * @ORM\Column(name="unsubscribed", type="boolean", nullable=true)
     */
    protected $unsubscribed;

    /**
     * @var boolean reportedSpam
     *
     * @ORM\Column(name="reported_spam", type="boolean", nullable=true)
     */
    protected $reportedSpam;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isBadMail()
    {
        return $this->badMail;
    }

    public function setBadMail($badMail)
    {
        $this->badMail = $badMail;
    }

    public function isUnsubscribed()
    {
        return $this->unsubscribed;
    }

    public function setUnsubscribed($unsubscribed)
    {
        $this->unsubscribed = $unsubscribed;
    }

    public function hasReportedSpam()
    {
        return $this->reportedSpam;
    }

    public function setReportedSpam($reportedSpam)
    {
        $this->reportedSpam = $reportedSpam;
    }
}

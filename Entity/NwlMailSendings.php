<?php
namespace Mach\Bundle\NwlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="nwl_mail_sendings")
 * @ORM\Entity(repositoryClass="Mach\Bundle\NwlBundle\Repository\NwlsMailSendings")
 */
class NwlMailSendings
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="nwl_shortname", type="string", nullable=false)
     */
    protected $nwlShortname;

    /**
     * @ORM\Column(name="already_sent", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $alreadySent;

    /**
     * @var date
     *
     * @ORM\Column(name="sending_date", type="date", nullable=false)
     */
    protected $sendingDate;

    public function __construct($nwlShortname, $sendingDate, $alreadySent = 0)
    {
        $this->nwlShortname = $nwlShortname;
        $this->sendingDate = $sendingDate;
        $this->alreadySent = $alreadySent;
    }

    public function getNwlShortname()
    {
        return $this->nwlShortname;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAlreadySent()
    {
        return $this->alreadySent;
    }

    public function setAlreadySent($alreadySent)
    {
        $this->alreadySent = $alreadySent;
    }

    public function getSendingDate()
    {
        return $this->sendingDate;
    }

    public function setSendingDate($sendingDate)
    {
        $this->sendingDate = $sendingDate;
    }

    public function incrementAlreadySent($incrementValue)
    {
        $this->alreadySent += $incrementValue;
    }
}
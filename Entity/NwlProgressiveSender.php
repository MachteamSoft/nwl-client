<?php
namespace Mach\Bundle\NwlBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="nwl_progressive_sender")
 * @ORM\Entity(repositoryClass="Mach\Bundle\NwlBundle\Repository\NwlsProgressiveSender")
 */
class NwlProgressiveSender
{
    /**
     * @ORM\Id
     * @ORM\Column(name="nwl_shortname", type="string", length=30, nullable=false)
     */
    protected $nwlShorname;

    /**
     * @ORM\Column(name="offset", type="integer", nullable=false, options={"unsigned":true})
     */
    protected $offset;

    /**
     * @ORM\Column(name="priority", type="smallint", nullable=false)
     */
    protected $priority;

    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    protected $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    protected $endDate;

    /**
     * @ORM\ManyToMany(targetEntity="SendgroupLimits", inversedBy="nwlProgressiveSender")
     * @ORM\JoinTable(name="nwl_sendgroup_mapping",
     *                joinColumns={@ORM\JoinColumn(name="nwl_shortname", referencedColumnName="nwl_shortname")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="sendgroup", referencedColumnName="sendgroup")}
     * )
     */
    protected $sendgroupLimits;

    public function __construct($nwlShortname, $offset, $priority)
    {
        $this->nwlShorname = $nwlShortname;
        $this->offset = $offset;
        $this->priority = $priority;
        $this->sendgroupLimits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getNwlShortname()
    {
        return $this->nwlShorname;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getSendgroupLimits()
    {
        return $this->sendgroupLimits;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function setSendgroupLimits(array $sendGroupLimits)
    {
        $this->sendgroupLimits = new ArrayCollection($sendGroupLimits);
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function isActive()
    {
        $now = time();
        if ($this->endDate != null && $this->startDate != null) {
            return $now >= $this->startDate->getTimestamp() && $now <= $this->endDate->getTimestamp();
        }

        if ($this->startDate != null) {
            return $now >= $this->startDate->getTimestamp();
        }

        return true;
    }
}
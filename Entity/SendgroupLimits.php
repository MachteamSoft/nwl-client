<?php

namespace Mach\Bundle\NwlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="feelings.nwl_sendgroup_limits")
 * @ORM\Entity()
 */
class SendgroupLimits
{
    /**
     * @ORM\Column(name="sendgroup", type="string", length=20, nullable=false)
     * @ORM\Id
     */
    protected $sendgroup;

    /**
     * @ORM\Column(name="limit", type="integer")
     */
    protected $limit;

    /**
     *
     * @ORM\ManyToMany(targetEntity="NwlProgressiveSender",mappedBy="sendgroupLimits")
     */
    private $nwlProgressiveSender;

    public function __construct($sendgroup, $limit)
    {
        $this->sendgroup = $sendgroup;
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getSendgroup()
    {
        return $this->sendgroup;
    }

    /**
     * @return string
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
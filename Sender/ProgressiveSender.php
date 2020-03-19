<?php

namespace Mach\Bundle\NwlBundle\Sender;

use Mach\Bundle\NwlBundle\Client;

/**
 * Limit the number of items that can exist in queue at any time,
 * and perform sending::post operation when the queue is full
 *
 * Important! Do not forget to call flush at the end of processing cycle, as
 * some items could remain in queue.
 *
 * @author Rares Vlasceanu
 */
class ProgressiveSender extends FileQueueBuilder implements ProgressiveSenderInterface
{
    /**
     * @var integer
     */
    protected $limit;

    /**
     * @var Client
     */
    protected $nwlClient;

    /**
     * @var string
     */
    protected $nwl;

    /**
     * @var array
     */
    protected $allowedContentIds;

    /**
     * @param integer $limit
     * @param Client $nwlClient
     * @param string $nwlShortname
     * @param array $allowedContentIds
     * @throws \InvalidArgumentException
     */
    public function __construct($limit, Client $nwlClient, $nwlShortname, array $allowedContentIds = array())
    {
        parent::__construct();

        if (intval($limit) <= 0) {
            throw new \InvalidArgumentException('Invalid limit provided!');
        }
        $this->limit = $limit;
        $this->nwlClient = $nwlClient;
        $this->nwl = $nwlShortname;
        $this->allowedContentIds = $allowedContentIds;
    }

    /**
     * Add new row to queue
     *
     * Important!
     *
     * All values contained in $rowData variable shoud be scalar, like:
     *      array(
     *          id => 10,
     *          email => "test@domain.com"
     *      )
     *
     * Providing non scalar values (arrays, objects) will cause an error to be trown
     *      array(
     *          id => 10,
     *          emails => array("test@domain.com", "new@domain.com")  <-- invalid value
     *      )
     *
     * @param array $rowData
     * @return integer (items sent) | void
     */
    public function addRow(array $rowData)
    {
        parent::addRow($rowData);

        if ($this->count() == $this->limit) {
            return $this->flush();
        }
    }

    /**
     * Send items in queue and then clear it
     *
     * @return integer
     */
    public function flush()
    {
        $items = $this->count();
        if ($items) {
            $this->nwlClient->sending()->post($this->nwl, $this, $this->allowedContentIds);
            $this->reset();
        }

        return $items;
    }

    public function getNwlShortname()
    {
        return $this->nwl;
    }

    public function resetOffset() {}
}
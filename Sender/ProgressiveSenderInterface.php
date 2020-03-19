<?php

namespace Mach\Bundle\NwlBundle\Sender;


interface ProgressiveSenderInterface
{
    /**
     * @param array $rowData
     * @return integer (items sent) | void
     */
    public function addRow(array $rowData);

    /**
     * @return integer
     */
    public function flush();

    public function count();

    public function getNwlShortname();

    public function resetOffset();
}
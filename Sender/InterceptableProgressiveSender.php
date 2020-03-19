<?php

namespace Mach\Bundle\NwlBundle\Sender;


use Mach\Bundle\NwlBundle\Mail\Interceptor\InterceptorInterface;

class InterceptableProgressiveSender implements ProgressiveSenderInterface
{
    private $progressiveSender;
    private $chainInterceptor;
    private $buffer;
    private $bufferLimit = 100;

    public function __construct(ProgressiveSenderInterface $progressiveSender, InterceptorInterface $chainInterceptor)
    {
        $this->progressiveSender = $progressiveSender;
        $this->chainInterceptor = $chainInterceptor;
    }

    public function addRow(array $rowData)
    {
        $this->buffer[] = $rowData;
        if (count($this->buffer) < $this->bufferLimit) {
            return;
        }
        return $this->applyChainInterceptor();
    }

    public function flush()
    {
        if (!empty($this->buffer)) {
            $this->applyChainInterceptor();
        }

        return $this->progressiveSender->flush();
    }

    public function count()
    {
        return $this->progressiveSender->count() + count($this->buffer);
    }

    public function getNwlShortname()
    {
        return $this->progressiveSender->getNwlShortname();
    }

    public function resetOffset()
    {
        $this->progressiveSender->resetOffset();
    }

    public function setBufferLimit($limit)
    {
        $this->bufferLimit = $limit;
    }

    private function applyChainInterceptor()
    {
        $rows = $this->chainInterceptor->batchIntercept($this->getNwlShortname(), $this->buffer);
        $this->buffer = array();
        return $this->addRows($rows);
    }

    private function addRows(array $rows)
    {
        $itemsSend = null;
        foreach ($rows as $row) {
            $result = $this->progressiveSender->addRow($row);
            if ($result) {
                $itemsSend = $result;
            }
        }

        return $itemsSend;
    }
}
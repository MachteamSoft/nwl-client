<?php

namespace Mach\Bundle\NwlBundle\Debug;

/**
 * @author Catalin Costache
 */
class DebugBacktrace implements DebugInfo
{
    /**
     * Return the calling trace of this method or the previous
     * $stepsBack trace
     *
     * @param int $stepsBack
     * @return string
     */
    public function getCaller($stepsBack = 0)
    {
        $trace = debug_backtrace(0);

        return $trace[1 + $stepsBack];
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getTrace($offset = 0, $limit = 10)
    {
        $trace = debug_backtrace(0);

        return array_slice($trace, $offset, $limit);
    }

    /**
     * @return string
     */
    public function getDebugInfo()
    {
        $fullTrace = $this->getTrace();

        $trace = reset($fullTrace);
        while ($trace && preg_match('#Mach[\\/]Bundle[\\/]NwlBundle#i', $trace['file'])) {
            $trace = next($fullTrace);
        }
        if (empty($trace)) {
            $trace = $this->getCaller(5); // fallback
        }

        unset($trace['args'], $trace['type']);

        return $trace;
    }
}

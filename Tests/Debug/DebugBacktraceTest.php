<?php

namespace Mach\Bundle\NwlBundle\Debug;

/**
 * @author Catalin Costache
 */
class DebugBacktraceTest extends \PHPUnit\Framework\TestCase
{
    public function testGetCaller()
    {
        $d = new DebugBacktrace();
        $caller = $d->getCaller();
        $this->assertEquals('testGetCaller', $caller['function']);
    }
}
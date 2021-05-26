<?php

namespace CashBerry\Calculation;

use PHPUnit\Framework\TestCase;

/**
 * Class XIRRTest
 */
class XIRRTest extends TestCase
{
    public function testCalculate(): void
    {
        $xirp = new XIRR();

        $result = $xirp->calculate(-1000, 1021.6, 7);
        self::assertEquals(266.93330420685, $result);

        $result = $xirp->calculate(-1000, 1192.4, 14);
        self::assertEquals(13886.005722064287, $result);

        $result = $xirp->calculate(-1000, 1320, 21);
        self::assertEquals(15766.248479375494, $result);

        $result = $xirp->calculate(-1000, 1495.9, 30);
        self::assertEquals(15798.634803781, $result);
    }
}

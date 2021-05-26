<?php

namespace CashBerry\FinancialFunctionsWithTheExcelFunctionNames;

use PHPUnit\Framework\TestCase;

/**
 * Class FinancialTest
 */
class FinancialTest extends TestCase
{
    public function testXIRP()
    {
        $f = new Financial();
        $result = $f->XIRR(array(-10000,2750,4250,3250,2750), array(
            mktime(0,0,0,1,1,2008),
            mktime(0,0,0,3,1,2008),
            mktime(0,0,0,10,30,2008),
            mktime(0,0,0,2,15,2009),
            mktime(0,0,0,4,1,2009),
        ), 0.1);
        self::assertEquals(0.37336284255981, $result);
    }
}

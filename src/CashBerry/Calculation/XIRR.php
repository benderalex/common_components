<?php

namespace CashBerry\Calculation;

use Carbon\Carbon;
use CashBerry\FinancialFunctionsWithTheExcelFunctionNames\Financial;

/**
 * Class XIRR
 */
class XIRR
{
    /** @var Financial */
    protected $financial;

    /**
     * XIRR constructor.
     */
    public function __construct()
    {
        $this->financial = new Financial();
    }

    /**
     * @param float $amount
     * @param float $interest
     * @param int $period
     *
     * @return float|int
     */
    public function calculate(float $amount, float $interest, int $period): float
    {
        $values = $this->getValues($amount, $interest, $period);

        $dates = $this->getDates($period);

        return $this->financial->XIRR($values, $dates) * 100;
    }

    /**
     * @param int $period
     *
     * @return array
     */
    protected function getDates(int $period): array
    {
        $dates = [];
        $currentDate = (new Carbon())->startOfDay();
        for ($i = 1; $i <= $period; $i++) {
            $dates[] = $currentDate->getTimestamp();
            $currentDate->addDay();
        }

        return $dates;
    }

    /**
     * @param float $amount
     * @param float $interest
     * @param int $period
     *
     * @return array
     */
    protected function getValues(float $amount, float $interest, int $period): array
    {
        $values = array_fill(1, $period - 2, 0);
        array_unshift($values, $amount);
        $values[] = $interest;

        return $values;
    }
}

<?php

namespace Wexample\SymfonyMoney\Enum;

/**
 * How an adjustment (discount, fee) is expressed: an amount in minor units,
 * or a rate in basis points (1000 = 10 %).
 */
enum PriceUnit: string
{
    case Money = 'money';
    case Percent = 'percent';
}

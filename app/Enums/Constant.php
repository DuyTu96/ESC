<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Constant enum.
 */
class Constant extends BaseEnum
{
    // Records limit from DB at once in batch
    const BATCH_LIMIT_ROW = 1000;

    // Reduction rate of CNJ point
    const REDUCTION_RATE = 0.05;

    // Number of days from reservation date to sales report deadline
    const DAYS_FOR_SALES_REPORT = 1;

    // A general member can be a VIP member if spending more than AMOUNT_BASIS_FOR_VIP_MEMBER yen
    // AMOUNT_BASIS_FOR_VIP_MEMBER yen = ( reservations.total_payment_amount + point_return_requests.using_expense )
    const AMOUNT_BASIS_FOR_VIP_MEMBER = 500000;

    const BASIC_LIMIT = 10;

    const DAY_OF_WEEK_JP = ['日', '月', '火', '水', '木', '金', '土'];

    const SHOP_GROUP_USER_ALL_SHOP_NAME = "'全店舗'";

    // limit time to expired resetpassword
    const MAX_TIME_EXPIRED_RESET_PASSWORD = 5;

    const MAX_TIME_EXPIRED_SEND_EMAIL = 5;
}

<?php

namespace App\Shared\Exceptions;

use DomainException;

/**
 * Vi pham quy tac nghiep vu (BR-01..BR-10, muc 9.3).
 *
 * Service nem ngoai le nay; handler (bootstrap/app.php) chuyen thanh
 * thong bao loi than thien (web) hoac HTTP 422 (API).
 */
class BusinessRuleException extends DomainException
{
    public function __construct(string $message, public readonly string $rule = '')
    {
        parent::__construct($message);
    }
}

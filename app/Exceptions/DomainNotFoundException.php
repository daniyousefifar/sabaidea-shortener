<?php

declare(strict_types=1);

namespace App\Exceptions;

class DomainNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The domain you requested does not exists.';
}
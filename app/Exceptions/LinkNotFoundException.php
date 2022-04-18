<?php

declare(strict_types=1);

namespace App\Exceptions;

class LinkNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The link you requested does not exists.';
}
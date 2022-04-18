<?php

declare(strict_types=1);

namespace App\Exceptions;

class UserNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The user you requested does not exists.';
}
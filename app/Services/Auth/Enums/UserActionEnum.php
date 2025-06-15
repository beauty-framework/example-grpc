<?php
declare(strict_types=1);

namespace App\Services\Auth\Enums;

enum UserActionEnum: string
{
    case Login = 'login';
    case Register = 'register';
    case Logout = 'logout';
    case Validate = 'validate';
}

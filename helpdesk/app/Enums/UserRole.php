<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Staff = 'staff';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Staff => 'Support staff',
            self::Admin => 'Administrator',
        };
    }
}

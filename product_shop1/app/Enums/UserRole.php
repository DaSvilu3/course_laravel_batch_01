<?php

namespace App\Enums;

/**
 * Application roles.
 *
 * To add a new role (e.g. "manager"), add a case here, translate it in
 * lang/{locale}/enums.php, then protect routes with middleware('role:manager').
 */
enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return __('enums.user_role.'.$this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}

<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Agent = 'agent';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Member',
            self::Agent => 'Agent',
            self::Admin => 'Administrator',
        };
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin => [
                'properties.view', 'properties.create', 'properties.update',
                'properties.delete', 'properties.approve', 'properties.feature',
                'users.manage', 'agents.verify', 'reports.view', 'settings.manage',
            ],
            self::Agent => [
                'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            ],
            self::User => ['properties.view'],
        };
    }
}

<?php

use App\Helpers\RoleHelper;

if (! function_exists('roleDisplay')) {
    function roleDisplay(string $role): string
    {
        return RoleHelper::getDisplayName($role);
    }
}

if (! function_exists('roleBadgeClass')) {
    function roleBadgeClass(string $role): string
    {
        return RoleHelper::getBadgeClass($role);
    }
}

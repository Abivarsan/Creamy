<?php

namespace App\Enums;

/**
 * UserRole Enum
 * 
 * Defines the three explicit roles mandated by the assessment:
 * - OWNER: Full administrative access (create, read, update, delete across items and customers).
 * - MANAGER: Operational management (create, read, update; cannot delete items or customers).
 * - CASHIER: Storefront operations (can view and edit items e.g. stock, can manage customers; cannot create or delete items, cannot delete customers).
 */
enum UserRole: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case CASHIER = 'cashier';

    /**
     * Get all values as a simple array for validation rules.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

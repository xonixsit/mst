<?php

namespace App\Enums;

enum VisaStatus: string
{
    case CITIZEN = 'citizen';
    case PERMANENT_RESIDENT = 'permanent_resident';
    case H1B = 'h1b';
    case F1 = 'f1';
    case J1 = 'j1';
    case L1 = 'l1';
    case OTHER = 'other';

    /**
     * Get the display label for the visa status
     */
    public function label(): string
    {
        return match($this) {
            self::CITIZEN => 'US Citizen',
            self::PERMANENT_RESIDENT => 'Permanent Resident',
            self::H1B => 'H-1B',
            self::F1 => 'F-1 Student',
            self::J1 => 'J-1 Exchange',
            self::L1 => 'L-1 Intracompany',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get all visa status options for forms
     */
    public static function options(): array
    {
        return collect(self::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        })->toArray();
    }

    /**
     * Check if this visa status requires date of entry
     */
    public function requiresDateOfEntry(): bool
    {
        return $this !== self::CITIZEN;
    }
}
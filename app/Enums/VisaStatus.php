<?php

namespace App\Enums;

enum VisaStatus: string
{
    case CITIZEN = 'citizen';
    case PERMANENT_RESIDENT = 'permanent_resident';
    case H1B = 'h1b';
    case H4 = 'h4';
    case F1 = 'f1';
    case F2 = 'f2';
    case J1 = 'j1';
    case J2 = 'j2';
    case L1 = 'l1';
    case L2 = 'l2';
    case O1 = 'o1';
    case O2 = 'o2';
    case E1 = 'e1';
    case E2 = 'e2';
    case TN = 'tn';
    case B1_B2 = 'b1_b2';
    case K1 = 'k1';
    case K3 = 'k3';
    case OTHER = 'other';

    /**
     * Get the display label for the visa status
     */
    public function label(): string
    {
        return match($this) {
            self::CITIZEN => 'US Citizen',
            self::PERMANENT_RESIDENT => 'Permanent Resident (Green Card)',
            self::H1B => 'H-1B (Specialty Occupation)',
            self::H4 => 'H-4 (H-1B Dependent)',
            self::F1 => 'F-1 (Student)',
            self::F2 => 'F-2 (F-1 Dependent)',
            self::J1 => 'J-1 (Exchange Visitor)',
            self::J2 => 'J-2 (J-1 Dependent)',
            self::L1 => 'L-1 (Intracompany Transfer)',
            self::L2 => 'L-2 (L-1 Dependent)',
            self::O1 => 'O-1 (Extraordinary Ability)',
            self::O2 => 'O-2 (O-1 Support)',
            self::E1 => 'E-1 (Treaty Trader)',
            self::E2 => 'E-2 (Treaty Investor)',
            self::TN => 'TN (NAFTA Professional)',
            self::B1_B2 => 'B-1/B-2 (Business/Tourist)',
            self::K1 => 'K-1 (Fiancé)',
            self::K3 => 'K-3 (Spouse of US Citizen)',
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

    /**
     * Check if this visa status allows work authorization
     */
    public function allowsWork(): bool
    {
        return match($this) {
            self::CITIZEN, 
            self::PERMANENT_RESIDENT, 
            self::H1B, 
            self::L1, 
            self::O1, 
            self::E1, 
            self::E2, 
            self::TN => true,
            self::H4, 
            self::F2, 
            self::J2, 
            self::L2, 
            self::O2 => false, // May have work authorization with EAD
            self::F1, 
            self::J1 => false, // Limited work authorization
            self::B1_B2, 
            self::K1, 
            self::K3 => false,
            self::OTHER => false,
        };
    }

    /**
     * Check if this visa status has tax treaty benefits
     */
    public function hasTaxTreatyBenefits(): bool
    {
        return match($this) {
            self::F1, 
            self::J1, 
            self::J2 => true,
            default => false,
        };
    }

    /**
     * Get tax residency implications
     */
    public function getTaxResidencyNote(): string
    {
        return match($this) {
            self::CITIZEN, 
            self::PERMANENT_RESIDENT => 'Subject to US tax on worldwide income',
            self::F1, 
            self::J1 => 'May be exempt from substantial presence test for 5 years (F-1) or 2 years (J-1)',
            self::H1B, 
            self::L1, 
            self::O1, 
            self::E1, 
            self::E2, 
            self::TN => 'Subject to substantial presence test for tax residency',
            default => 'Tax residency depends on substantial presence test and specific circumstances',
        };
    }
}
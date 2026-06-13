<?php
namespace App\Enums;

enum VaccinationStatus: string {
    case PENDING = 'pending';
    case DONE = 'done';
    case MISSED = 'missed';
}

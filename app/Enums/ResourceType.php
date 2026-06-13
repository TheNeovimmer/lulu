<?php
namespace App\Enums;

enum ResourceType: string {
    case PDF = 'pdf';
    case EBOOK = 'ebook';
    case VIDEO = 'video';
    case GUIDE = 'guide';
}

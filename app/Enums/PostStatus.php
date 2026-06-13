<?php
namespace App\Enums;

enum PostStatus: string {
    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';
    case REPORTED = 'reported';
}

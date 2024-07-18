<?php

namespace App\Enums;

enum ZipCodeSource: string
{
    case GOOGLE = 'google';
    case USPS = 'usps';
    case OPENSTREETMAP = 'openstreetmap';
    case CANADA_POST = 'canada_post';
}

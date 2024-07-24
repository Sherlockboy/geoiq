<?php

namespace App\Enums;

enum ZipCodeSource: string
{
    case GOOGLE = 'google';
    case USPS = 'usps';
    case OPENSTREETMAP = 'openstreetmap';
    case CANADA_POST = 'canada_post';
    case ZIP_CODE_ORG = 'zip_code_org';
}

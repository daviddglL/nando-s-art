<?php

namespace App\Enums;

enum Role: string
{
    use HasValues;

    case User = 'user';
    case Admin = 'admin';
}

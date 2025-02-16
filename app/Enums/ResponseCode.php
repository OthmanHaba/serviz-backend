<?php

namespace App\Enums;

enum ResponseCode: int
{
    case Success = 201;
    case NotFound = 404;
    case Unauthorized = 401;
    case Forbidden = 403;
    case NoContent = 204;
}

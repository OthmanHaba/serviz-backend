<?php

namespace App\Enums;

enum ServiceStatus
{
    case PendingUserApproved ;
    case PendingProviderApproved ;
    case InProgress ;
    case Completed ;

}

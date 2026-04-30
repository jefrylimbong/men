<?php

namespace App\Filament\Resources\CustomerData\Pages;

use App\Filament\Resources\CustomerData\CustomerDataResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerData extends CreateRecord
{
    protected static string $resource = CustomerDataResource::class;
}

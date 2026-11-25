<?php
namespace App\Filament\Resources\InstancePages;
use App\Filament\Resources\InstanceResource;
class ListRecords extends \Filament\Resources\Pages\ListRecords {protected static string $resource=InstanceResource::class; protected function getHeaderActions(): array {return [\Filament\Actions\CreateAction::make()];}}

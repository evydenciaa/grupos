<?php
namespace App\Filament\Resources\ContentPages;
use App\Filament\Resources\ContentResource;
class ListRecords extends \Filament\Resources\Pages\ListRecords {protected static string $resource=ContentResource::class; protected function getHeaderActions(): array {return [\Filament\Actions\CreateAction::make()];}}

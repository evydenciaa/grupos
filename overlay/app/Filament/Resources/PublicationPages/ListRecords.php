<?php
namespace App\Filament\Resources\PublicationPages;
use App\Filament\Resources\PublicationResource;
class ListRecords extends \Filament\Resources\Pages\ListRecords {protected static string $resource=PublicationResource::class; protected function getHeaderActions(): array {return [\Filament\Actions\CreateAction::make()];}}

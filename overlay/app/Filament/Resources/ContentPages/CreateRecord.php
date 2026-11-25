<?php
namespace App\Filament\Resources\ContentPages;
use App\Filament\Resources\ContentResource;
class CreateRecord extends \Filament\Resources\Pages\CreateRecord {protected static string $resource=ContentResource::class; protected function mutateFormDataBeforeCreate(array $data): array {return \App\Services\MediaInspector::inspect($data);} }

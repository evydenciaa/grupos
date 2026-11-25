<?php
namespace App\Filament\Resources\ContentPages;
use App\Filament\Resources\ContentResource;
class EditRecord extends \Filament\Resources\Pages\EditRecord {protected static string $resource=ContentResource::class; protected function mutateFormDataBeforeSave(array $data): array {return \App\Services\MediaInspector::inspect($data);} }

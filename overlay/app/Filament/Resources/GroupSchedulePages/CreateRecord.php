<?php
namespace App\Filament\Resources\GroupSchedulePages;
use App\Filament\Resources\GroupScheduleResource;
class CreateRecord extends \Filament\Resources\Pages\CreateRecord {
 protected static string $resource=GroupScheduleResource::class;
 protected function mutateFormDataBeforeCreate(array $data): array{return GroupScheduleResource::validateRule($data);}
}

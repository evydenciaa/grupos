<?php
namespace App\Filament\Resources\GroupSchedulePages;
use App\Filament\Resources\GroupScheduleResource;
class EditRecord extends \Filament\Resources\Pages\EditRecord {
 protected static string $resource=GroupScheduleResource::class;
 protected function mutateFormDataBeforeSave(array $data): array{return GroupScheduleResource::validateRule($data);}
}

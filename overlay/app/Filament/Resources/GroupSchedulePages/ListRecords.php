<?php
namespace App\Filament\Resources\GroupSchedulePages;
use App\Filament\Resources\GroupScheduleResource;
class ListRecords extends \Filament\Resources\Pages\ListRecords {
 protected static string $resource=GroupScheduleResource::class;
 protected function getHeaderActions(): array{return [\Filament\Actions\CreateAction::make()];}
}

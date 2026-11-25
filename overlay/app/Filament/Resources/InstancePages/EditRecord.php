<?php
namespace App\Filament\Resources\InstancePages;
use App\Filament\Resources\InstanceResource;
class EditRecord extends \Filament\Resources\Pages\EditRecord {protected static string $resource=InstanceResource::class;  protected function afterSave(): void {if($this->record->wasChanged(['token','client_token'])){$this->record->groups()->update(['authorized'=>false,'enabled'=>false,'metadata_at'=>null]);$this->record->update(['connected'=>false,'synced_at'=>null]);}}}

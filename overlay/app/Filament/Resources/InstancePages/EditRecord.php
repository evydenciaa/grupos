<?php
namespace App\Filament\Resources\InstancePages;
use App\Filament\Resources\InstanceResource;
class EditRecord extends \Filament\Resources\Pages\EditRecord {
 protected static string $resource=InstanceResource::class;
 protected function afterSave(): void {
  if($this->record->wasChanged(['token','client_token'])){
   $this->record->groups()->update(['authorized'=>false,'enabled'=>false,'authorized_at'=>null,'metadata_at'=>null,'member_count'=>null,'invitation_link'=>null,'invitation_source'=>null,'invitation_checked_at'=>null,'present'=>false]);
   $this->record->update(['connected'=>false,'synced_at'=>null,'sync_state'=>'identity_changed','phone'=>null]);
  }
 }
}

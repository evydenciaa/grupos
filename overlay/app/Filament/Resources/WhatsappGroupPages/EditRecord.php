<?php
namespace App\Filament\Resources\WhatsappGroupPages;
use App\Filament\Resources\WhatsappGroupResource;
use Illuminate\Validation\ValidationException;
class EditRecord extends \Filament\Resources\Pages\EditRecord {
 protected static string $resource=WhatsappGroupResource::class;
 protected function mutateFormDataBeforeSave(array $data): array {
  $g=$this->record->fresh();$data=array_intersect_key($data,array_flip(['invitation_link','authorized','authorization_notes','enabled','max_daily_publications']));
  if($data['authorized']??false){
   if(!$g->present||!$g->metadata_at||$g->metadata_at->lt(now()->subDays(7))||$g->is_admin===null||$g->admin_only===null||$g->announcement!==false||($g->admin_only&&!$g->is_admin)||!filled($data['authorization_notes']??null))
    throw ValidationException::withMessages(['data.authorized'=>'Atualize os dados e confirme as regras e a autorização do grupo.']);
   $data['authorized_at']=now();
  }else{$data['authorized_at']=null;$data['enabled']=false;}
  if(array_key_exists('invitation_link',$data)&&$data['invitation_link']!==$g->invitation_link){$data['invitation_source']=filled($data['invitation_link'])?'manual':null;$data['invitation_checked_at']=null;}
  return $data;
 }
}

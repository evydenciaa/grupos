<?php
namespace App\Filament\Resources\PublicationPages;
use App\Filament\Resources\PublicationResource;
use App\Models\{WhatsappGroup,Content};
use Illuminate\Validation\ValidationException;
class CreateRecord extends \Filament\Resources\Pages\CreateRecord {
 protected static string $resource=PublicationResource::class;
 protected function mutateFormDataBeforeCreate(array $data): array {
  if(!WhatsappGroup::authorizedForPlanning()->whereKey($data['whatsapp_group_id'])->exists())throw ValidationException::withMessages(['data.whatsapp_group_id'=>'Grupo não apto. Revise a autorização e os metadados.']);
  $c=Content::findOrFail($data['content_id']);
  if(!$c->enabled||($c->expires_at&&$c->expires_at->lt(\Carbon\Carbon::parse($data['scheduled_at']))))throw ValidationException::withMessages(['data.content_id'=>'Conteúdo desativado ou vencido.']);
  return array_intersect_key($data,array_flip(['whatsapp_group_id','content_id','scheduled_at']))+['caption_snapshot'=>$c->caption,'path_snapshot'=>$c->path,'hash_snapshot'=>$c->sha256,'kind_snapshot'=>$c->kind];
 }
}

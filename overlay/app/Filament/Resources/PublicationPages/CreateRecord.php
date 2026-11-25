<?php
namespace App\Filament\Resources\PublicationPages;
use App\Filament\Resources\PublicationResource;
class CreateRecord extends \Filament\Resources\Pages\CreateRecord {protected static string $resource=PublicationResource::class;  protected function mutateFormDataBeforeCreate(array $data): array { $group=\App\Models\WhatsappGroup::findOrFail($data['whatsapp_group_id']); if(!$group->authorized||!$group->enabled||!$group->present) throw \Illuminate\Validation\ValidationException::withMessages(['data.whatsapp_group_id'=>'Grupo não autorizado.']); return array_intersect_key($data,array_flip(['whatsapp_group_id','content_id','scheduled_at']));}}

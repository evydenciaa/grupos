<?php
namespace Tests\Feature;
use App\Models\{Instance,WhatsappGroup};
use App\Services\MetadataRefresh;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
class MetadataRefreshTest extends TestCase {
 use RefreshDatabase;
 public function test_only_one_group_per_call_and_counts_members(): void {
  $i=Instance::create(['name'=>'Teste','external_id'=>'demo','token'=>'fake','client_token'=>'fake','phone'=>'5511999999999','connected'=>true,'enabled'=>true,'sync_state'=>'complete','synced_at'=>now()]);
  foreach(['1001-group','1002-group'] as $id)WhatsappGroup::create(['instance_id'=>$i->id,'external_id'=>$id,'name'=>$id]);
  Http::preventStrayRequests();Http::fake(fn($r)=>str_contains($r->url(),'/status')?Http::response(['connected'=>true]):(str_contains($r->url(),'/device')?Http::response(['phone'=>'5511999999999']):Http::response(['phone'=>'1001-group','subject'=>'Grupo','description'=>'Regras','adminOnlyMessage'=>false,'isGroupAnnouncement'=>false,'participants'=>[['phone'=>'5511999999999','isAdmin'=>false,'isSuperAdmin'=>false],['phone'=>'5511888888888','isAdmin'=>false]]])));
  $this->assertSame('updated',app(MetadataRefresh::class)->run());
  $this->assertDatabaseHas('whatsapp_groups',['external_id'=>'1001-group','member_count'=>2]);$this->assertDatabaseHas('whatsapp_groups',['external_id'=>'1002-group','member_count'=>null]);Http::assertSentCount(3);
 }
}

<?php
namespace Tests\Feature;
use App\Models\{Instance,WhatsappGroup};
use App\Services\{GroupOverview,GroupSync};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
class GroupOverviewTest extends TestCase {
 use RefreshDatabase;
 private function instance(): Instance{return Instance::create(['name'=>'Teste','external_id'=>'demo','token'=>'fake','client_token'=>'fake','phone'=>'5511999999999','connected'=>true,'enabled'=>true]);}
 public function test_count_is_memberships_not_unique_people(): void {
  $i=$this->instance();
  foreach([40,60,null] as $n=>$total)WhatsappGroup::create(['instance_id'=>$i->id,'external_id'=>(string)(1000+$n).'-group','name'=>'G'.$n,'member_count'=>$total,'metadata_at'=>$total===null?null:now(),'last_message_at'=>now()]);
  $s=app(GroupOverview::class)->totals();$this->assertSame(100,$s['members']);$this->assertSame(3,$s['present']);$this->assertSame(1,$s['pending']);
 }
 public function test_failed_second_page_preserves_list(): void {
  $i=$this->instance();WhatsappGroup::create(['instance_id'=>$i->id,'external_id'=>'999-group','name'=>'Original']);
  $rows=array_map(fn($n)=>['phone'=>(string)(1000+$n).'-group','name'=>'G'.$n,'isGroup'=>true],range(1,50));
  Http::preventStrayRequests();Http::fake(fn($r)=>str_contains($r->url(),'/status')?Http::response(['connected'=>true]):(str_contains($r->url(),'/device')?Http::response(['phone'=>'5511999999999']):(str_contains($r->url(),'page=1&')?Http::response($rows):Http::response([],503))));
  try{app(GroupSync::class)->sync($i);$this->fail('Expected error');}catch(\RuntimeException){}
  $this->assertDatabaseCount('whatsapp_groups',1);$this->assertDatabaseHas('whatsapp_groups',['external_id'=>'999-group','present'=>true]);$this->assertSame('failed',$i->fresh()->sync_state);
 }
}

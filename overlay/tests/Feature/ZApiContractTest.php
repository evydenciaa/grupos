<?php
namespace Tests\Feature;
use App\Models\Instance;
use App\Services\ZApiClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
class ZApiContractTest extends TestCase {
 public function test_media_payloads(): void {
  Http::preventStrayRequests(); Http::fake();
  $i=new Instance(['external_id'=>'instance','token'=>'token','client_token'=>'client']);$api=new ZApiClient($i);
  foreach(['image','video'] as $kind){$p=$api->mediaPayload('120363019502650977-group',$kind,'https://example.com/media','Legenda');$this->assertSame('send-'.$kind,$p['endpoint']);$this->assertSame('Legenda',$p['body']['caption']);$this->assertSame('https://example.com/media',$p['body'][$kind]);}
  Http::assertNothingSent();
 }
 public function test_group_pagination_contract(): void {
  Http::preventStrayRequests(); Http::fake(['https://api.z-api.io/*'=>Http::response([])]);
  $i=new Instance(['external_id'=>'instance','token'=>'token','client_token'=>'client']);
  (new ZApiClient($i))->groups(2);
  Http::assertSent(fn($r)=>$r->method()==='GET'&&str_contains($r->url(),'page=2')&&str_contains($r->url(),'pageSize=50')&&$r->hasHeader('Client-Token','client'));
 }
}

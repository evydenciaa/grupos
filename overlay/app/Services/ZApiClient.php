<?php
namespace App\Services;
use App\Models\Instance;
use Illuminate\Support\Facades\Http;
use RuntimeException;
class ZApiClient {
 public function __construct(private Instance $instance){}
 private function request(string $method,string $path,array $data=[]): array {
  $url='https://api.z-api.io/instances/'.rawurlencode($this->instance->external_id).'/token/'.rawurlencode($this->instance->token).'/'.$path;
  try {
   $http=Http::acceptJson()->asJson()->withHeaders(['Client-Token'=>$this->instance->client_token])->connectTimeout(5)->timeout(15)->withOptions(['allow_redirects'=>false]);
   $response=$method==='GET'?$http->get($url,$data):$http->post($url,$data);
  } catch (\Throwable) {throw new RuntimeException('Falha de transporte Z-API; resultado pode ser desconhecido.');}
  if (!$response->successful()) throw new RuntimeException('Z-API HTTP '.$response->status());
  $json=$response->json(); if(!is_array($json)) throw new RuntimeException('Resposta Z-API inválida.'); return $json;
 }
 public function status(): array {return $this->request('GET','status');}
 public function device(): array {return $this->request('GET','device');}
 public function groups(int $page,int $size=50): array {return $this->request('GET','groups',['page'=>$page,'pageSize'=>$size]);}
 public function metadata(string $id,bool $full=false): array {return $this->request('GET',($full?'group-metadata/':'light-group-metadata/').rawurlencode($id));}
 public function invite(string $id): array {return $this->request('GET','group-invitation-link/'.rawurlencode($id));}
 // Contract builder only. No live POST is exposed in this starter.
 public function mediaPayload(string $id,string $kind,string $url,string $caption): array {
  if(!in_array($kind,['image','video'],true)||!str_starts_with($url,'https://')) throw new RuntimeException('Mídia inválida.');
  if(!preg_match('/^(?:[0-9]+-group|[0-9]+-[0-9]+)$/D',$id)) throw new RuntimeException('ID de grupo inválido.');
  return ['endpoint'=>'send-'.$kind,'body'=>['phone'=>$id,$kind=>$url,'caption'=>$caption,'viewOnce'=>false]];
 }
}

<?php
namespace App\Services;
use App\Models\{Instance,WhatsappGroup};
use Illuminate\Support\Facades\{Cache,DB};
use Illuminate\Support\Carbon;
use RuntimeException;
class GroupSync {
 public function sync(Instance $instance): int {
  return Cache::lock('grupos-sync-'.$instance->id,600)->block(1,function()use($instance){
   $instance->refresh(); if(!$instance->enabled) throw new RuntimeException('Instância desabilitada.');
   $api=new ZApiClient($instance); $connected=($api->status()['connected']??false)===true; $instance->update(['connected'=>$connected]);
   if(!$connected) throw new RuntimeException('Instância desconectada.');
   $phone=$api->device()['phone']??null;
   if(!is_string($phone)||trim($phone)==='') throw new RuntimeException('Identidade da conta não confirmada.');
   if($instance->phone!==null && $instance->phone!==$phone){
    $instance->groups()->update(['authorized'=>false,'enabled'=>false,'metadata_at'=>null]);
   }
   $instance->update(['phone'=>$phone]);
   $seen=[]; $stamp=now(); $complete=false;
   for($page=1;$page<=20;$page++){
    $rows=$api->groups($page); if(!array_is_list($rows)) throw new RuntimeException('Formato de listagem inesperado.');
    foreach($rows as $row){
     if(!is_array($row)||!isset($row['phone'],$row['name'])||!is_string($row['phone'])||!preg_match('/^(?:[0-9]+-group|[0-9]+-[0-9]+)$/D',$row['phone'])) throw new RuntimeException('Registro de grupo inválido.');
     $id=$row['phone']; if(isset($seen[$id])) throw new RuntimeException('Paginação repetida; sincronização incompleta.');
     $seen[$id]=true; $ms=$row['lastMessageTime']??null;
     WhatsappGroup::updateOrCreate(['instance_id'=>$instance->id,'external_id'=>$id],['name'=>mb_substr((string)$row['name'],0,255),'present'=>true,'seen_at'=>$stamp,'last_message_at'=>is_numeric($ms)&&$ms>0?Carbon::createFromTimestampMs((int)$ms,'UTC'):null]);
    }
    if(count($rows)<50){$complete=true;break;}
   }
   if(!$complete) throw new RuntimeException('Limite de páginas atingido; ausência não foi reconciliada.');
   DB::transaction(function()use($instance,$seen,$stamp){$instance->groups()->whereNotIn('external_id',array_keys($seen))->update(['present'=>false,'enabled'=>false,'authorized'=>false]);$instance->update(['synced_at'=>$stamp]);});
   return count($seen);
  });
 }
 public function refresh(WhatsappGroup $group): void {
  $api=new ZApiClient($group->instance); $d=$api->metadata($group->external_id,true);
  if(($d['phone']??null)!==$group->external_id||!isset($d['subject'],$d['participants'])||!is_array($d['participants'])) throw new RuntimeException('Metadados incompletos.');
  $phone=$group->instance->phone; $admin=null; foreach($d['participants'] as $p){if($phone&&($p['phone']??null)===$phone){$admin=($p['isAdmin']??false)===true||($p['isSuperAdmin']??false)===true;break;}}
  $description=(string)($d['description']??''); $data=['name'=>mb_substr((string)$d['subject'],0,255),'description'=>$description,'member_count'=>count($d['participants']),'admin_only'=>is_bool($d['adminOnlyMessage']??null)?$d['adminOnlyMessage']:null,'is_admin'=>$admin,'metadata_at'=>now()];
  if($description!==($group->description??'')||$admin===null) $data+=['authorized'=>false,'enabled'=>false];
  $link=$d['invitationLink']??null; if(is_string($link)&&preg_match('~^https://chat\.whatsapp\.com/[A-Za-z0-9]+$~D',$link)) $data['invitation_link']=$link;
  $group->update($data);
 }
}

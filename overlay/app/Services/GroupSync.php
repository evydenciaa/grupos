<?php
namespace App\Services;
use App\Models\{Instance,WhatsappGroup};
use Illuminate\Support\Facades\{Cache,DB};
use Illuminate\Support\Carbon;
use RuntimeException;
class GroupSync {
 private function identify(Instance $instance,ZApiClient $api): string {
  if(!$instance->enabled) throw new RuntimeException('Instância desabilitada.');
  $connected=($api->status()['connected']??false)===true;
  $instance->update(['connected'=>$connected]);
  if(!$connected) throw new RuntimeException('Instância desconectada.');
  $phone=$api->device()['phone']??null;
  if(!is_string($phone)||!preg_match('/^[0-9]{8,20}$/D',$phone)) throw new RuntimeException('Identidade não confirmada.');
  if($instance->phone!==$phone){
   DB::transaction(function()use($instance,$phone){
    $instance->groups()->update(['authorized'=>false,'enabled'=>false,'authorized_at'=>null,'metadata_at'=>null,'invitation_link'=>null,'invitation_source'=>null,'invitation_checked_at'=>null,'member_count'=>null,'present'=>false]);
    $instance->update(['phone'=>$phone,'synced_at'=>null,'sync_state'=>'identity_changed']);
   });
  }
  return $phone;
 }
 private static function flag(mixed $value): ?bool {
  return match($value){true,1,'1','true'=>true,false,0,'0','false'=>false,default=>null};
 }
 public function sync(Instance $instance): int {
  return Cache::lock('grupos-sync-'.$instance->id,600)->block(1,function()use($instance){
   $instance->refresh(); $instance->update(['sync_attempted_at'=>now(),'sync_state'=>'running']);
   try {
    $api=new ZApiClient($instance); $phone=$this->identify($instance,$api);
    $seen=[];$stamp=now();$complete=false;
    for($page=1;$page<=20;$page++){
     $rows=$api->groups($page,50); if(!array_is_list($rows)||count($rows)>50) throw new RuntimeException('Formato de listagem inesperado.');
     foreach($rows as $row){
      if(!is_array($row)||($row['isGroup']??null)!==true||!is_string($row['phone']??null)||!is_string($row['name']??null)||!preg_match('/^(?:[0-9]+-group|[0-9]+-[0-9]+)$/D',$row['phone'])) throw new RuntimeException('Registro de grupo inválido.');
      $id=$row['phone']; if(isset($seen[$id])) throw new RuntimeException('Paginação repetida.');
      $ms=$row['lastMessageTime']??null;$unread=$row['unread']??null;
      $seen[$id]=['name'=>mb_substr($row['name'],0,255),'present'=>true,'seen_at'=>$stamp,'last_message_at'=>is_numeric($ms)&&$ms>0&&$ms<4102444800000?Carbon::createFromTimestampMs((int)$ms,'UTC'):null,'archived'=>self::flag($row['archived']??null),'muted'=>self::flag($row['isMuted']??null),'unread_count'=>is_numeric($unread)&&$unread>=0&&$unread<=2147483647?(int)$unread:null];
     }
     if(count($rows)<50){$complete=true;break;}
    }
    if(!$complete) throw new RuntimeException('Limite de páginas atingido.');
    if(($api->device()['phone']??null)!==$phone) throw new RuntimeException('Conta alterada durante a leitura.');
    DB::transaction(function()use($instance,$seen,$stamp){
     foreach($seen as $id=>$data) WhatsappGroup::updateOrCreate(['instance_id'=>$instance->id,'external_id'=>$id],$data);
     $instance->groups()->whereNotIn('external_id',array_keys($seen))->update(['present'=>false,'enabled'=>false,'authorized'=>false,'authorized_at'=>null]);
     $instance->update(['synced_at'=>$stamp,'sync_state'=>'complete']);
    });
    return count($seen);
   } catch(\Throwable $e){$instance->update(['sync_state'=>'failed','connected'=>false]);throw new RuntimeException('Sincronização incompleta; dados anteriores preservados, exceto revogação por troca de conta.');}
  });
 }
 public function refresh(WhatsappGroup $group,bool $includeInvite=false): void {
  Cache::lock('grupos-sync-'.$group->instance_id,600)->block(1,function()use($group,$includeInvite){
   $group->refresh();$instance=$group->instance; $api=new ZApiClient($instance);$phone=$this->identify($instance,$api);$group->refresh();
   if(!$group->present) throw new RuntimeException('Sincronize a lista novamente.');
   $d=$api->metadata($group->external_id,$includeInvite);
   if(($d['phone']??null)!==$group->external_id||!is_string($d['subject']??null)||!is_array($d['participants']??null)||!array_is_list($d['participants'])) throw new RuntimeException('Metadados incompletos.');
   $admin=null;foreach($d['participants'] as $p){if(is_array($p)&&($p['phone']??null)===$phone){$admin=self::flag($p['isAdmin']??null);if(($p['isSuperAdmin']??null)===true)$admin=true;break;}}
   $description=is_string($d['description']??null)?$d['description']:null;
   $only=self::flag($d['adminOnlyMessage']??null);$announcement=self::flag($d['isGroupAnnouncement']??null);
   $data=['name'=>mb_substr($d['subject'],0,255),'description'=>$description,'member_count'=>count($d['participants']),'admin_only'=>$only,'is_admin'=>$admin,'announcement'=>$announcement,'metadata_at'=>now()];
   if($description!==$group->description||$only!==$group->admin_only||$announcement!==$group->announcement||$admin!==$group->is_admin||$admin===null||$only===null||$announcement!==false||($only===true&&$admin!==true)) $data+=['authorized'=>false,'enabled'=>false,'authorized_at'=>null];
   if($includeInvite){
    $link=$d['invitationLink']??null;
    $valid=is_string($link)&&preg_match('~^https://chat\.whatsapp\.com/[A-Za-z0-9_-]+$~D',$link)&&$admin===true;
    if($valid||$group->invitation_source!=='manual') $data+=['invitation_link'=>$valid?$link:null,'invitation_source'=>$valid?'api':null,'invitation_checked_at'=>now()];
   } elseif($admin!==true&&$group->invitation_source==='api') $data+=['invitation_link'=>null,'invitation_source'=>null,'invitation_checked_at'=>now()];
   $group->update($data);
  });
 }
}

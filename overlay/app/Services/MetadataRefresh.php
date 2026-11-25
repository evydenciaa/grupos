<?php
namespace App\Services;
use App\Models\WhatsappGroup;
use Illuminate\Support\Facades\Cache;
class MetadataRefresh {
 /** Only one group per invocation, at most once per hour on failure. */
 public function run(): string {
  return Cache::lock('grupos-metadata-batch',180)->get(function(){
   $group=WhatsappGroup::query()->where('present',true)
    ->whereHas('instance',fn($q)=>$q->where('enabled',true)->where('connected',true)->where('sync_state','complete')->where('synced_at','>=',now()->subDays(7)))
    ->where(fn($q)=>$q->whereNull('metadata_at')->orWhere('metadata_at','<',now()->subDays(1)))
    ->where(fn($q)=>$q->whereNull('metadata_attempted_at')->orWhere('metadata_attempted_at','<',now()->subHour()))
    ->orderByRaw('CASE WHEN metadata_attempted_at IS NULL THEN 0 ELSE 1 END')->orderBy('metadata_attempted_at')->orderBy('id')->first();
   if(!$group)return 'idle';
   $group->forceFill(['metadata_attempted_at'=>now()])->save();
   try{app(GroupSync::class)->refresh($group);return 'updated';}catch(\Throwable){return 'failed';}
  })??'busy';
 }
}

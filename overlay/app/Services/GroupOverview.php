<?php
namespace App\Services;
use App\Models\WhatsappGroup;
class GroupOverview {
 public function totals(): array {
  $present=WhatsappGroup::query()->where('present',true);
  $fresh=(clone $present)->where('metadata_at','>=',now()->subDays(7))->whereNotNull('member_count');
  return ['present'=>(clone $present)->count(),'recent'=>WhatsappGroup::recent()->count(),'authorized'=>WhatsappGroup::authorizedForPlanning()->count(),'members'=>(int)(clone $fresh)->sum('member_count'),'known'=>(clone $fresh)->count(),'pending'=>(clone $present)->count()-(clone $fresh)->count()];
 }
}

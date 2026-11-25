<?php
namespace App\Services;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
class RecurrenceCalendar {
 public const TZ='America/Sao_Paulo';
 public static function validate(array $rule,int $maxDaily): array {
  $start=$rule['starts_on']??'';$end=$rule['ends_on']??'';
  foreach([$start,$end] as $date){if(!is_string($date)||!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$date)||!checkdate((int)substr($date,5,2),(int)substr($date,8,2),(int)substr($date,0,4))) throw new InvalidArgumentException('Data inválida.');}
  if($end<$start) throw new InvalidArgumentException('Fim anterior ao início.');
  $interval=filter_var($rule['interval_days']??1,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>365]]);
  if($interval===false) throw new InvalidArgumentException('Intervalo inválido.');
  $days=$rule['weekdays']??[];$times=$rule['times']??[];
  if(!is_array($days)||!is_array($times)||!$days||!$times) throw new InvalidArgumentException('Informe dias e horários.');
  foreach($days as $d) if(!in_array((string)$d,['1','2','3','4','5','6','7'],true)) throw new InvalidArgumentException('Dia inválido.');
  foreach($times as $t) if(!is_string($t)||!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D',$t)) throw new InvalidArgumentException('Horário inválido.');
  $days=array_values(array_unique(array_map('intval',$days))); $times=array_values(array_unique($times));sort($times);
  if(count($times)>$maxDaily||$maxDaily<1) throw new InvalidArgumentException('Horários excedem a frequência autorizada do grupo.');
  return ['starts_on'=>$start,'ends_on'=>$end,'interval_days'=>$interval,'weekdays'=>$days,'times'=>$times];
 }
 /** Preview only. No side effects, no API calls. Dates stored locally; occurrences returned UTC. */
 public static function upcoming(array $rule,CarbonImmutable $from,int $maxDaily,int $limit=14): array {
  $r=self::validate($rule,$maxDaily);$from=$from->setTimezone(self::TZ);
  $anchor=CarbonImmutable::parse($r['starts_on'],self::TZ)->startOfDay();
  $end=CarbonImmutable::parse($r['ends_on'],self::TZ)->endOfDay();
  $date=$from->startOfDay()->greaterThan($anchor)?$from->startOfDay():$anchor;
  $result=[];$horizon=$date->addDays(365);$limit=max(1,min(31,$limit));
  for(;$date->lte($end)&&$date->lte($horizon)&&count($result)<$limit;$date=$date->addDay()){
   if(((int)$anchor->diffInDays($date))%$r['interval_days']!==0||!in_array($date->dayOfWeekIso,$r['weekdays'],true)) continue;
   foreach($r['times'] as $time){
    $at=$date->setTimeFromTimeString($time);
    if($at->lt($from)||$at->gt($end)) continue;
    $result[]=$at->utc();if(count($result)>=$limit)break;
   }
  }
  return $result;
 }
}

<?php
namespace App\Filament\Widgets;
use App\Models\{Publication,Instance,GroupSchedule};
use App\Services\GroupOverview;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
class GruposStats extends StatsOverviewWidget {
 protected ?string $pollingInterval = null;
 protected function getStats(): array {
  $s=app(GroupOverview::class)->totals();
  return [
   Stat::make('Instâncias conectadas',Instance::where('connected',true)->where('enabled',true)->count()),
   Stat::make('Grupos presentes',$s['present'])->description('Última lista sincronizada'),
   Stat::make('Movimentados (7 dias)',$s['recent']),
   Stat::make('Participações conhecidas',$s['members'])->description($s['known'].' grupos atualizados; não são pessoas únicas'),
   Stat::make('Dados pendentes',$s['pending'])->description('Sem contagem atualizada nos últimos 7 dias'),
   Stat::make('Aptos para planejamento',$s['authorized']),
   Stat::make('Recorrências habilitadas',GroupSchedule::where('enabled',true)->count())->description('Configuração, não envio'),
   Stat::make('Rascunhos',Publication::where('status','draft')->count()),
  ];
 }
}

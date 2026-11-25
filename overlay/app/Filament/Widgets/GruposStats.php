<?php
namespace App\Filament\Widgets;
use App\Models\{WhatsappGroup,Publication,Instance};
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
class GruposStats extends StatsOverviewWidget {
 protected ?string $pollingInterval = null;
 protected function getStats(): array {return [
 Stat::make('Instâncias conectadas',Instance::where('connected',true)->count()),
 Stat::make('Grupos disponíveis',WhatsappGroup::where('present',true)->count()),
 Stat::make('Grupos autorizados',WhatsappGroup::where('present',true)->where('authorized',true)->where('enabled',true)->count()),
 Stat::make('Rascunhos',Publication::where('status','draft')->count()),
 ];}
}

<?php
namespace App\Filament\Resources;
use App\Models\GroupSchedule;
use App\Services\RecurrenceCalendar;
use Carbon\CarbonImmutable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{Select,DatePicker,TextInput,CheckboxList,TagsInput,Toggle};
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Actions\{EditAction};
use Illuminate\Validation\ValidationException;
class GroupScheduleResource extends Resource {
 protected static ?string $model=GroupSchedule::class;
 protected static ?string $navigationLabel='Recorrências';
 public static function form(Schema $schema): Schema {return $schema->components([
  Select::make('whatsapp_group_id')->label('Grupo')->relationship('group','name',fn($q)=>$q->where('present',true))->searchable()->required()->unique(ignoreRecord:true),
  Select::make('content_id')->label('Imagem ou vídeo com legenda')->relationship('content','title',fn($q)=>$q->where('enabled',true))->searchable()->required(),
  DatePicker::make('starts_on')->label('Início')->default(now('America/Sao_Paulo')->toDateString())->required(),
  DatePicker::make('ends_on')->label('Fim')->required()->afterOrEqual('starts_on'),
  TextInput::make('interval_days')->label('Intervalo em dias')->integer()->minValue(1)->maxValue(365)->default(1)->required()->helperText('1 = diariamente nos dias selecionados; 7 = a cada sete dias desde o início.'),
  CheckboxList::make('weekdays')->label('Dias da semana')->options([1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sáb',7=>'Dom'])->columns(4)->default([1,2,3,4,5,6,7])->required(),
  TagsInput::make('times')->label('Horários de Brasília (HH:mm)')->default(['09:00'])->required()->helperText('Digite o horário e pressione Enter. Limite diário definido no grupo.'),
  Toggle::make('enabled')->label('Habilitar agenda de revisão')->default(false)->helperText('Não ativa disparos. Apenas calcula as próximas ocorrências.')
 ]);}
 public static function table(Table $table): Table {return $table->modifyQueryUsing(fn($q)=>$q->with(['group.instance','content']))->columns([
  TextColumn::make('group.name')->label('Grupo')->searchable(),
  TextColumn::make('content.title')->label('Conteúdo'),
  TextColumn::make('times')->label('Horários')->formatStateUsing(fn($state)=>is_array($state)?implode(', ',$state):$state),
  TextColumn::make('ends_on')->label('Até')->date('d/m/Y'),
  IconColumn::make('enabled')->label('Habilitada')->boolean(),
  TextColumn::make('next_occurrence')->label('Próxima revisão')->state(function(GroupSchedule $record){
   if(!$record->enabled||!$record->group||!$record->content?->enabled||!$record->group->authorized||!$record->group->enabled||!$record->group->present||!$record->group->authorized_at||!$record->group->metadata_at||$record->group->metadata_at->lt(now()->subDays(7))||$record->group->announcement!==false||$record->group->is_admin===null||$record->group->admin_only===null||($record->group->admin_only&&!$record->group->is_admin)||!$record->group->instance?->enabled||!$record->group->instance?->connected||$record->group->instance?->sync_state!=='complete'||!$record->group->instance?->synced_at||$record->group->instance->synced_at->lt(now()->subDays(7))) return 'Pausada / revisar';
   try{$next=RecurrenceCalendar::upcoming($record->toArray(),CarbonImmutable::now(),$record->group->max_daily_publications,1);}catch(\Throwable){return 'Revisar regra';}
   $at=$next[0]??null;
   if(!$at||($record->content->expires_at&&$record->content->expires_at->lt($at))) return 'Sem ocorrência válida';
   return $at->setTimezone(RecurrenceCalendar::TZ)->format('d/m/Y H:i');
  })
  ])->recordActions([EditAction::make()])->defaultPaginationPageOption(25);}
 public static function validateRule(array $data): array {
  $g=\App\Models\WhatsappGroup::findOrFail($data['whatsapp_group_id']);$content=\App\Models\Content::findOrFail($data['content_id']);
  if(($data['enabled']??false)&&(!\App\Models\WhatsappGroup::authorizedForPlanning()->whereKey($g->id)->exists()||!$content->enabled||($content->expires_at&&$content->expires_at->isPast())))throw ValidationException::withMessages(['data.enabled'=>'Grupo ou conteúdo não está apto; mantenha a agenda pausada.']);
  try{$rule=RecurrenceCalendar::validate($data,$g->max_daily_publications);}catch(\InvalidArgumentException $e){throw ValidationException::withMessages(['data.times'=>$e->getMessage()]);}
  return array_merge(array_intersect_key($data,array_flip(['whatsapp_group_id','content_id','enabled'])),$rule);
 }
 public static function getPages(): array {return ['index'=>\App\Filament\Resources\GroupSchedulePages\ListRecords::route('/'),'create'=>\App\Filament\Resources\GroupSchedulePages\CreateRecord::route('/create'),'edit'=>\App\Filament\Resources\GroupSchedulePages\EditRecord::route('/{record}/edit')];}
}

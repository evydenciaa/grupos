<?php
namespace App\Filament\Resources;
use App\Models\WhatsappGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput,Textarea,Toggle};
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Tables\Filters\{Filter,TernaryFilter};
use Filament\Actions\{Action,EditAction};
use Filament\Notifications\Notification;
class WhatsappGroupResource extends Resource {
 protected static ?string $model=WhatsappGroup::class;
 protected static ?string $navigationLabel='Grupos';
 public static function form(Schema $schema): Schema {return $schema->components([
  TextInput::make('name')->label('Nome')->disabled(),TextInput::make('external_id')->label('ID Z-API')->disabled(),
  Textarea::make('description')->label('Descrição / regras')->disabled(),
  TextInput::make('invitation_link')->label('Link de convite')->url()->regex('~^https://chat\.whatsapp\.com/[A-Za-z0-9_-]+$~D'),
  Toggle::make('authorized')->label('Publicidade autorizada')->live(),
  Textarea::make('authorization_notes')->label('Evidência e condições da autorização')->required(fn($get)=>$get('authorized')),
  TextInput::make('max_daily_publications')->label('Máximo diário autorizado')->numeric()->integer()->minValue(1)->maxValue(24)->required(),
  Toggle::make('enabled')->label('Habilitar planejamento')
 ]);}
 public static function table(Table $table): Table {return $table->columns([
  TextColumn::make('name')->label('Grupo')->searchable()->sortable(),
  TextColumn::make('instance.name')->label('Instância')->toggleable(),
  TextColumn::make('member_count')->label('Membros')->placeholder('Não consultado')->sortable(),
  TextColumn::make('last_message_at')->label('Última atividade')->dateTime('d/m H:i')->timezone('America/Sao_Paulo')->sortable()->placeholder('Desconhecida'),
  TextColumn::make('metadata_at')->label('Dados atualizados')->dateTime('d/m H:i')->timezone('America/Sao_Paulo')->placeholder('Pendente'),
  IconColumn::make('present')->label('Presente')->boolean(),IconColumn::make('authorized')->label('Autorizado')->boolean(),IconColumn::make('enabled')->label('Habilitado')->boolean(),
  IconColumn::make('archived')->label('Arquivado')->boolean()->toggleable(isToggledHiddenByDefault:true),
  IconColumn::make('admin_only')->label('Só admins')->boolean()->toggleable(),
  TextColumn::make('max_daily_publications')->label('Limite/dia')->toggleable(),
  TextColumn::make('external_id')->label('ID')->searchable()->copyable()->toggleable(isToggledHiddenByDefault:true),
  TextColumn::make('invitation_link')->label('Convite')->limit(25)->copyable()->placeholder('Indisponível')->toggleable(),
  TextColumn::make('invitation_source')->label('Origem do convite')->placeholder('—')->toggleable(isToggledHiddenByDefault:true)
  ])->filters([
   TernaryFilter::make('present')->label('Presença')->default(true),
   Filter::make('recent')->label('Atividade nos últimos 7 dias')->query(fn($q)=>$q->recent()),
   Filter::make('ready')->label('Aptos para planejamento')->query(fn($q)=>$q->authorizedForPlanning()),
   Filter::make('stale')->label('Metadados pendentes/desatualizados')->query(fn($q)=>$q->where(fn($b)=>$b->whereNull('metadata_at')->orWhere('metadata_at','<',now()->subDays(7)))),
   TernaryFilter::make('archived')->label('Arquivado')
  ])->recordActions([
   Action::make('metadata')->label('Atualizar dados')->action(fn($record)=>static::run(fn()=>app(\App\Services\GroupSync::class)->refresh($record))),
   Action::make('invite')->label('Consultar convite')->action(fn($record)=>static::run(fn()=>app(\App\Services\GroupSync::class)->refresh($record,true))),
   EditAction::make()
  ])->defaultSort('member_count','desc')->defaultPaginationPageOption(25);}
 public static function getPages(): array {return ['index'=>\App\Filament\Resources\WhatsappGroupPages\ListRecords::route('/'),'edit'=>\App\Filament\Resources\WhatsappGroupPages\EditRecord::route('/{record}/edit')];}
 public static function run(callable $fn): void {try{$fn();Notification::make()->title('Dados atualizados')->success()->send();}catch(\Throwable){Notification::make()->title('Não foi possível atualizar')->body('Confira conexão, permissões e sincronização.')->danger()->send();}}
}

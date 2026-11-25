<?php
namespace App\Filament\Resources;
use App\Models\WhatsappGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput,Textarea,Toggle,FileUpload,DateTimePicker,Select};
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Actions\{Action,EditAction};
use Filament\Notifications\Notification;
class WhatsappGroupResource extends Resource {
 protected static ?string $model=WhatsappGroup::class;
 protected static ?string $navigationLabel='Grupos';
 public static function form(Schema $schema): Schema {return $schema->components([TextInput::make('name')->disabled(), TextInput::make('external_id')->disabled(), Textarea::make('description')->disabled(), TextInput::make('invitation_link')->url()->regex('~^https://chat\.whatsapp\.com/[A-Za-z0-9]+$~D'), Toggle::make('authorized')->label('Publicidade autorizada'), Textarea::make('authorization_notes')->required(fn($get)=>$get('authorized')), Toggle::make('enabled')]);}
 public static function table(Table $table): Table {return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('external_id')->searchable(), TextColumn::make('member_count'), IconColumn::make('present')->boolean(), IconColumn::make('authorized')->boolean(), IconColumn::make('enabled')->boolean(), TextColumn::make('last_message_at')->dateTime(), TextColumn::make('invitation_link')->limit(28)])->recordActions([Action::make('metadata')->label('Atualizar detalhes')->action(fn($record)=>static::run(fn()=>app(\App\Services\GroupSync::class)->refresh($record))), EditAction::make()])->defaultPaginationPageOption(25);}
 public static function getPages(): array {return ['index'=>\App\Filament\Resources\WhatsappGroupPages\ListRecords::route('/'),'edit'=>\App\Filament\Resources\WhatsappGroupPages\EditRecord::route('/{record}/edit')];}
 public static function run(callable $fn): void {try{$fn();Notification::make()->title('Operação concluída')->success()->send();}catch(\Throwable){Notification::make()->title('Operação não concluída')->body('Confira conexão e configuração.')->danger()->send();}}
}

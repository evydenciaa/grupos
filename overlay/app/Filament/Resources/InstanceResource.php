<?php
namespace App\Filament\Resources;
use App\Models\Instance;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput,Textarea,Toggle,FileUpload,DateTimePicker,Select};
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Actions\{Action,EditAction};
use Filament\Notifications\Notification;
class InstanceResource extends Resource {
 protected static ?string $model=Instance::class;
 protected static ?string $navigationLabel='Instâncias';
 public static function form(Schema $schema): Schema {return $schema->components([TextInput::make('name')->required(), TextInput::make('external_id')->required()->unique(ignoreRecord:true)->disabled(fn($record)=>$record!==null), TextInput::make('token')->password()->required(fn($record)=>!$record)->dehydrated(fn($state)=>filled($state))->formatStateUsing(fn()=>null), TextInput::make('client_token')->password()->required(fn($record)=>!$record)->dehydrated(fn($state)=>filled($state))->formatStateUsing(fn()=>null), Toggle::make('enabled')->default(true)]);}
 public static function table(Table $table): Table {return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('phone'), IconColumn::make('connected')->boolean(), TextColumn::make('synced_at')->dateTime()])->recordActions([Action::make('sync')->label('Sincronizar grupos')->requiresConfirmation()->action(fn($record)=>static::run(fn()=>app(\App\Services\GroupSync::class)->sync($record))), EditAction::make()])->defaultPaginationPageOption(25);}
 public static function getPages(): array {return ['index'=>\App\Filament\Resources\InstancePages\ListRecords::route('/'),'create'=>\App\Filament\Resources\InstancePages\CreateRecord::route('/create'),'edit'=>\App\Filament\Resources\InstancePages\EditRecord::route('/{record}/edit')];}
 public static function run(callable $fn): void {try{$fn();Notification::make()->title('Operação concluída')->success()->send();}catch(\Throwable){Notification::make()->title('Operação não concluída')->body('Confira conexão e configuração.')->danger()->send();}}
}

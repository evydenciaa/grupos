<?php
namespace App\Filament\Resources;
use App\Models\Publication;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput,Textarea,Toggle,FileUpload,DateTimePicker,Select};
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Actions\{Action,EditAction};
use Filament\Notifications\Notification;
class PublicationResource extends Resource {
 protected static ?string $model=Publication::class;
 protected static ?string $navigationLabel='Postagens';
 public static function form(Schema $schema): Schema {return $schema->components([Select::make('whatsapp_group_id')->relationship('group','name',fn($query)=>$query->where('authorized',true)->where('enabled',true)->where('present',true))->searchable()->required(), Select::make('content_id')->relationship('content','title',fn($query)=>$query->where('enabled',true))->searchable()->required(), DateTimePicker::make('scheduled_at')->timezone('America/Sao_Paulo')->required()]);}
 public static function table(Table $table): Table {return $table->columns([TextColumn::make('group.name')->searchable(), TextColumn::make('content.title'), TextColumn::make('scheduled_at')->dateTime()->timezone('America/Sao_Paulo')->sortable(), TextColumn::make('status')->badge(), TextColumn::make('error')])->recordActions([])->defaultPaginationPageOption(25);}
 public static function getPages(): array {return ['index'=>\App\Filament\Resources\PublicationPages\ListRecords::route('/'),'create'=>\App\Filament\Resources\PublicationPages\CreateRecord::route('/create')];}
 public static function run(callable $fn): void {try{$fn();Notification::make()->title('Operação concluída')->success()->send();}catch(\Throwable){Notification::make()->title('Operação não concluída')->body('Confira conexão e configuração.')->danger()->send();}}
}

<?php
namespace App\Filament\Resources;
use App\Models\Content;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput,Textarea,Toggle,FileUpload,DateTimePicker,Select};
use Filament\Tables\Columns\{TextColumn,IconColumn};
use Filament\Actions\{Action,EditAction};
use Filament\Notifications\Notification;
class ContentResource extends Resource {
 protected static ?string $model=Content::class;
 protected static ?string $navigationLabel='Conteúdos';
 public static function form(Schema $schema): Schema {return $schema->components([TextInput::make('title')->required(), FileUpload::make('path')->disk('local')->directory('campaign-media')->visibility('private')->preventFilePathTampering()->acceptedFileTypes(['image/jpeg','image/png','video/mp4'])->maxSize(16384)->required(), Textarea::make('caption')->required()->maxLength(1024), Toggle::make('enabled')->default(true), DateTimePicker::make('expires_at')->timezone('America/Sao_Paulo')]);}
 public static function table(Table $table): Table {return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('kind'), TextColumn::make('caption')->limit(50), IconColumn::make('enabled')->boolean()])->recordActions([EditAction::make()])->defaultPaginationPageOption(25);}
 public static function getPages(): array {return ['index'=>\App\Filament\Resources\ContentPages\ListRecords::route('/'),'create'=>\App\Filament\Resources\ContentPages\CreateRecord::route('/create'),'edit'=>\App\Filament\Resources\ContentPages\EditRecord::route('/{record}/edit')];}
 public static function run(callable $fn): void {try{$fn();Notification::make()->title('Operação concluída')->success()->send();}catch(\Throwable){Notification::make()->title('Operação não concluída')->body('Confira conexão e configuração.')->danger()->send();}}
}

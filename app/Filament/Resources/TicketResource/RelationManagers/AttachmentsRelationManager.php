<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Lampiran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->directory('ticket-attachments')
                    ->preserveFilenames()
                    ->maxSize(10240) // 10MB
                    ->acceptedFileTypes([
                        'image/*',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/plain',
                        'text/csv',
                    ]),
                Forms\Components\TextInput::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Deskripsi file...'),
                Forms\Components\Toggle::make('is_public')
                    ->label('Visible untuk Customer')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Preview')
                    ->square()
                    ->size(40)
                    ->visibility('private')
                    ->defaultImageUrl(fn ($record) => $record->isImage() ? null : '/images/file-icon.png'),
                Tables\Columns\TextColumn::make('original_name')
                    ->label('Nama File')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'image',
                        'info' => 'document',
                        'warning' => 'video',
                        'secondary' => 'audio',
                        'primary' => 'other',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'image' => 'Gambar',
                        'document' => 'Dokumen',
                        'video' => 'Video',
                        'audio' => 'Audio',
                        'other' => 'Lainnya',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('formatted_file_size')
                    ->label('Ukuran'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('Diupload oleh')
                    ->placeholder('System'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe File')
                    ->options([
                        'image' => 'Gambar',
                        'document' => 'Dokumen',
                        'video' => 'Video',
                        'audio' => 'Audio',
                        'other' => 'Lainnya',
                    ]),
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Visibility')
                    ->placeholder('Semua')
                    ->trueLabel('Publik')
                    ->falseLabel('Private'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload File')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by'] = auth()->id();
                        
                        // Extract file information
                        if (isset($data['file_path'])) {
                            $filePath = $data['file_path'];
                            $data['original_name'] = basename($filePath);
                            $data['file_name'] = basename($filePath);
                            
                            // Get file info
                            $fullPath = storage_path('app/public/' . $filePath);
                            if (file_exists($fullPath)) {
                                $data['file_size'] = filesize($fullPath);
                                $data['mime_type'] = mime_content_type($fullPath);
                                $data['file_hash'] = hash_file('md5', $fullPath);
                                $data['type'] = \App\Models\TicketAttachment::determineFileType($data['mime_type']);
                            }
                        }
                        
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->file_url)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

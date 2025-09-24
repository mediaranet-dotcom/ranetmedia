<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Komentar & Aktivitas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                    ->label('Komentar')
                    ->required()
                    ->rows(4),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'comment' => 'Komentar',
                                'status_change' => 'Perubahan Status',
                                'assignment_change' => 'Perubahan Penugasan',
                                'priority_change' => 'Perubahan Prioritas',
                                'resolution' => 'Penyelesaian',
                                'system_note' => 'Catatan Sistem',
                            ])
                            ->default('comment'),
                        Forms\Components\Toggle::make('is_internal')
                            ->label('Catatan Internal'),
                        Forms\Components\TextInput::make('time_spent_minutes')
                            ->label('Waktu (menit)')
                            ->numeric(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('author_display_name')
                    ->label('Penulis')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'comment',
                        'info' => 'status_change',
                        'warning' => 'assignment_change',
                        'danger' => 'priority_change',
                        'success' => 'resolution',
                        'secondary' => 'system_note',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'comment' => 'Komentar',
                        'status_change' => 'Status',
                        'assignment_change' => 'Penugasan',
                        'priority_change' => 'Prioritas',
                        'resolution' => 'Penyelesaian',
                        'system_note' => 'Sistem',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('content')
                    ->label('Isi')
                    ->limit(100)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 100 ? $state : null;
                    }),
                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Internal')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye-slash')
                    ->falseIcon('heroicon-o-eye')
                    ->trueColor('warning')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('formatted_time_spent')
                    ->label('Waktu')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'comment' => 'Komentar',
                        'status_change' => 'Perubahan Status',
                        'assignment_change' => 'Perubahan Penugasan',
                        'priority_change' => 'Perubahan Prioritas',
                        'resolution' => 'Penyelesaian',
                        'system_note' => 'Catatan Sistem',
                    ]),
                Tables\Filters\TernaryFilter::make('is_internal')
                    ->label('Internal')
                    ->placeholder('Semua')
                    ->trueLabel('Internal saja')
                    ->falseLabel('Publik saja'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Komentar')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        $data['author_type'] = 'staff';
                        $data['is_public'] = !($data['is_internal'] ?? false);
                        return $data;
                    }),
            ])
            ->actions([
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

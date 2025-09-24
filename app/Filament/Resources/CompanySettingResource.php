<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySettingResource\Pages;
use App\Models\CompanySetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanySettingResource extends Resource
{
    protected static ?string $model = CompanySetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Pengaturan Perusahaan';

    protected static ?string $modelLabel = 'Pengaturan Perusahaan';

    protected static ?string $pluralModelLabel = 'Pengaturan Perusahaan';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Perusahaan')
                    ->description('Data perusahaan yang akan tampil di invoice dan dokumen lainnya')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo Perusahaan')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml', 'image/gif'])
                            ->directory('logos')
                            ->disk('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '2:1',
                                '3:1',
                                '16:9',
                            ])
                            ->maxSize(2048)
                            ->helperText('Upload logo perusahaan. Format: JPG, JPEG, PNG, SVG, GIF (max 2MB). Ukuran disarankan: 400x200px atau 600x300px')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(255)
                            ->default('RANET Provider'),
                        Forms\Components\Textarea::make('company_address')
                            ->label('Alamat Perusahaan')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('company_phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Legal')
                    ->description('Nomor legal dan izin usaha')
                    ->schema([
                        Forms\Components\TextInput::make('tax_number')
                            ->label('NPWP')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('business_license')
                            ->label('NIB/SIUP')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('bank_details')
                            ->label('Detail Bank')
                            ->placeholder('Bank: BCA\nNo. Rekening: 1234567890\nAtas Nama: RANET Provider')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Invoice')
                    ->description('Kustomisasi tampilan invoice')
                    ->schema([
                        Forms\Components\Toggle::make('invoice_settings.show_logo')
                            ->label('Tampilkan Logo')
                            ->default(true),
                        Forms\Components\Toggle::make('invoice_settings.show_tax_number')
                            ->label('Tampilkan NPWP')
                            ->default(true),
                        Forms\Components\Toggle::make('invoice_settings.show_business_license')
                            ->label('Tampilkan NIB/SIUP')
                            ->default(true),
                        Forms\Components\Toggle::make('invoice_settings.show_bank_details')
                            ->label('Tampilkan Detail Bank')
                            ->default(true),
                        Forms\Components\Textarea::make('invoice_settings.footer_text')
                            ->label('Teks Footer Invoice')
                            ->placeholder('Terima kasih atas kepercayaan Anda menggunakan layanan RANET Provider')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Email')
                    ->description('Konfigurasi email yang dikirim sistem')
                    ->schema([
                        Forms\Components\TextInput::make('email_settings.from_name')
                            ->label('Nama Pengirim')
                            ->default('RANET Provider'),
                        Forms\Components\TextInput::make('email_settings.reply_to')
                            ->label('Reply To Email')
                            ->email()
                            ->default('noreply@ranet.com'),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Hanya satu pengaturan yang bisa aktif'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40)
                    ->width(80)
                    ->defaultImageUrl(asset('images/ranet-logo.svg')),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_phone')
                    ->label('Telepon'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (CompanySetting $record) {
                        // Deactivate all others
                        CompanySetting::query()->update(['is_active' => false]);
                        // Activate this one
                        $record->update(['is_active' => true]);
                    })
                    ->visible(fn(CompanySetting $record) => !$record->is_active)
                    ->requiresConfirmation(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCompanySettings::route('/'),
        ];
    }
}

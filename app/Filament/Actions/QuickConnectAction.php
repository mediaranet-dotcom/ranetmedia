<?php

namespace App\Filament\Actions;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Service;
use App\Models\Odp;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;

class QuickConnectAction
{
    public static function make(): Action
    {
        return Action::make('quickConnect')
            ->label('Quick Connect Customer')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->form([
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->options(Customer::where('status', 'active')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $customer = Customer::find($state);
                            $set('customer_info', $customer ? $customer->phone . ' - ' . $customer->address : '');
                        }
                    }),
                
                Forms\Components\Placeholder::make('customer_info')
                    ->label('Customer Info')
                    ->content(fn ($get) => $get('customer_info') ?? 'Select a customer to see details'),

                Forms\Components\Select::make('package_id')
                    ->label('Package')
                    ->options(Package::pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $package = Package::find($state);
                            $set('package_info', $package ? 'Rp ' . number_format($package->price, 0, ',', '.') . '/month - ' . $package->speed : '');
                        }
                    }),

                Forms\Components\Placeholder::make('package_info')
                    ->label('Package Info')
                    ->content(fn ($get) => $get('package_info') ?? 'Select a package to see details'),

                Forms\Components\Select::make('odp_id')
                    ->label('ODP')
                    ->options(Odp::where('status', 'active')->get()->mapWithKeys(function ($odp) {
                        $available = $odp->total_ports - $odp->used_ports;
                        return [$odp->id => $odp->name . " ({$available} ports available)"];
                    }))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $odp = Odp::find($state);
                            if ($odp) {
                                // Get used ports
                                $usedPorts = Service::where('odp_id', $state)
                                    ->whereNotNull('odp_port')
                                    ->pluck('odp_port')
                                    ->toArray();
                                
                                // Find next available port
                                $nextPort = null;
                                for ($i = 1; $i <= $odp->total_ports; $i++) {
                                    if (!in_array($i, $usedPorts)) {
                                        $nextPort = $i;
                                        break;
                                    }
                                }
                                
                                $set('odp_port', $nextPort);
                                $set('odp_info', $odp->area . ', ' . $odp->district . ' - ' . count($usedPorts) . '/' . $odp->total_ports . ' ports used');
                            }
                        }
                    }),

                Forms\Components\Placeholder::make('odp_info')
                    ->label('ODP Info')
                    ->content(fn ($get) => $get('odp_info') ?? 'Select an ODP to see details'),

                Forms\Components\TextInput::make('odp_port')
                    ->label('Port Number')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->helperText('Auto-suggested next available port'),

                Forms\Components\Select::make('fiber_cable_color')
                    ->label('Fiber Cable Color')
                    ->options([
                        'blue' => 'Blue',
                        'orange' => 'Orange',
                        'green' => 'Green',
                        'brown' => 'Brown',
                        'slate' => 'Slate',
                        'white' => 'White',
                        'red' => 'Red',
                        'black' => 'Black',
                        'yellow' => 'Yellow',
                        'violet' => 'Violet',
                        'rose' => 'Rose',
                        'aqua' => 'Aqua',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('signal_strength')
                    ->label('Signal Strength (dBm)')
                    ->numeric()
                    ->step(0.1)
                    ->placeholder('-20.5')
                    ->helperText('Typical range: -15 to -35 dBm'),

                Forms\Components\Textarea::make('installation_notes')
                    ->label('Installation Notes')
                    ->placeholder('Any special notes about the installation...')
                    ->columnSpanFull(),
            ])
            ->action(function (array $data) {
                try {
                    // Validate port availability
                    $existingService = Service::where('odp_id', $data['odp_id'])
                        ->where('odp_port', $data['odp_port'])
                        ->first();

                    if ($existingService) {
                        Notification::make()
                            ->title('Port Already Used')
                            ->body("Port {$data['odp_port']} is already occupied by another customer.")
                            ->danger()
                            ->send();
                        return;
                    }

                    // Check if customer already has active service
                    $existingCustomerService = Service::where('customer_id', $data['customer_id'])
                        ->where('status', 'active')
                        ->first();

                    if ($existingCustomerService) {
                        Notification::make()
                            ->title('Customer Already Connected')
                            ->body('This customer already has an active service.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Create the service
                    $service = Service::create([
                        'customer_id' => $data['customer_id'],
                        'package_id' => $data['package_id'],
                        'odp_id' => $data['odp_id'],
                        'odp_port' => $data['odp_port'],
                        'fiber_cable_color' => $data['fiber_cable_color'],
                        'signal_strength' => $data['signal_strength'] ?? null,
                        'installation_notes' => $data['installation_notes'] ?? null,
                        'start_date' => now(),
                        'status' => 'active',
                    ]);

                    // Update ODP port usage
                    $odp = Odp::find($data['odp_id']);
                    $odp->updatePortUsage();

                    $customer = Customer::find($data['customer_id']);
                    $package = Package::find($data['package_id']);

                    Notification::make()
                        ->title('Customer Connected Successfully!')
                        ->body("Connected {$customer->name} to {$odp->name} port {$data['odp_port']} with {$package->name} package.")
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Connection Failed')
                        ->body('An error occurred while connecting the customer: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}

<?php

namespace App\Filament\Resources\TicketsResource\Widgets;

use App\Models\Ticket;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTickets extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'gray',
                        'in_progress' => 'warning',
                        'closed' => 'success',
                    }),
                TextColumn::make('created_by')
                    ->getStateUsing(fn($record): string => $record->creator->name),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('assigned_to')
                    ->getStateUsing(fn($record): ?string => $record->assignedAgent?->name ?? 'Unassigned'),
            ])
            ->filters([
                SelectFilter::make('created_by')
                    ->label('Created By')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn() => User::query()
                        ->where('role', 'agent')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->query(function ($query, array $data) {
                        return $data['value']
                            ? $query->where('assigned_to', $data['value'])
                            : $query->whereNull('assigned_to');
                    })
            ]);
    }
}

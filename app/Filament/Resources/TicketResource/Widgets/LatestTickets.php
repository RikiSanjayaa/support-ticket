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
                    ->limit(10)
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
                TextColumn::make('assigned_to')
                    ->getStateUsing(fn($record): ?string => $record->assignedAgent?->name ?? 'Unassigned'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'closed' => 'Closed',
                    ])
                    ->preload(),

                SelectFilter::make('created_by')
                    ->label('Created By')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn() => [
                        '' => 'All Tickets',
                        'unassigned' => 'Unassigned',
                        ...User::query()
                            ->where('role', 'agent')
                            ->pluck('name', 'id')
                            ->toArray()
                    ])
                    ->searchable()
                    ->preload()
                    ->query(function ($query, array $data) {
                        if (!$data['value']) {
                            return $query; // Show all tickets
                        }

                        return $data['value'] === 'unassigned'
                            ? $query->whereNull('assigned_to')
                            : $query->where('assigned_to', $data['value']);
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

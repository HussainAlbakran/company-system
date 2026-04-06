<?php

namespace App\Services;

use App\Models\ProductionOrder;

class ProductionOrderCalculatorService
{
    public function recalculate(ProductionOrder $order): ProductionOrder
    {
        $producedQuantity = (float) $order->entries()->sum('quantity');
        $suppliedQuantity = (float) $order->supplies()->sum('quantity');

        $firstEntryDate = $order->entries()->orderBy('entry_date')->value('entry_date');
        $lastEntryDate = $order->entries()->orderByDesc('entry_date')->value('entry_date');

        $status = $order->status;

        if ($producedQuantity <= 0) {
            $status = 'pending';
        } elseif ($producedQuantity < (float) $order->planned_quantity) {
            $status = 'in_progress';
        } elseif ($producedQuantity >= (float) $order->planned_quantity) {
            $status = 'completed';
        }

        $order->update([
            'produced_quantity' => $producedQuantity,
            'supplied_quantity' => $suppliedQuantity,
            'production_start_date' => $order->production_start_date ?: $firstEntryDate,
            'actual_end_date' => $producedQuantity >= (float) $order->planned_quantity ? $lastEntryDate : null,
            'status' => $status,
        ]);

        return $order->fresh();
    }
}
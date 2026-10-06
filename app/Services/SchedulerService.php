<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;

class SchedulerService
{
    private const WORK_START = 9;
    private const WORK_END = 17;
    private const CHANGEOVER_MINUTES = 30;

    public function schedule()
    {
        $orders = Order::with([
            'items.product.productType',
            'customer',
        ])
        ->get();

        $orders = $orders
            ->sortBy('need_by_date')
            ->sortBy(function ($order) {
                return $order->items->first()->product->product_type_id;
            });

        $currentTime = Carbon::now()
            ->nextWeekday()
            ->setTime(self::WORK_START, 0);

        $currentType = null;

        $schedule = [];

        foreach ($orders as $order) {
            $productType = $order->items->first()->product->productType;

            $currentTime = $this->moveToWorkingTime($currentTime);

            if ($currentType !== null && $currentType->id !== $productType->id) {
                $currentTime = $this->addWorkingMinutes(
                    $currentTime,
                    self::CHANGEOVER_MINUTES
                );
            }

            $startTime = $currentTime->copy();

            $productionMinutes = 0;

            foreach ($order->items as $item) {
                $productionMinutes +=
                    ($item->quantity / $productType->production_speed) * 60;
            }

            $currentTime = $this->addWorkingMinutes(
                $currentTime,
                $productionMinutes
            );

            $schedule[] = [
                'order' => $order,
                'start' => $startTime,
                'end' => $currentTime->copy(),
                'production_minutes' => $productionMinutes,
                'product_type' => $productType,
            ];

            $currentType = $productType;
        }

        return $schedule;
    }

    private function moveToWorkingTime(Carbon $time): Carbon
    {
        if ($time->isWeekend()) {
            return $time->nextWeekday()->setTime(self::WORK_START, 0);
        }

        if ($time->hour >= self::WORK_END) {
            return $time->addDay()
                ->nextWeekday()
                ->setTime(self::WORK_START, 0);
        }

        if ($time->hour < self::WORK_START) {
            return $time->setTime(self::WORK_START, 0);
        }

        return $time;
    }

    private function addWorkingMinutes(Carbon $time, float $minutes): Carbon
    {
        $time = $this->moveToWorkingTime($time);

        while ($minutes > 0) {
            $endOfDay = $time->copy()->setTime(self::WORK_END, 0);

            $availableMinutes = $time->diffInMinutes($endOfDay);

            if ($minutes <= $availableMinutes) {
                return $time->addMinutes($minutes);
            }

            $minutes -= $availableMinutes;

            $time = $time->addDay()
                ->nextWeekday()
                ->setTime(self::WORK_START, 0);
        }

        return $time;
    }
}
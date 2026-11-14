<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use Tests\TestCase;

class CustomItineraryOvertimeTest extends TestCase
{
    public function test_config_has_custom_itinerary_defaults(): void
    {
        $this->assertSame(8, config('trihita.custom_base_hours'));
        $this->assertSame(75000, config('trihita.overtime_rate_per_hour'));
    }

    public function test_within_base_hours_has_no_overtime(): void
    {
        foreach ([0.0, 6.0, 8.0] as $hours) {
            $result = Booking::calculateOvertime($hours);

            $this->assertFalse($result['is_overtime'], "{$hours}h should not be overtime");
            $this->assertSame(0, $result['overtime_hours']);
            $this->assertSame(0, $result['overtime_charge']);
        }
    }

    public function test_overtime_is_rounded_up_and_charged_per_hour(): void
    {
        // 8.5h -> excess 0.5 -> ceil = 1h -> Rp 75.000
        $r1 = Booking::calculateOvertime(8.5);
        $this->assertTrue($r1['is_overtime']);
        $this->assertSame(1, $r1['overtime_hours']);
        $this->assertSame(75000, $r1['overtime_charge']);

        // 10.2h -> excess 2.2 -> ceil = 3h -> Rp 225.000
        $r2 = Booking::calculateOvertime(10.2);
        $this->assertSame(3, $r2['overtime_hours']);
        $this->assertSame(225000, $r2['overtime_charge']);

        // 11h -> excess 3 -> 3h -> Rp 225.000
        $r3 = Booking::calculateOvertime(11.0);
        $this->assertSame(3, $r3['overtime_hours']);
        $this->assertSame(225000, $r3['overtime_charge']);
    }

    public function test_overtime_respects_config_overrides(): void
    {
        config(['trihita.custom_base_hours' => 6, 'trihita.overtime_rate_per_hour' => 100000]);

        $result = Booking::calculateOvertime(7.5);
        $this->assertSame(2, $result['overtime_hours']);
        $this->assertSame(200000, $result['overtime_charge']);
    }

    public function test_package_regions_use_new_enum(): void
    {
        $this->assertSame(['utara', 'selatan', 'timur', 'barat'], Package::regionKeys());
        $this->assertNotContains('lainnya', Package::regionKeys());

        $package = new Package(['region' => 'selatan']);
        $this->assertSame('South Bali', $package->regionLabel());
    }
}

<?php


namespace Bpuig\Subby\Traits;


use Bpuig\Subby\Helpers\CarbonHelper;
use Bpuig\Subby\Services\Period;
use Illuminate\Support\Carbon;

trait HasTrialPeriodUsage
{
    use HasTrialPeriod;

    /**
     * Trial start date function
     * @return mixed
     */
    public function getTrialStartDate()
    {
        return $this->created_at;
    }

    /**
     * Trial period usage
     * @param string $interval
     * @return int
     * @throws \Exception
     */
    public function getTrialPeriodUsageIn(string $interval): int
    {
        $diff = $this->getTrialStartDate()->{CarbonHelper::diffIn($interval)}(Carbon::now());

        return ($diff > $this->getTrialTotalDurationIn($interval)) ? $this->getTrialTotalDurationIn($interval) : $diff;
    }

    /**
     * Remaining trial period usage
     * @param string $interval
     * @return int
     */
    public function getTrialPeriodRemainingUsageIn(string $interval): int
    {
        // Check if the current date is after the trial end date
        if (Carbon::now()->greaterThanOrEqualTo($this->trial_ends_at)) {
            // The trial period has ended, return 0
            return 0;
        } else {
            // Otherwise, return the remaining duration of the trial period
            return Carbon::now()->{CarbonHelper::diffIn($interval)}($this->trial_ends_at);
        }
    }

    /**
     * Check if entity has ended trial
     *
     * @return bool
     */
    public function hasEndedTrial(): bool
    {
        return !$this->trial_ends_at || \Carbon\Carbon::now()->gte($this->trial_ends_at);
    }
}

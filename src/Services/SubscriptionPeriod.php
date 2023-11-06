<?php

declare(strict_types=1);

namespace Bpuig\Subby\Services;

use Bpuig\Subby\Models\Plan;
use Bpuig\Subby\Models\PlanCombination;
use Carbon\Carbon;

/**
 * Class SubscriptionPeriod
 *
 * Intermediate class to calculate subscription periods accounting trial
 *
 * @package Bpuig\Subby\Services
 */
class SubscriptionPeriod
{
    protected $trialEnd = null;
    protected $start = null;
    protected $end = null;

    protected $plan;
    protected $startDate;
    protected $actualPlan;

    public function __construct(Plan|PlanCombination $plan, Carbon $startDate)
    {
        $this->plan = $plan;
        $this->startDate = $startDate;

        // Determine the actual plan to check for trial period
        $this->actualPlan = ($plan instanceof PlanCombination) ? $plan->plan : $plan;

        if ($this->actualPlan->trial_period > 0) {
            $this->setTrialPeriod();
        } else {
            $this->setSubscriptionPeriod();
        }
    }

    /**
     * Get start date.
     *
     * @return \Carbon\Carbon|null
     */
    public function getStartDate()
    {
        return $this->start;
    }

    /**
     * Get end date.
     *
     * @return \Carbon\Carbon
     */
    public function getEndDate()
    {
        return $this->end;
    }

    /**
     * Get trial end date.
     *
     * @return \Carbon\Carbon
     */
    public function getTrialEndDate()
    {
        return $this->trialEnd;
    }

    /**
     * Set trial period based on plan data
     */
    private function setTrialPeriod()
    {
        $trial = new Period($this->actualPlan->trial_interval, $this->actualPlan->trial_period, $this->startDate);
        $this->trialEnd = $trial->getEndDate();
    }

    /**
     * Set subscription period
     */
    private function setSubscriptionPeriod()
    {
        $period = new Period($this->plan->invoice_interval, $this->plan->invoice_period, $this->startDate);
        $this->start = $period->getStartDate();
        $this->end = $period->getEndDate();
    }
}

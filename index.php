<?php

class WaterBottleFilling
{
    private int $numberOfTaps;

    public function __construct(int $numberOfTaps)
    {
        $this->validateTaps($numberOfTaps);
        $this->numberOfTaps = $numberOfTaps;
    }

    public function timeRequired(array $queue): int
    {
        $this->validateQueue($queue);

        $totalTime = 0;
        $queueLength = count($queue);
        $litersPerTapPerSecond = $this->numberOfTaps * 0.1; // 100ml/sec per tap

        foreach ($queue as $bottleSize) {
            $timeInSeconds = ceil($bottleSize / $litersPerTapPerSecond);
            $totalTime += $timeInSeconds;
        }

        return $totalTime;
    }

    private function validateTaps(int $numberOfTaps): void
    {
        if ($numberOfTaps <= 0) {
            throw new InvalidArgumentException('Number of taps must be a positive integer.');
        }
    }

    private function validateQueue(array $queue): void
    {
        if (empty($queue)) {
            throw new InvalidArgumentException('Queue is empty.');
        }
    }
}

// Example usage
$waterBottleFilling = new WaterBottleFilling(3);
$queue = [400, 750, 1000];
$totalTime = $waterBottleFilling->timeRequired($queue);
echo "Total time required: {$totalTime} seconds";
?>

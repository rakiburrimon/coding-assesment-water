<?php

class WaterBottleFillingTimeCalculator
{
    // Function to calculate the total time required to fill all bottles
    public function calculateTotalTime(array $queue, int $taps, array $flowRates, int $walkingTime): int {
        // Check if a single flow rate is provided
        if (count($flowRates) === 1) {
            $flowRate = (int)$flowRates[0];
            // Calculate the total flow rate of all taps combined
            $totalFlowRate = $taps * $flowRate; // ml per second
        } else {
            // Calculate the total flow rate of all taps combined
            $totalFlowRate = array_sum($flowRates); // ml per second
        }

        // Calculate the time required for each person in the queue and sum them up
        $totalTime = 0;
        foreach ($queue as $bottleSize) {
            // Calculate the time to fill the bottle
            $fillTime = ceil($bottleSize / $totalFlowRate);

            // Add walking time if applicable
            $totalTime += $fillTime + $walkingTime;
        }
        return $totalTime;
    }

    // Function to validate inputs
    public function validateInputs(array $queue, int $taps, array $flowRates, int $walkingTime, int $queueNumber, array $newFlowRates): void {
        // Validate the queue
        if (empty($queue)) {
            throw new InvalidArgumentException('Queue is empty.');
        }
        foreach ($queue as $size) {
            if ($size <= 0) {
                throw new InvalidArgumentException('Bottle sizes must be positive integers.');
            }
        }

        // Validate the number of taps
        if ($taps <= 0) {
            throw new InvalidArgumentException('Number of taps must be a positive integer.');
        }

        // Validate the flow rates
        if (empty($flowRates)) {
            throw new InvalidArgumentException('Flow rates are missing.');
        }
        foreach ($flowRates as $rate) {
            if ($rate <= 0) {
                throw new InvalidArgumentException('Flow rates must be positive integers.');
            }
        }
        // Check if a single flow rate is provided
        if (count($flowRates) === 1 && empty($flowRates[0])) {
            throw new InvalidArgumentException('Flow rate must be required if a single value is provided.');
        } elseif (count($flowRates) != 1 && count($flowRates) != $taps) {
            throw new InvalidArgumentException('Number of flow rates must be 1 or equal to the number of taps.');
        }

        // Validate walking time
        if ($walkingTime < 0) {
            throw new InvalidArgumentException('Walking time must be a non-negative integer.');
        }

        // Validate queue number
        if ($queueNumber < 0 || $queueNumber > count($queue)) {
            throw new InvalidArgumentException('Queue number must be a non-negative integer less than or equal to the length of the queue.');
        }

        // Validate new flow rates
        foreach ($newFlowRates as $rate) {
            if ($rate <= 0) {
                throw new InvalidArgumentException('New flow rates must be positive integers.');
            }
        }
    }

    // Function to adjust flow rates after the specified queue number
    public function adjustFlowRates(array $queue, int $queueNumber, array $newFlowRates, array $flowRates): array {
        // If the queue number is greater than the queue length, do not adjust flow rates
        if ($queueNumber >= count($queue)) {
            return $flowRates;
        }

        // Adjust the flow rates after the specified queue number
        for ($i = $queueNumber; $i < count($flowRates); $i++) {
            $flowRates[$i] = $newFlowRates[$i - $queueNumber];
        }

        return $flowRates;
    }
}

// Instantiate the class
$calculator = new WaterBottleFillingTimeCalculator();

// Sample input data
$queue = [500, 750, 1000]; // Queue of bottle sizes
$taps = 3; // Number of taps
$flowRates = [100, 150, 200]; // Flow rates per tap (ml per second)
$walkingTime = 10; // Time to walk to tap (in seconds)
$queueNumber = 2; // Queue number for changing tap flow rate
$newFlowRates = [250, 300]; // New flow rates after queue number

// Calculate the total time required
$totalTime = $calculator->calculateTotalTime($queue, $taps, $flowRates, $walkingTime, $queueNumber, $newFlowRates);

// Display the result
echo $totalTime;

?>

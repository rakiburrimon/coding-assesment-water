<?php

class WaterBottleFillingTimeCalculator
{
    // Function to calculate the total time required to fill all bottles
    public function calculateTotalTime(array $queue, int $taps, array $flowRates, ?int $walkingTime = 0, ?int $queueNumber = null, ?array $newFlowRates = null): int
    {
        // Adjust flow rates if there's only one tap
        if ($taps === 1) {
            $flowRates = [$flowRates[0]];
        }

        // Calculate the total flow rate of all taps combined
        $totalFlowRate = array_sum($flowRates); // ml per second

        // Calculate the time required for each person in the queue and sum them up
        $totalTime = 0;
        $bottlesFilled = 0; // Counter to track the number of bottles filled
        foreach ($queue as $index => $bottleSize) {
            // Calculate the time to fill the bottle
            $fillTime = ceil($bottleSize / $totalFlowRate);

            // Add walking time if applicable (only for the first bottle of each group)
            if ($walkingTime > 0 && $bottlesFilled % $taps === 0) {
                $totalTime += $walkingTime;
            }

            // Add the time to fill the bottle
            $totalTime += $fillTime;

            // Increment the counter
            $bottlesFilled++;

            // Adjust flow rates after the specified queue number if provided
            if ($queueNumber !== null && $index + 1 === $queueNumber && $newFlowRates !== null) {
                $flowRates = array_slice($newFlowRates, 0, $taps);
                $totalFlowRate = array_sum($flowRates);
            }
        }

        return $totalTime;
    }
}

// Instantiate the class
$calculator = new WaterBottleFillingTimeCalculator();

// Sample input data
$queue = [500, 750, 1000, 500, 750, 1000, 500, 750, 1000]; // Queue of bottle sizes
$taps = 3; // Number of taps
$flowRates = [100, 150, 200]; // Flow rates per tap (ml per second)

// Optional parameters
$walkingTime = 10; // Time to walk to tap (in seconds)
$queueNumber = 2; // Queue number for changing tap flow rate
$newFlowRates = [250, 300]; // New flow rates after queue number

// Calculate the total time required
$totalTime = $calculator->calculateTotalTime($queue, $taps, $flowRates, $walkingTime ?? 0, $queueNumber ?? 0, $newFlowRates ?? []);

// Display the result
echo $totalTime;
?>
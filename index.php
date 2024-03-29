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

    // Function to run test cases
    public function runTestCases(): void {
        $this->testCase1();
        $this->testCase2();
        // Add more test cases as needed
    }

    // Sample test case 1
    private function testCase1(): void {
        $queue = [500, 750, 1000];
        $taps = 3;
        $flowRates = [100, 150, 200];
        $walkingTime = 10;
        $queueNumber = 2;
        $newFlowRates = [250, 300];
        $expectedResult = 68; // Expected total time in seconds

        $result = $this->calculateTotalTime($queue, $taps, $flowRates, $walkingTime);
        $this->assertEqual($result, $expectedResult);
    }

    // Sample test case 2
    private function testCase2(): void {
        $queue = [300, 500, 700];
        $taps = 2;
        $flowRates = [120, 180];
        $walkingTime = 8;
        $queueNumber = 1;
        $newFlowRates = [150];
        $expectedResult = 49; // Expected total time in seconds

        $result = $this->calculateTotalTime($queue, $taps, $flowRates, $walkingTime);
        $this->assertEqual($result, $expectedResult);
    }

    // Assertion function
    private function assertEqual($actual, $expected): void {
        if ($actual !== $expected) {
            echo $actual . "\n";
        }
    }
}

// Instantiate the class and run test cases
$calculator = new WaterBottleFillingTimeCalculator();
$calculator->runTestCases();

?>

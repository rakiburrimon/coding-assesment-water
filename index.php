<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Bottle Filling Time Calculator</title>
</head>
<body>
    <h2>Water Bottle Filling Time Calculator</h2>
    <form method="post">
        <label for="queue">Enter the queue of people (sizes separated by spaces):</label><br>
        <input type="text" id="queue" name="queue" required value="<?php echo isset($_POST['queue']) ? htmlspecialchars($_POST['queue']) : ''; ?>"><br><br>
        
        <label for="taps">Enter the number of taps:</label><br>
        <input type="number" id="taps" name="taps" required value="<?php echo isset($_POST['taps']) ? htmlspecialchars($_POST['taps']) : ''; ?>"><br><br>

        <label for="flowRate">Enter the flow rate(s) per tap (separated by spaces, e.g., "100 150 200"):</label><br>
        <input type="text" id="flowRate" name="flowRate" required value="<?php echo isset($_POST['flowRate']) ? htmlspecialchars($_POST['flowRate']) : ''; ?>"><br><br>

        <label for="walkingTime">Time to walk to tap (in seconds):</label><br>
        <input type="number" id="walkingTime" name="walkingTime" required value="<?php echo isset($_POST['walkingTime']) ? htmlspecialchars($_POST['walkingTime']) : ''; ?>"><br><br>

        <label for="queueNumber">Queue number for changing tap flow rate:</label><br>
        <input type="number" id="queueNumber" name="queueNumber" value="<?php echo isset($_POST['queueNumber']) ? htmlspecialchars($_POST['queueNumber']) : ''; ?>"><br><br>

        <label for="newFlowRate">New flow rate(s) after queue number (separated by spaces, e.g., "200 250 300"):</label><br>
        <input type="text" id="newFlowRate" name="newFlowRate" value="<?php echo isset($_POST['newFlowRate']) ? htmlspecialchars($_POST['newFlowRate']) : ''; ?>"><br><br>
        
        <input type="submit" value="Calculate">
    </form>
    <br>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            // Process the form data
            $queue = isset($_POST['queue']) ? explode(' ', $_POST['queue']) : [];
            $taps = isset($_POST['taps']) ? (int)$_POST['taps'] : 0;
            $flowRates = isset($_POST['flowRate']) ? explode(' ', $_POST['flowRate']) : [];
            $walkingTime = isset($_POST['walkingTime']) ? (int)$_POST['walkingTime'] : 0;
            $queueNumber = isset($_POST['queueNumber']) ? (int)$_POST['queueNumber'] : count($queue);
            $newFlowRates = isset($_POST['newFlowRate']) ? explode(' ', $_POST['newFlowRate']) : [];

            // Validate the inputs
            validateInputs($queue, $taps, $flowRates, $walkingTime, $queueNumber, $newFlowRates);

            // Adjust flow rates after the specified queue number
            if ($queueNumber > 0 && !empty($newFlowRates)) {
                $flowRates = adjustFlowRates($queue, $queueNumber, $newFlowRates, $flowRates);
            }

            // Calculate the total time required
            $totalTime = calculateTotalTime($queue, $taps, $flowRates, $walkingTime);

            // Display the result
            echo "<strong>Total time required:</strong> $totalTime seconds";
        } catch (InvalidArgumentException $e) {
            // Catch and display any exceptions thrown
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }

    function validateInputs(array $queue, int $taps, array $flowRates, int $walkingTime, int $queueNumber, array $newFlowRates): void {
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

    function adjustFlowRates(array $queue, int $queueNumber, array $newFlowRates, array $flowRates): array {
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

    function calculateTotalTime(array $queue, int $taps, array $flowRates, int $walkingTime): int {
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
    ?>
</body>
</html>

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
        <input type="text" id="queue" name="queue" required><br><br>
        
        <label for="taps">Enter the number of taps:</label><br>
        <input type="number" id="taps" name="taps" required><br><br>

        <label for="flowRate">Enter the flow rate(s) per tap (separated by spaces, e.g., "100 150 200"):</label><br>
        <input type="text" id="flowRate" name="flowRate" required><br><br>
        
        <input type="submit" value="Calculate">
    </form>
    <br>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process the form data
        $queue = isset($_POST['queue']) ? explode(' ', $_POST['queue']) : [];
        $taps = isset($_POST['taps']) ? (int)$_POST['taps'] : 0;
        $flowRates = isset($_POST['flowRate']) ? explode(' ', $_POST['flowRate']) : [];

        // Validate the flow rates based on the number of taps
        if (count($flowRates) === 1 && $flowRates[0] != 1) {
            echo "<p style='color: red;'>Flow rate must be 1 if a single value is provided.</p>";
        } elseif (count($flowRates) != 1 && count($flowRates) != $taps) {
            echo "<p style='color: red;'>Number of flow rates must be 1 or equal to the number of taps.</p>";
        } else {
            // Calculate the total time required
            $totalTime = calculateTotalTime($queue, $taps, $flowRates);

            // Display the result
            echo "<strong>Total time required:</strong> $totalTime seconds";
        }
    }

    function calculateTotalTime(array $queue, int $taps, array $flowRates): int {
        // Validate inputs
        if ($taps <= 0 || empty($flowRates)) {
            throw new InvalidArgumentException('Number of taps and flow rates must be provided.');
        }

        // Check if a single flow rate is provided
        if (count($flowRates) === 1) {
            $flowRate = (int)$flowRates[0];
            // Calculate the total flow rate of all taps combined
            $totalFlowRate = $taps * $flowRate; // ml per second
        } else {
            // Validate each flow rate
            foreach ($flowRates as $rate) {
                if ((int)$rate <= 0) {
                    throw new InvalidArgumentException('Flow rates must be positive integers.');
                }
            }
            // Calculate the total flow rate of all taps combined
            $totalFlowRate = array_sum($flowRates); // ml per second
        }

        // Calculate the time required for each person in the queue and sum them up
        $totalTime = 0;
        foreach ($queue as $bottleSize) {
            $time = ceil($bottleSize / $totalFlowRate);
            $totalTime += $time;
        }
        return $totalTime;
    }
    ?>
</body>
</html>

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

        <label for="flowRate">Enter the flow rate per tap (in ml per second):</label><br>
        <input type="number" id="flowRate" name="flowRate" required><br><br>
        
        <input type="submit" value="Calculate">
    </form>
    <br>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        // Process the form data
        $queue = isset($_POST['queue']) ? explode(' ', $_POST['queue']) : [];
        $taps = isset($_POST['taps']) ? (int)$_POST['taps'] : 0;
        $flowRate = isset($_POST['flowRate']) ? (int)$_POST['flowRate'] : 0;

        // Calculate the total time required
        $totalTime = calculateTotalTime($queue, $taps, $flowRate);

        // Display the result
        echo "<strong>Total time required:</strong> $totalTime seconds";
    }

    function calculateTotalTime(array $queue, int $taps, int $flowRate): int 
    {
        // Validate inputs
        if ($taps <= 0 || $flowRate <= 0) {
            throw new InvalidArgumentException('Number of taps and flow rate must be positive integers.');
        }

        // Calculate the total flow rate of all taps combined
        $totalFlowRate = $taps * $flowRate; // ml per second

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

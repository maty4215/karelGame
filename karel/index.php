<?php
session_start();

if (!isset($_SESSION['field'])) {
    resetField();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commands = strtoupper($_POST['commands']);
    processCommands($commands);
}

function resetField() {
    $_SESSION['field'] = array_fill(0, 10, array_fill(0, 10, ['karel' => false, 'color' => '']));
    $_SESSION['karel'] = ['x' => 0, 'y' => 0, 'direction' => 'E'];
    $_SESSION['field'][0][0]['karel'] = true;
}

function processCommands($commands) {
    $lines = explode("\n", $commands);
    foreach ($lines as $line) {
        $parts = explode(' ', trim($line));
        $command = $parts[0];
        $argument = isset($parts[1]) ? $parts[1] : null;
        executeCommand($command, $argument);
    }
}

function executeCommand($command, $argument) {
    switch ($command) {
        case 'KROK':
            krok($argument);
            break;
        case 'VLEVOBOK':
            levo($argument);
            break;
        case 'POLOZ':
            poloz($argument);
            break;
        case 'RESET':
            resetField();
            break;
    }
}

function krok($steps) {
    $steps = $steps ?: 1;
    $direction = $_SESSION['karel']['direction'];
    $x = &$_SESSION['karel']['x'];
    $y = &$_SESSION['karel']['y'];
    
    $_SESSION['field'][$y][$x]['karel'] = false; // Clear current position

    for ($i = 0; $i < $steps; $i++) {
        switch ($direction) {
            case 'E': $x = min(9, $x + 1); break;
            case 'W': $x = max(0, $x - 1); break;
            case 'N': $y = max(0, $y - 1); break;
            case 'S': $y = min(9, $y + 1); break;
        }
    }
    $_SESSION['field'][$y][$x]['karel'] = true; // Set new position
}

function levo($times) {
    $times = $times ?: 1;
    $directions = ['E' => 'N', 'N' => 'W', 'W' => 'S', 'S' => 'E'];
    for ($i = 0; $i < $times; $i++) {
        $_SESSION['karel']['direction'] = $directions[$_SESSION['karel']['direction']];
    }
}

function poloz($color) {
    $x = $_SESSION['karel']['x'];
    $y = $_SESSION['karel']['y'];
    $_SESSION['field'][$y][$x]['color'] = strtolower($color);
}

function displayField() {
    foreach ($_SESSION['field'] as $row) {
        foreach ($row as $cell) {
            $color = isset($cell['color']) ? htmlspecialchars($cell['color']) : '';
            echo '<div class="cell" style="background-color:' . $color . ';">';
            if ($cell['karel']) {
                echo '<span class="karel direction-' . strtolower($_SESSION['karel']['direction']) . '">K</span>';
            }
            echo '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karel Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Karel Game</h1>
        <form method="POST" action="index.php">
            <textarea name="commands" placeholder="Enter commands here..."></textarea>
            <button type="submit">Submit</button>
        </form>
        <div id="game-field">
            <?php displayField(); ?>
        </div>
    </div>
</body>
</html>
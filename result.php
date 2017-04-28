<?php
require_once __DIR__ . '/vendor/autoload.php';
use Google\Cloud\Speech\SpeechClient;
use Google\Cloud\Storage\StorageClient;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Google Speech Transcript Result</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <table border="1">
            <?php
            $no = 1;
            $results = unserialize(file_get_contents('transcript'));
            foreach($results as $transcript) {
                foreach($transcript->alternatives() as $trans) {
                    echo "<tr>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>" . $trans['transcript'] . "</td>";
                    echo "</tr>";
                    $no++;
                }
            }
            ?>
        </table>
    </body>
</html>
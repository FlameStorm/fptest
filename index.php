<html>
<head>
<title>Testimg SMS messages parser</title>
<style>
pre .msg {font-weight: bold; color: #0090F0;}
pre .error {font-weight: bold; color: #F02000;}
</style>
</head>
<body>
<pre>Testimg SMS messages parser:
<?php

$messages = include('testcases.php');

require_once "parseMessage.php";

$totalCount = count($messages);
$passedCount = 0;
foreach ($messages as $message) {
    try {
        echo "<hr><span class='msg'>" . htmlspecialchars($message) . "</span>\n";
        $result = parseMessage($message);
        var_dump($result);
		$passedCount++;
    } catch (Exception $e) {
        echo "<span class='error'>SMS message parsing failed: " . $e->getMessage() . "</span>\n";
    }
}
echo "\n\n<b>TOTAL: passed {$passedCount} of {$totalCount}</b>";

?>
</pre>
</body>
</html>
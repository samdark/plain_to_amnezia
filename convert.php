<?php

if (!isset($argv[1], $argv[2])) {
    echo "convert.php source.lst destination.json\n";
    exit(1);
}

$sourcePath = $argv[1];
$destinationPath = $argv[2];

$source = fopen($sourcePath, 'rb');
if (!$source) {
    echo "Error opening file $sourcePath.\n";
    exit(1);
}

$destination = fopen($destinationPath, 'wb');
if (!$destination) {
    echo "Error opening file $destinationPath.\n";
    exit(1);
}

fwrite($destination, "[\n");
$isFirst = true;
while (($line = fgets($source)) !== false) {
    $line = trim($line);

    putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1');
    $ip = gethostbyname($line . '.');

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        continue;
    }

    if (!$isFirst) {
        fwrite($destination, ",\n");
    }
    $isFirst = false;

    $block = <<<BLOCK
    {
        "hostname": "$line",
        "ip": "$ip"
    }
BLOCK;

    fwrite($destination, $block);
}
fwrite($destination, "\n]");

fclose($source);
fclose($destination);

exit(0);

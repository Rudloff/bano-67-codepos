<?php
require_once 'config.php';
$pdo = new PDO(
    'pgsql:host=localhost;port=5432;dbname='.DBNAME.';user='.USER.';password='.PASS
);

$query = $pdo->prepare(
    "SELECT * FROM codes_postaux_region
    WHERE ST_Contains(geom, ST_GeomFromText(:point, 4326));"
);

$csvfile = file_get_contents('bano-67.csv');
$csvlines = explode(PHP_EOL, $csvfile);

$fp = fopen('bano-67-codes.csv', 'w');

foreach ($csvlines as $line) {
    $address = str_getcsv($line);
    if ($address[4] == 'Strasbourg') {
        $query->bindValue(':point', 'POINT('.$address[7].' '.$address[6].')');
        $query->execute();
        $codepos = $query->fetch(PDO::FETCH_ASSOC);
        $address[3] = $codepos['id'];
        echo $address[1].' '.$address[2].', '.$address[4].' => '.$codepos['id'], PHP_EOL;
        fputcsv($fp, $address);
    }
}
?>

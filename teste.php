<?php
$ip = '100.64.26.250';  // IP do roteador
$ports = [80, 8080, 8888];

function testPort($ip, $port) {
    $errno = 0;
    $errstr = '';
    $timeout = 5; // Timeout de 5 segundos
    $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);

    if (!$socket) {
        echo "Erro ao tentar conectar na porta $port: $errstr ($errno)\n";
    } else {
        echo "Porta $port no IP $ip está acessível.\n";
        fclose($socket);
    }
}

foreach ($ports as $port) {
    testPort($ip, $port);
}
?>
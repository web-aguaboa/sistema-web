<?php
echo "Testando ambiente para Excel..." . PHP_EOL;

// Testar se Python está disponível
exec('python --version 2>&1', $output, $return);
if ($return === 0) {
    echo 'Python disponível: ' . implode(' ', $output) . PHP_EOL;
} else {
    echo 'Python NÃO encontrado' . PHP_EOL;
}

// Testar PowerShell
exec('powershell -Command "Write-Host PowerShell-OK"', $psOutput, $psReturn);
if ($psReturn === 0) {
    echo 'PowerShell disponível' . PHP_EOL;
} else {
    echo 'PowerShell indisponível' . PHP_EOL;
}

// Testar LibreOffice
exec('libreoffice --version 2>&1', $loOutput, $loReturn);
if ($loReturn === 0) {
    echo 'LibreOffice: ' . implode(' ', $loOutput) . PHP_EOL;
} else {
    echo 'LibreOffice NÃO encontrado' . PHP_EOL;
}

echo 'Fim dos testes.' . PHP_EOL;
?>
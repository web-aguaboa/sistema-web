<?php
// Teste direto do método Python/pandas

require_once 'config/config.php';
require_once 'src/models/Database.php';
require_once 'src/models/EnvaseModel.php';
require_once 'src/models/ClientModel.php';
require_once 'src/models/UploadModel.php';
require_once 'src/models/ActivityLog.php';
require_once 'src/controllers/EnvaseController.php';

echo "=== TESTE DIRETO PYTHON/PANDAS ===" . PHP_EOL;

// Simular sessão
$_SESSION['user_id'] = 1;

// Instanciar controller
$database = new Database();
$envaseModel = new EnvaseModel($database);
$clientModel = new ClientModel($database);
$uploadModel = new UploadModel($database);
$activityLog = new ActivityLog($database);

$controller = new EnvaseController($envaseModel, $clientModel, $uploadModel, $activityLog);

// Usar reflection para acessar método privado
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('lerComPythonPandas');
$method->setAccessible(true);

// Testar com arquivo de exemplo
$arquivoTeste = 'public/uploads/RelatorioOLAP.xls';

if (file_exists($arquivoTeste)) {
    echo "Testando arquivo: $arquivoTeste" . PHP_EOL;
    
    $inicio = microtime(true);
    $dados = $method->invoke($controller, $arquivoTeste);
    $fim = microtime(true);
    
    echo "Tempo de execução: " . round($fim - $inicio, 2) . " segundos" . PHP_EOL;
    echo "Registros encontrados: " . count($dados) . PHP_EOL;
    
    if (!empty($dados)) {
        echo "Primeiros 3 registros:" . PHP_EOL;
        for ($i = 0; $i < min(3, count($dados)); $i++) {
            print_r($dados[$i]);
        }
    }
} else {
    echo "Arquivo não encontrado: $arquivoTeste" . PHP_EOL;
    echo "Arquivos disponíveis em public/uploads/:" . PHP_EOL;
    $files = glob('public/uploads/*');
    foreach ($files as $file) {
        echo "- " . basename($file) . PHP_EOL;
    }
}

echo "=== FIM DO TESTE ===" . PHP_EOL;
?>
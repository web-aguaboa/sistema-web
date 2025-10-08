<?php
// Teste simples da página de envase cliente

require_once 'config/config.php';
require_once 'src/models/Database.php';
require_once 'src/models/Client.php';
require_once 'src/models/Envase.php';

echo "=== TESTE PÁGINA ENVASE CLIENTE ===" . PHP_EOL;

try {
    // Testar conexão
    $database = new Database();
    echo "✅ Conexão com banco OK" . PHP_EOL;
    
    // Testar modelo Client
    $clientModel = new Client();
    $client = $clientModel->findById(4);
    
    if ($client) {
        echo "✅ Cliente encontrado: " . $client['cliente'] . PHP_EOL;
        echo "   Empresa: " . $client['empresa'] . PHP_EOL;
    } else {
        echo "❌ Cliente ID 4 não encontrado" . PHP_EOL;
        
        // Listar clientes disponíveis
        $db = $database->getConnection();
        $stmt = $db->query("SELECT id, cliente, empresa FROM clients LIMIT 5");
        $clientes = $stmt->fetchAll();
        
        echo "Clientes disponíveis:" . PHP_EOL;
        foreach ($clientes as $c) {
            echo "  ID {$c['id']}: {$c['cliente']} ({$c['empresa']})" . PHP_EOL;
        }
    }
    
    // Testar modelo Envase
    $envaseModel = new Envase();
    if ($client && $client['empresa']) {
        $envaseData = $envaseModel->findByEmpresa($client['empresa']);
        echo "✅ Dados de envase encontrados: " . count($envaseData) . " registros" . PHP_EOL;
        
        if (!empty($envaseData)) {
            echo "   Primeiro registro: " . $envaseData[0]['produto'] . " - " . $envaseData[0]['quantidade'] . PHP_EOL;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . PHP_EOL;
}

echo "=== FIM DO TESTE ===" . PHP_EOL;
?>
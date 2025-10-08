<?php
// Verificar configuração de uploads e arquivos de ações
require_once 'config/init.php';

echo "🔍 Verificando Configuração de Uploads\n\n";

// Verificar constantes
echo "📁 UPLOAD_DIR: " . UPLOAD_DIR . "\n";
echo "📁 Diretório absoluto: " . realpath(UPLOAD_DIR) . "\n\n";

// Verificar diretórios
$actionsDir = UPLOAD_DIR . 'actions/';
echo "📂 Diretório de ações: $actionsDir\n";
echo "📂 Existe? " . (is_dir($actionsDir) ? "SIM" : "NÃO") . "\n";

if (!is_dir($actionsDir)) {
    mkdir($actionsDir, 0755, true);
    echo "✅ Diretório criado!\n";
}

// Listar arquivos
echo "\n📋 Arquivos no diretório de ações:\n";
$files = glob($actionsDir . '*');
if (empty($files)) {
    echo "  (vazio)\n";
} else {
    foreach ($files as $file) {
        echo "  - " . basename($file) . " (" . filesize($file) . " bytes)\n";
    }
}

// Buscar ações com arquivos no banco
echo "\n🗄️ Ações com arquivos no banco:\n";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, descricao, arquivo FROM actions WHERE arquivo IS NOT NULL AND arquivo != ''");
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($actions)) {
        echo "  (nenhuma ação com arquivo)\n";
    } else {
        foreach ($actions as $action) {
            $filePath = $actionsDir . $action['arquivo'];
            $exists = file_exists($filePath) ? "✅" : "❌";
            echo "  - ID {$action['id']}: {$action['arquivo']} $exists\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

// Criar arquivo de teste
echo "\n🧪 Criando arquivo de teste...\n";
$testFile = $actionsDir . 'teste_' . time() . '.txt';
file_put_contents($testFile, 'Arquivo de teste criado em ' . date('Y-m-d H:i:s'));
echo "✅ Arquivo criado: " . basename($testFile) . "\n";

echo "\n🌐 URLs para testar:\n";
echo "  - Arquivo de teste: http://localhost:8080/uploads/actions/" . basename($testFile) . "\n";

if (!empty($actions)) {
    foreach ($actions as $action) {
        echo "  - Arquivo da ação {$action['id']}: http://localhost:8080/uploads/actions/{$action['arquivo']}\n";
    }
}
?>
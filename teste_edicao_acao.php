<?php
// Teste específico para edição de ação
require_once 'config/init.php';

// Simular sessão
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "🔧 Teste Edição de Ação\n\n";

try {
    // 1. Verificar se a ação existe
    $actionModel = new Action();
    $action = $actionModel->findById(1); // Testando ação ID 1
    
    if ($action) {
        echo "✅ Ação encontrada:\n";
        echo "  ID: {$action['id']}\n";
        echo "  Descrição: {$action['descricao']}\n";
        echo "  Data: {$action['data_acao']}\n";
        echo "  Arquivo: " . ($action['arquivo'] ?: 'Nenhum') . "\n\n";
        
        // 2. Testar endpoint GET
        echo "🔍 Testando endpoint GET /action/1...\n";
        $controller = new ActionsController();
        
        ob_start();
        $controller->get(1);
        $response = ob_get_clean();
        
        echo "📄 Resposta: $response\n";
        
        $json = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✅ JSON válido!\n\n";
        } else {
            echo "❌ JSON inválido: " . json_last_error_msg() . "\n\n";
        }
        
        // 3. Testar atualização
        echo "✏️ Testando atualização...\n";
        $_POST['descricao'] = 'Descrição atualizada via teste - ' . date('Y-m-d H:i:s');
        $_POST['data_acao'] = date('Y-m-d');
        
        ob_start();
        $controller->update(1);
        $updateResponse = ob_get_clean();
        
        echo "📄 Resposta update: $updateResponse\n";
        
        $updateJson = json_decode($updateResponse, true);
        if (json_last_error() === JSON_ERROR_NONE && $updateJson['success']) {
            echo "✅ Atualização bem-sucedida!\n";
        } else {
            echo "❌ Erro na atualização\n";
        }
        
    } else {
        echo "❌ Ação ID 1 não encontrada\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
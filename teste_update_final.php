<?php
// Teste FINAL - Simulação completa de edição de ação
require_once 'config/init.php';

// Simular sessão de usuário
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

// Simular dados POST para atualização
$_POST['descricao'] = 'Descrição atualizada pelo teste - ' . date('Y-m-d H:i:s');
$_POST['data_acao'] = date('Y-m-d');

echo "🔧 Teste Final - Atualização de Ação\n\n";

try {
    $controller = new ActionsController();
    
    echo "✅ Controller criado\n";
    echo "📝 Dados para atualizar:\n";
    echo "  - Descrição: {$_POST['descricao']}\n";
    echo "  - Data: {$_POST['data_acao']}\n\n";
    
    echo "🔄 Executando atualização da ação ID 3...\n";
    
    // Capturar a saída
    ob_start();
    $controller->update(3);
    $output = ob_get_clean();
    
    echo "📄 Resposta do controller:\n";
    echo $output . "\n";
    
    // Verificar se é JSON válido
    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "\n✅ JSON válido!\n";
        if ($json['success']) {
            echo "🎉 Atualização realizada com sucesso!\n";
        } else {
            echo "❌ Erro na atualização: " . $json['message'] . "\n";
        }
    } else {
        echo "\n❌ JSON inválido! Erro: " . json_last_error_msg() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n🎯 Teste concluído!\n";
?>
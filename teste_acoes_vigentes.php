<?php
/**
 * Teste da página de ações vigentes
 */

require_once 'config/init.php';

// Simular sessão de admin
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['username'] = 'admin';

try {
    // Instanciar o controller
    $crmController = new CrmController();
    
    echo "Testando acesso à página de Ações Vigentes...\n\n";
    
    // Chamar o método diretamente
    $crmController->acoesVigentes();
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
<?php
require_once 'config/init.php';

echo "<h2>🔍 Verificação e Limpeza Forçada do CRM</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    
    echo "<h3>1. Verificando estado atual das tabelas</h3>";
    
    // Verificar clientes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clients");
    $total_clientes = $stmt->fetch()['total'];
    echo "📊 Clientes na tabela: {$total_clientes}<br>";
    
    if ($total_clientes > 0) {
        // Mostrar alguns clientes que ainda existem
        $stmt = $pdo->query("SELECT id, cliente, empresa, cidade FROM clients LIMIT 10");
        $clientes_existentes = $stmt->fetchAll();
        
        echo "<h4>Clientes que ainda existem:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Cliente</th><th>Empresa</th><th>Cidade</th></tr>";
        foreach ($clientes_existentes as $cliente) {
            echo "<tr>";
            echo "<td>{$cliente['id']}</td>";
            echo "<td>" . htmlspecialchars($cliente['cliente']) . "</td>";
            echo "<td>" . htmlspecialchars($cliente['empresa']) . "</td>";
            echo "<td>" . htmlspecialchars($cliente['cidade']) . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // Verificar ações
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM actions");
    $total_acoes = $stmt->fetch()['total'];
    echo "📊 Ações na tabela: {$total_acoes}<br>";
    
    // Verificar informações
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM client_infos");
    $total_infos = $stmt->fetch()['total'];
    echo "📊 Informações na tabela: {$total_infos}<br><br>";
    
    if ($total_clientes > 0 || $total_acoes > 0 || $total_infos > 0) {
        echo "<h3>2. Forçando limpeza completa</h3>";
        
        // Desabilitar verificações de chave estrangeira temporariamente
        $pdo->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // Limpar todas as tabelas relacionadas
        $pdo->query("DELETE FROM actions");
        echo "✅ Tabela actions limpa<br>";
        
        $pdo->query("DELETE FROM client_infos");
        echo "✅ Tabela client_infos limpa<br>";
        
        $pdo->query("DELETE FROM clients");
        echo "✅ Tabela clients limpa<br>";
        
        // Resetar auto_increment
        $pdo->query("ALTER TABLE actions AUTO_INCREMENT = 1");
        $pdo->query("ALTER TABLE client_infos AUTO_INCREMENT = 1");
        $pdo->query("ALTER TABLE clients AUTO_INCREMENT = 1");
        echo "✅ Contadores resetados<br>";
        
        // Reabilitar verificações de chave estrangeira
        $pdo->query("SET FOREIGN_KEY_CHECKS = 1");
        echo "✅ Chaves estrangeiras reabilitadas<br><br>";
        
        echo "<h3>3. Verificação final</h3>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM clients");
        $clientes_final = $stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM actions");
        $acoes_final = $stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM client_infos");
        $infos_final = $stmt->fetch()['total'];
        
        echo "📊 Verificação após limpeza forçada:<br>";
        echo "• Clientes: {$clientes_final}<br>";
        echo "• Ações: {$acoes_final}<br>";
        echo "• Informações: {$infos_final}<br><br>";
        
        if ($clientes_final == 0 && $acoes_final == 0 && $infos_final == 0) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
            echo "<h3>🎉 LIMPEZA FORÇADA CONCLUÍDA!</h3>";
            echo "Todos os dados do CRM foram completamente removidos.";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
            echo "<h3>⚠️ AINDA HÁ DADOS RESTANTES</h3>";
            echo "Pode ser necessário verificar manualmente.";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h3>✅ CRM JÁ ESTÁ LIMPO!</h3>";
        echo "Não há dados para remover.";
        echo "</div>";
    }
    
    // Limpar cache da sessão se existir
    if (isset($_SESSION['crm_cache'])) {
        unset($_SESSION['crm_cache']);
        echo "✅ Cache da sessão limpo<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ ERRO</h3>";
    echo "Erro: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<br><br>";
echo "<div style='text-align: center;'>";
echo "<a href='public/index.php?path=/crm' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>🔄 VERIFICAR CRM AGORA</a>";
echo "<a href='limpar_crm_completo.php' style='background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🗑️ EXECUTAR LIMPEZA NOVAMENTE</a>";
echo "</div>";
?>
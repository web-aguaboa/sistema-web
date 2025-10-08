<?php
require_once 'config/init.php';

echo "<h2>🧪 Teste Simples da Função Limpar Tudo</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    
    echo "<h3>1. Estado atual das tabelas:</h3>";
    
    $tabelas = ['users', 'clients', 'envase_data', 'upload_history', 'activity_log', 'actions'];
    
    foreach ($tabelas as $tabela) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM `{$tabela}`");
            $total = $stmt->fetch()['total'];
            echo "✅ {$tabela}: {$total} registros<br>";
        } catch (Exception $e) {
            echo "❌ {$tabela}: ERRO - " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<br><h3>2. Testando limpeza simplificada:</h3>";
    
    // Teste básico de limpeza
    $pdo->query("SET FOREIGN_KEY_CHECKS = 0");
    
    $removidos = [];
    
    // Tentar limpar apenas as principais
    $tabelasLimpar = ['actions', 'clients', 'envase_data', 'upload_history'];
    
    foreach ($tabelasLimpar as $tabela) {
        try {
            $stmt = $pdo->prepare("DELETE FROM `{$tabela}`");
            $stmt->execute();
            $removidos[$tabela] = $stmt->rowCount();
            echo "✅ {$tabela}: {$removidos[$tabela]} registros removidos<br>";
            
            // Resetar auto increment
            $pdo->query("ALTER TABLE `{$tabela}` AUTO_INCREMENT = 1");
            
        } catch (Exception $e) {
            echo "❌ Erro ao limpar {$tabela}: " . $e->getMessage() . "<br>";
            $removidos[$tabela] = 0;
        }
    }
    
    $pdo->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<br><h3>3. Estado após limpeza:</h3>";
    
    foreach ($tabelas as $tabela) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM `{$tabela}`");
            $total = $stmt->fetch()['total'];
            echo "📊 {$tabela}: {$total} registros<br>";
        } catch (Exception $e) {
            echo "❌ {$tabela}: ERRO - " . $e->getMessage() . "<br>";
        }
    }
    
    $total_removido = array_sum($removidos);
    
    if ($total_removido > 0) {
        echo "<br><div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h3>🎉 LIMPEZA REALIZADA COM SUCESSO!</h3>";
        echo "Total de registros removidos: {$total_removido}<br>";
        foreach ($removidos as $tabela => $qtd) {
            if ($qtd > 0) {
                echo "• {$tabela}: {$qtd}<br>";
            }
        }
        echo "</div>";
    } else {
        echo "<br><div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; border-left: 4px solid #bee5eb;'>";
        echo "<h3>ℹ️ SISTEMA JÁ ESTAVA LIMPO</h3>";
        echo "Não havia dados para remover.";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ ERRO NO TESTE</h3>";
    echo "Erro: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<br><br>";
echo "<div style='text-align: center;'>";
echo "<a href='public/index.php?path=/envase' style='background: #007fa3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔄 IR PARA DASHBOARD ENVASE</a>";
echo " ";
echo "<a href='public/index.php?path=/crm' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>👥 IR PARA CRM</a>";
echo "</div>";
?>
<?php
require_once 'config/init.php';

echo "<h2>Teste do RelatorioOLAP.htm Real</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Verificar arquivo
    $arquivo = 'public/uploads/RelatorioOLAP.htm';
    if (!file_exists($arquivo)) {
        echo "❌ Arquivo não encontrado: $arquivo<br>";
        exit;
    }
    
    echo "✓ Arquivo encontrado: $arquivo<br>";
    echo "Tamanho: " . number_format(filesize($arquivo)) . " bytes<br><br>";
    
    // Contar registros antes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total_antes = $stmt->fetch()['total'];
    echo "📊 Registros ANTES: {$total_antes}<br><br>";
    
    // Processar arquivo
    echo "<h3>🔄 Processando RelatorioOLAP.htm</h3>";
    
    $envaseController = new EnvaseController();
    $reflection = new ReflectionClass('EnvaseController');
    $method = $reflection->getMethod('processarPlanilha');
    $method->setAccessible(true);
    
    echo "Iniciando processamento...<br>";
    $inicio = microtime(true);
    
    $resultado = $method->invoke($envaseController, $arquivo, 'RelatorioOLAP.htm');
    
    $fim = microtime(true);
    $tempo = round($fim - $inicio, 2);
    
    echo "<h4>📋 Resultado do processamento (em {$tempo}s):</h4>";
    echo "• Sucesso: " . ($resultado['sucesso'] ? '✅ SIM' : '❌ NÃO') . "<br>";
    echo "• Registros processados: " . ($resultado['registros'] ?? 0) . "<br>";
    
    if (!empty($resultado['erros'])) {
        echo "• Erros encontrados: " . count($resultado['erros']) . "<br>";
        echo "<details><summary>Ver erros</summary>";
        foreach (array_slice($resultado['erros'], 0, 10) as $erro) {
            echo "  • $erro<br>";
        }
        if (count($resultado['erros']) > 10) {
            echo "  • ... e mais " . (count($resultado['erros']) - 10) . " erros<br>";
        }
        echo "</details>";
    }
    
    // Contar registros depois
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total_depois = $stmt->fetch()['total'];
    echo "<br>📊 Registros DEPOIS: {$total_depois}<br>";
    echo "📈 Novos registros: " . ($total_depois - $total_antes) . "<br><br>";
    
    if ($total_depois > $total_antes) {
        echo "<h3>✅ SUCESSO! Dados inseridos no banco</h3>";
        
        // Mostrar amostra dos dados inseridos
        $stmt = $pdo->query("SELECT * FROM envase_data ORDER BY id DESC LIMIT 10");
        $novos_registros = $stmt->fetchAll();
        
        echo "<h4>📊 Últimos 10 registros inseridos:</h4>";
        echo "<table border='1' style='border-collapse: collapse; font-size: 11px;'>";
        echo "<tr><th>ID</th><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Ano</th><th>Mês</th><th>Dia</th><th>Quantidade</th><th>Arquivo</th></tr>";
        
        foreach ($novos_registros as $reg) {
            echo "<tr>";
            echo "<td>{$reg['id']}</td>";
            echo "<td>" . htmlspecialchars(substr($reg['empresa'], 0, 20)) . "</td>";
            echo "<td>" . htmlspecialchars($reg['cidade'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars(substr($reg['produto'], 0, 15)) . "</td>";
            echo "<td>{$reg['ano']}</td>";
            echo "<td>{$reg['mes']}</td>";
            echo "<td>{$reg['dia']}</td>";
            echo "<td>" . number_format($reg['quantidade']) . "</td>";
            echo "<td>" . htmlspecialchars($reg['arquivo_origem'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Estatísticas dos dados inseridos
        echo "<h4>📈 Estatísticas dos dados:</h4>";
        
        // Empresas únicas
        $stmt = $pdo->query("SELECT COUNT(DISTINCT empresa) as total FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm'");
        $empresas = $stmt->fetch()['total'];
        echo "• Empresas diferentes: {$empresas}<br>";
        
        // Produtos únicos
        $stmt = $pdo->query("SELECT COUNT(DISTINCT produto) as total FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm'");
        $produtos = $stmt->fetch()['total'];
        echo "• Produtos diferentes: {$produtos}<br>";
        
        // Total quantidade
        $stmt = $pdo->query("SELECT SUM(quantidade) as total FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm'");
        $total_qtd = $stmt->fetch()['total'];
        echo "• Quantidade total: " . number_format($total_qtd) . "<br>";
        
        // Anos disponíveis
        $stmt = $pdo->query("SELECT DISTINCT ano FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm' ORDER BY ano");
        $anos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "• Anos: " . implode(', ', $anos) . "<br>";
        
        // Top 5 empresas por quantidade
        echo "<br><h4>🏆 Top 5 empresas por quantidade:</h4>";
        $stmt = $pdo->query("SELECT empresa, SUM(quantidade) as total 
                            FROM envase_data 
                            WHERE arquivo_origem = 'RelatorioOLAP.htm' 
                            GROUP BY empresa 
                            ORDER BY total DESC 
                            LIMIT 5");
        $top_empresas = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Empresa</th><th>Quantidade Total</th></tr>";
        foreach ($top_empresas as $emp) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($emp['empresa']) . "</td>";
            echo "<td>" . number_format($emp['total']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<h3>❌ Nenhum dado foi inserido</h3>";
        echo "Possíveis problemas:<br>";
        echo "• Formato do HTML não compatível<br>";
        echo "• Dados não passaram na validação<br>";
        echo "• Erro na extração dos dados<br>";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
echo "<h3>🎯 Próximos passos:</h3>";
echo "1. <a href='public/index.php' style='color: #007fa3;'>Ver Dashboard Principal</a><br>";
echo "2. <a href='public/index.php?path=/envase' style='color: #007fa3;'>Ver Dados de Envase</a><br>";
echo "3. <a href='public/index.php?path=/envase/charts' style='color: #007fa3;'>Ver Gráficos</a><br>";
echo "</div>";
?>
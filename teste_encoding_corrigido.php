<?php
require_once 'config/init.php';

echo "<h2>Teste de Correção de Encoding - RelatorioOLAP.htm</h2>";

try {
    // Limpar dados antigos primeiro
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query("DELETE FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm'");
    echo "✓ Dados antigos removidos<br><br>";
    
    // Verificar arquivo
    $arquivo = 'public/uploads/RelatorioOLAP.htm';
    if (!file_exists($arquivo)) {
        echo "❌ Arquivo não encontrado: $arquivo<br>";
        exit;
    }
    
    echo "✓ Arquivo encontrado: $arquivo<br><br>";
    
    // Processar arquivo com correções de encoding
    echo "<h3>🔄 Processando com correções de encoding</h3>";
    
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
    
    if ($resultado['registros'] > 0) {
        echo "<h3>✅ Dados inseridos! Verificando qualidade dos nomes:</h3>";
        
        // Verificar qualidade dos nomes das empresas
        $stmt = $pdo->query("SELECT DISTINCT empresa FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm' ORDER BY empresa LIMIT 20");
        $empresas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h4>📊 Primeiras 20 empresas (verificando encoding):</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Empresa</th><th>Caracteres problemáticos?</th></tr>";
        
        foreach ($empresas as $empresa) {
            $problemas = [];
            
            // Verificar caracteres problemáticos
            if (strpos($empresa, 'Ã') !== false) {
                $problemas[] = "Contém Ã";
            }
            if (preg_match('/[Ã]{2,}/', $empresa)) {
                $problemas[] = "Múltiplos Ã";
            }
            if (strpos($empresa, '�') !== false) {
                $problemas[] = "Caractere �";
            }
            if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $empresa)) {
                $problemas[] = "Chars controle";
            }
            
            $status = empty($problemas) ? "✅ OK" : "❌ " . implode(", ", $problemas);
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($empresa) . "</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Mostrar exemplos de registros completos
        echo "<h4>📋 Exemplos de registros (com nomes corrigidos):</h4>";
        $stmt = $pdo->query("SELECT * FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm' ORDER BY id LIMIT 10");
        $exemplos = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; font-size: 11px;'>";
        echo "<tr><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Ano</th><th>Mês</th><th>Dia</th><th>Quantidade</th></tr>";
        
        foreach ($exemplos as $reg) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($reg['empresa']) . "</td>";
            echo "<td>" . htmlspecialchars($reg['cidade']) . "</td>";
            echo "<td>" . htmlspecialchars($reg['produto']) . "</td>";
            echo "<td>{$reg['ano']}</td>";
            echo "<td>{$reg['mes']}</td>";
            echo "<td>{$reg['dia']}</td>";
            echo "<td>" . number_format($reg['quantidade']) . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Contar problemas de encoding
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm' AND empresa LIKE '%Ã%'");
        $problemas_encoding = $stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm'");
        $total_registros = $stmt->fetch()['total'];
        
        echo "<h4>📊 Análise de qualidade:</h4>";
        echo "• Total de registros: " . number_format($total_registros) . "<br>";
        echo "• Registros com problemas de encoding: " . number_format($problemas_encoding) . "<br>";
        echo "• Porcentagem de problemas: " . round(($problemas_encoding / $total_registros) * 100, 2) . "%<br>";
        
        if ($problemas_encoding < ($total_registros * 0.1)) {
            echo "<div style='color: green; font-weight: bold;'>✅ EXCELENTE! Menos de 10% dos registros com problemas de encoding.</div>";
        } elseif ($problemas_encoding < ($total_registros * 0.3)) {
            echo "<div style='color: orange; font-weight: bold;'>⚠️ MODERADO: Entre 10-30% dos registros com problemas.</div>";
        } else {
            echo "<div style='color: red; font-weight: bold;'>❌ PROBLEMÁTICO: Mais de 30% dos registros com problemas de encoding.</div>";
        }
        
    } else {
        echo "<h3>❌ Nenhum dado foi processado</h3>";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<h3>🎯 Próximos passos:</h3>";
echo "1. <a href='public/index.php?path=/envase' style='color: #007fa3;'>Ver dados no Dashboard de Envase</a><br>";
echo "2. <a href='public/index.php?path=/crm' style='color: #007fa3;'>Verificar no CRM se nomes estão corretos</a><br>";
echo "3. <a href='public/index.php?path=/crm/unify-clients' style='color: #007fa3;'>Usar unificação de clientes se necessário</a><br>";
echo "</div>";
?>
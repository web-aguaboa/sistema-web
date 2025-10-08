<?php
require_once 'config/init.php';

echo "<h2>Reprocessamento com Correções Melhoradas</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Limpar dados antigos
    echo "<h3>1. Limpando dados antigos</h3>";
    $stmt = $pdo->query("DELETE FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm'");
    echo "✅ Dados antigos removidos<br><br>";
    
    // Verificar arquivo
    $arquivo = 'public/uploads/RelatorioOLAP.htm';
    if (!file_exists($arquivo)) {
        echo "❌ Arquivo não encontrado: $arquivo<br>";
        exit;
    }
    
    echo "<h3>2. Reprocessando com correções melhoradas</h3>";
    
    $envaseController = new EnvaseController();
    $reflection = new ReflectionClass('EnvaseController');
    $method = $reflection->getMethod('processarPlanilha');
    $method->setAccessible(true);
    
    echo "Processando...<br>";
    $inicio = microtime(true);
    
    $resultado = $method->invoke($envaseController, $arquivo, 'RelatorioOLAP.htm');
    
    $fim = microtime(true);
    $tempo = round($fim - $inicio, 2);
    
    echo "<h4>📋 Resultado (em {$tempo}s):</h4>";
    echo "• Sucesso: " . ($resultado['sucesso'] ? '✅ SIM' : '❌ NÃO') . "<br>";
    echo "• Registros: " . ($resultado['registros'] ?? 0) . "<br><br>";
    
    if ($resultado['registros'] > 0) {
        echo "<h3>3. Verificando qualidade dos nomes</h3>";
        
        // Contar problemas
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM envase_data 
            WHERE arquivo_origem = 'RelatorioOLAP.htm'
              AND (empresa LIKE '%Ã%' OR empresa LIKE '%?%' OR empresa REGEXP '[Ã]{2,}')
        ");
        $problemas = $stmt->fetch()['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data WHERE arquivo_origem = 'RelatorioOLAP.htm'");
        $total = $stmt->fetch()['total'];
        
        echo "• Total de registros: " . number_format($total) . "<br>";
        echo "• Registros com problemas: " . number_format($problemas) . "<br>";
        echo "• Qualidade: " . round((($total - $problemas) / $total) * 100, 2) . "%<br><br>";
        
        // Mostrar exemplos de empresas
        echo "<h4>📊 Exemplos de empresas processadas:</h4>";
        $stmt = $pdo->query("
            SELECT DISTINCT empresa, cidade, COUNT(*) as registros
            FROM envase_data 
            WHERE arquivo_origem = 'RelatorioOLAP.htm' 
            GROUP BY empresa, cidade
            ORDER BY empresa 
            LIMIT 15
        ");
        $empresas = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Empresa</th><th>Cidade</th><th>Registros</th><th>Status</th></tr>";
        
        foreach ($empresas as $emp) {
            $status = "✅ OK";
            if (strpos($emp['empresa'], 'Ã') !== false || strpos($emp['empresa'], '?') !== false) {
                $status = "❌ Problema";
            }
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($emp['empresa']) . "</td>";
            echo "<td>" . htmlspecialchars($emp['cidade']) . "</td>";
            echo "<td>" . number_format($emp['registros']) . "</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Se ainda há problemas, aplicar correção direta
        if ($problemas > 0) {
            echo "<h3>4. Aplicando correção direta nos problemas restantes</h3>";
            
            $stmt = $pdo->query("
                SELECT id, empresa, cidade, produto 
                FROM envase_data 
                WHERE arquivo_origem = 'RelatorioOLAP.htm'
                  AND (empresa LIKE '%Ã%' OR empresa LIKE '%?%' OR cidade LIKE '%Ã%' OR cidade LIKE '%?%')
            ");
            $registros_problematicos = $stmt->fetchAll();
            
            $correcoes = 0;
            foreach ($registros_problematicos as $reg) {
                // Aplicar correções específicas
                $empresa_corrigida = corrigirNomeEmpresa($reg['empresa']);
                $cidade_corrigida = corrigirNomeCidade($reg['cidade']);
                $produto_corrigido = corrigirNomeProduto($reg['produto']);
                
                if ($empresa_corrigida !== $reg['empresa'] || $cidade_corrigida !== $reg['cidade'] || $produto_corrigido !== $reg['produto']) {
                    $stmt_update = $pdo->prepare("UPDATE envase_data SET empresa = ?, cidade = ?, produto = ? WHERE id = ?");
                    if ($stmt_update->execute([$empresa_corrigida, $cidade_corrigida, $produto_corrigido, $reg['id']])) {
                        $correcoes++;
                    }
                }
            }
            
            echo "✅ {$correcoes} registros corrigidos diretamente<br><br>";
        }
        
        // Estatísticas finais
        echo "<h3>5. Resultado final</h3>";
        
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM envase_data 
            WHERE arquivo_origem = 'RelatorioOLAP.htm'
              AND (empresa LIKE '%Ã%' OR empresa LIKE '%?%')
        ");
        $problemas_finais = $stmt->fetch()['total'];
        
        $qualidade_final = round((($total - $problemas_finais) / $total) * 100, 2);
        
        echo "• Qualidade final: {$qualidade_final}%<br>";
        
        if ($qualidade_final >= 95) {
            echo "<div style='color: green; font-weight: bold;'>🎉 EXCELENTE! Qualidade ≥ 95%</div>";
        } elseif ($qualidade_final >= 80) {
            echo "<div style='color: orange; font-weight: bold;'>👍 BOM! Qualidade ≥ 80%</div>";
        } else {
            echo "<div style='color: red; font-weight: bold;'>⚠️ Ainda há problemas significativos</div>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage();
}

function corrigirNomeEmpresa($nome) {
    $correções = [
        // Padrões específicos observados
        '/AABBA ITAPECERICAA.*/' => 'AABBA ITAPECERICA',
        '/AABBÁ ITAPECERICAÁ.*/' => 'AABBÁ ITAPECERICA', 
        '/ABDIELÁ.*/' => 'ABDIELA',
        '/ABDIELÃ.*/' => 'ABDIELA',
        // Remover sequências de caracteres problemáticos no final
        '/\s+[ÃÁa\?]+\s*$/' => '',
        '/\s+[Ã\s]+$/' => '',
        '/\s+[a\?\s]+$/' => '',
    ];
    
    foreach ($correções as $regex => $substituto) {
        $nome = preg_replace($regex, $substituto, $nome);
    }
    
    return trim($nome);
}

function corrigirNomeCidade($nome) {
    $correções = [
        'SOAÁ PAULO' => 'SÃO PAULO',
        'SAOÁ PAULO' => 'SÃO PAULO', 
        '/ÁGUASÁ.*SANTAÁ.*/' => 'ÁGUAS DE SANTA BÁRBARA',
        '/ÁGUASÃ.*SANTAÃ.*/' => 'ÁGUAS DE SANTA BÁRBARA',
        '/.*ÁGUAS.*SANTAA.*/' => 'ÁGUAS DE SANTA BÁRBARA',
        '/\s+[ÃÁa\?]+\s*$/' => '',
    ];
    
    foreach ($correções as $buscar => $substituto) {
        if (strpos($buscar, '/') === 0) {
            $nome = preg_replace($buscar, $substituto, $nome);
        } else {
            $nome = str_replace($buscar, $substituto, $nome);
        }
    }
    
    return trim($nome);
}

function corrigirNomeProduto($nome) {
    // Remover caracteres problemáticos dos produtos
    $nome = preg_replace('/[Ãa\?]+/', '', $nome);
    $nome = preg_replace('/\s+/', ' ', trim($nome));
    return $nome;
}

echo "<br><br>";
echo "<a href='public/index.php?path=/envase' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ver Dados Corrigidos</a>";
?>
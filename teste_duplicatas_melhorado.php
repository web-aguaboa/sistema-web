<?php
require_once 'config/init.php';

echo "<h2>🔍 Teste de Detecção de Duplicatas Melhorado</h2>";

try {
    $clientModel = new Client();
    
    echo "<h3>1. Testando detecção com exemplos da imagem:</h3>";
    
    // Simular clientes como na imagem
    $clientes_teste = [
        ['id' => 1, 'cliente' => 'EMBU DISTR (ADEGA TROPICAL)', 'empresa' => 'EMBU DISTR (ADEGA TROPICAL)', 'total_envases' => 110],
        ['id' => 2, 'cliente' => 'EMBU DISTR (BELLA FONTE)', 'empresa' => 'EMBU DISTR (BELLA FONTE)', 'total_envases' => 2321],
        ['id' => 3, 'cliente' => 'EMBU DISTR (CACH DO MANDAQUI)', 'empresa' => 'EMBU DISTR (CACH DO MANDAQUI)', 'total_envases' => 9681],
        ['id' => 4, 'cliente' => 'EMBU DISTR (CASA DA AGUA)', 'empresa' => 'EMBU DISTR (CASA DA AGUA)', 'total_envases' => 997],
        ['id' => 5, 'cliente' => 'EMBU DISTR (CESAR)', 'empresa' => 'EMBU DISTR (CESAR)', 'total_envases' => 129355],
        ['id' => 6, 'cliente' => 'EMBU DISTR (COMERCIAL DE BEB. IG)', 'empresa' => 'EMBU DISTR (COMERCIAL DE BEB. IG)', 'total_envases' => 6888],
    ];
    
    echo "<h4>Clientes de teste:</h4>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Envases</th></tr>";
    foreach ($clientes_teste as $cliente) {
        echo "<tr>";
        echo "<td>{$cliente['id']}</td>";
        echo "<td>" . htmlspecialchars($cliente['cliente']) . "</td>";
        echo "<td>" . number_format($cliente['total_envases']) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // Testar detecção usando reflexão para acessar método privado
    $reflection = new ReflectionClass($clientModel);
    $method = $reflection->getMethod('clientsAreSimilar');
    $method->setAccessible(true);
    
    echo "<h4>Testando similaridade entre pares:</h4>";
    $grupos_detectados = [];
    $processados = [];
    
    foreach ($clientes_teste as $i => $cliente1) {
        if (in_array($cliente1['id'], $processados)) continue;
        
        $grupo = [$cliente1];
        $processados[] = $cliente1['id'];
        
        foreach ($clientes_teste as $j => $cliente2) {
            if ($i >= $j || in_array($cliente2['id'], $processados)) continue;
            
            $similar = $method->invoke($clientModel, $cliente1, $cliente2);
            echo "• {$cliente1['cliente']} <-> {$cliente2['cliente']}: " . ($similar ? "✅ SIMILAR" : "❌ DIFERENTE") . "<br>";
            
            if ($similar) {
                $grupo[] = $cliente2;
                $processados[] = $cliente2['id'];
            }
        }
        
        if (count($grupo) > 1) {
            $grupos_detectados[] = $grupo;
        }
    }
    
    echo "<br><h3>2. Grupos de duplicatas detectados:</h3>";
    
    if (!empty($grupos_detectados)) {
        foreach ($grupos_detectados as $i => $grupo) {
            echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745; border-radius: 5px;'>";
            echo "<h4>Grupo " . ($i + 1) . " - EMBU DISTR (" . count($grupo) . " clientes)</h4>";
            
            // Ordenar por envases
            usort($grupo, function($a, $b) {
                return $b['total_envases'] <=> $a['total_envases'];
            });
            
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Papel</th><th>Nome</th><th>Envases</th></tr>";
            
            foreach ($grupo as $j => $cliente) {
                $papel = $j === 0 ? "👑 Principal" : "🔄 Duplicata";
                echo "<tr>";
                echo "<td>$papel</td>";
                echo "<td>" . htmlspecialchars($cliente['cliente']) . "</td>";
                echo "<td>" . number_format($cliente['total_envases']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        }
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h4>🎉 SUCESSO!</h4>";
        echo "Detectados " . count($grupos_detectados) . " grupo(s) de duplicatas<br>";
        echo "Total de clientes duplicados: " . array_sum(array_map(function($g) { return count($g) - 1; }, $grupos_detectados));
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
        echo "<h4>❌ PROBLEMA</h4>";
        echo "Nenhum grupo de duplicatas foi detectado com o algoritmo atual.";
        echo "</div>";
    }
    
    echo "<br><h3>3. Testando no banco real:</h3>";
    $duplicatas_reais = $clientModel->findDuplicateClients();
    
    if (!empty($duplicatas_reais)) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h4>✅ DUPLICATAS ENCONTRADAS NO BANCO!</h4>";
        echo "Total de grupos: " . count($duplicatas_reais) . "<br>";
        
        foreach ($duplicatas_reais as $i => $grupo) {
            echo "<br><strong>Grupo " . ($i + 1) . ":</strong><br>";
            foreach ($grupo as $cliente) {
                echo "• " . htmlspecialchars($cliente['cliente']) . " (" . number_format($cliente['total_envases']) . " envases)<br>";
            }
        }
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h4>⚠️ NENHUMA DUPLICATA ENCONTRADA</h4>";
        echo "O algoritmo não detectou duplicatas no banco atual.";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ ERRO</h3>";
    echo "Erro: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<br><br>";
echo "<div style='text-align: center;'>";
echo "<a href='public/index.php?path=/crm/unify-clients' style='background: #ffc107; color: #212529; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔄 TESTAR UNIFICAÇÃO</a>";
echo " ";
echo "<a href='public/index.php?path=/crm' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>👥 VOLTAR AO CRM</a>";
echo "</div>";
?>
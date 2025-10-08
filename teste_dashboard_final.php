<?php
require_once 'config/init.php';

echo "<h2>Teste Final - Verificação Dashboard</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    
    // 1. Verificar dados na tabela
    echo "<h3>1. Dados na tabela envase_data:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total = $stmt->fetch()['total'];
    echo "Total de registros: <strong>{$total}</strong><br><br>";
    
    if ($total > 0) {
        // Mostrar últimos 10 registros
        $stmt = $pdo->query("SELECT * FROM envase_data ORDER BY id DESC LIMIT 10");
        $registros = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; font-size: 12px;'>";
        echo "<tr><th>ID</th><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Ano</th><th>Mês</th><th>Dia</th><th>Quantidade</th><th>Arquivo</th><th>Data Upload</th></tr>";
        
        foreach ($registros as $reg) {
            echo "<tr>";
            echo "<td>{$reg['id']}</td>";
            echo "<td>" . htmlspecialchars($reg['empresa']) . "</td>";
            echo "<td>" . htmlspecialchars($reg['cidade'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($reg['produto']) . "</td>";
            echo "<td>{$reg['ano']}</td>";
            echo "<td>{$reg['mes']}</td>";
            echo "<td>{$reg['dia']}</td>";
            echo "<td>{$reg['quantidade']}</td>";
            echo "<td>" . htmlspecialchars($reg['arquivo_origem'] ?? '') . "</td>";
            echo "<td>{$reg['data_upload']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // 2. Testar estatísticas do modelo Envase
    echo "<h3>2. Estatísticas do modelo Envase:</h3>";
    $envaseModel = new Envase();
    $stats = $envaseModel->getStats();
    
    echo "- Total de registros: {$stats['total_registros']}<br>";
    echo "- Empresas únicas: {$stats['empresas_unicas']}<br>";
    echo "- Produtos únicos: {$stats['produtos_unicos']}<br>";
    echo "- Total quantidade: " . number_format($stats['total_quantidade']) . "<br>";
    echo "- Anos disponíveis: " . implode(', ', $stats['anos_disponiveis']) . "<br><br>";
    
    // 3. Testar histórico de uploads
    echo "<h3>3. Histórico de uploads:</h3>";
    $uploadModel = new UploadHistory();
    $uploads = $uploadModel->getRecent(5);
    
    if (!empty($uploads)) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Arquivo</th><th>Status</th><th>Registros</th><th>Data</th></tr>";
        
        foreach ($uploads as $upload) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($upload['nome_arquivo']) . "</td>";
            echo "<td>{$upload['status']}</td>";
            echo "<td>{$upload['registros_processados']}</td>";
            echo "<td>" . date('d/m/Y H:i', strtotime($upload['data_upload'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum upload encontrado no histórico.";
    }
    
    // 4. Inserir um registro de teste via HTML simulado
    echo "<br><h3>4. Teste de inserção via HTML simulado:</h3>";
    
    $html_test = '<table>
        <tr><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Ano</th><th>Mês</th><th>Dia</th><th>Quantidade</th></tr>
        <tr><td>TESTE FINAL HTML</td><td>São Paulo</td><td>AGUA MINERAL 500ML</td><td>2024</td><td>1</td><td>20</td><td>1500</td></tr>
    </table>';
    
    $temp_file = 'public/uploads/teste_final.html';
    file_put_contents($temp_file, $html_test);
    
    $envaseController = new EnvaseController();
    $reflection = new ReflectionClass('EnvaseController');
    $method = $reflection->getMethod('processarPlanilha');
    $method->setAccessible(true);
    
    $resultado = $method->invoke($envaseController, $temp_file, 'teste_final.html');
    
    echo "Resultado do processamento:<br>";
    echo "- Sucesso: " . ($resultado['sucesso'] ? 'SIM' : 'NÃO') . "<br>";
    echo "- Registros: " . ($resultado['registros'] ?? 0) . "<br>";
    if (!empty($resultado['erros'])) {
        echo "- Erros: " . count($resultado['erros']) . "<br>";
        foreach ($resultado['erros'] as $erro) {
            echo "  • $erro<br>";
        }
    }
    
    // Limpar arquivo temporário
    if (file_exists($temp_file)) {
        unlink($temp_file);
    }
    
    // 5. Verificar novamente após inserção
    echo "<br><h3>5. Verificação final:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total_final = $stmt->fetch()['total'];
    echo "Total de registros final: <strong>{$total_final}</strong><br>";
    
    // 6. Comparar o que o dashboard mostraria
    echo "<br><h3>6. Preview do que apareceria no dashboard:</h3>";
    $stats_final = $envaseModel->getStats();
    
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h4>📊 Estatísticas Dashboard:</h4>";
    echo "• Total de Registros: " . number_format($stats_final['total_registros']) . "<br>";
    echo "• Empresas Cadastradas: " . number_format($stats_final['empresas_unicas']) . "<br>";
    echo "• Produtos Diferentes: " . number_format($stats_final['produtos_unicos']) . "<br>";
    echo "• Total Envases: " . number_format($stats_final['total_quantidade']) . "<br>";
    echo "</div>";
    
    if ($stats_final['total_registros'] > 0) {
        echo "<div style='color: green; font-weight: bold;'>✅ SUCESSO! Os dados estão sendo processados e devem aparecer no dashboard.</div>";
    } else {
        echo "<div style='color: red; font-weight: bold;'>❌ PROBLEMA! Nenhum dado foi encontrado.</div>";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br>";
echo "<a href='public/index.php' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Ir para o Dashboard</a>";
echo " ";
echo "<a href='public/index.php?path=/envase' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📊 Ver Envase</a>";
?>
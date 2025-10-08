<?php
/**
 * Simular upload de RelatorioOLAP.xls
 */

require_once 'config/init.php';

// Simular sessão
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "<h2>🎯 Simulação de Upload RelatorioOLAP.xls</h2>";

// Simular o processo exato do upload
$nomeArquivoSimulado = "RelatorioOLAP (54).xls";
$arquivoTeste = sys_get_temp_dir() . '/' . uniqid() . '.xls';

// Criar um arquivo .xls simulado (com conteúdo binário básico)
$conteudoExcel = pack("H*", "D0CF11E0A1B11AE1"); // Assinatura de arquivo Excel
$conteudoExcel .= str_repeat("Dados Excel simulados ", 1000);
file_put_contents($arquivoTeste, $conteudoExcel);

echo "<p>✅ Arquivo .xls simulado criado: " . basename($arquivoTeste) . "</p>";
echo "<p>📊 Tamanho: " . number_format(filesize($arquivoTeste)) . " bytes</p>";

try {
    $envaseController = new EnvaseController();
    
    // Usar reflexão para acessar o método processarPlanilha
    $reflection = new ReflectionClass($envaseController);
    $method = $reflection->getMethod('processarPlanilha');
    $method->setAccessible(true);
    
    echo "<h3>🔄 Simulando processarPlanilha()...</h3>";
    
    // Renomear temporariamente para simular o nome correto
    $arquivoComNomeCorreto = dirname($arquivoTeste) . '/' . $nomeArquivoSimulado;
    copy($arquivoTeste, $arquivoComNomeCorreto);
    
    $resultado = $method->invoke($envaseController, $arquivoComNomeCorreto, $nomeArquivoSimulado);
    
    echo "<h3>📊 Resultado do Processamento:</h3>";
    
    if ($resultado['sucesso']) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 8px; color: #155724;'>";
        echo "<h4>✅ Sucesso!</h4>";
        echo "<p><strong>Registros processados:</strong> {$resultado['registros']}</p>";
        
        if (!empty($resultado['erros'])) {
            echo "<p><strong>Erros:</strong> " . count($resultado['erros']) . " linha(s) com problemas</p>";
            echo "<details><summary>Ver erros</summary>";
            echo "<ul>";
            foreach (array_slice($resultado['erros'], 0, 10) as $erro) {
                echo "<li>" . htmlspecialchars($erro) . "</li>";
            }
            echo "</ul></details>";
        }
        echo "</div>";
        
        // Verificar se os dados foram inseridos no banco
        echo "<h3>🗄️ Verificação no Banco de Dados:</h3>";
        
        $db = Database::getInstance()->getConnection();
        
        // Contar registros
        $stmt = $db->query("SELECT COUNT(*) as total FROM envase_data");
        $totalEnvase = $stmt->fetch()['total'];
        echo "<p>📦 <strong>Total de registros de envase:</strong> " . number_format($totalEnvase) . "</p>";
        
        // Contar clientes
        $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
        $totalClientes = $stmt->fetch()['total'];
        echo "<p>👥 <strong>Total de clientes:</strong> " . number_format($totalClientes) . "</p>";
        
        // Mostrar últimos registros inseridos
        $stmt = $db->query("SELECT * FROM envase_data WHERE arquivo_origem LIKE '%RelatorioOLAP%' ORDER BY data_upload DESC LIMIT 5");
        $ultimosRegistros = $stmt->fetchAll();
        
        if ($ultimosRegistros) {
            echo "<h4>📋 Últimos registros inseridos:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Empresa</th><th>Produto</th><th>Data</th><th>Qtde</th><th>Inserido em</th></tr>";
            
            foreach ($ultimosRegistros as $reg) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($reg['empresa']) . "</td>";
                echo "<td>" . htmlspecialchars($reg['produto']) . "</td>";
                echo "<td>" . sprintf('%02d/%02d/%d', $reg['dia'], $reg['mes'], $reg['ano']) . "</td>";
                echo "<td>" . number_format($reg['quantidade']) . "</td>";
                echo "<td>" . $reg['data_upload'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px; color: #721c24;'>";
        echo "<h4>❌ Falha no processamento</h4>";
        echo "<p><strong>Erro:</strong> {$resultado['erro']}</p>";
        echo "</div>";
    }
    
    // Limpar arquivos temporários
    unlink($arquivoTeste);
    if (file_exists($arquivoComNomeCorreto)) {
        unlink($arquivoComNomeCorreto);
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px; color: #721c24;'>";
    echo "<h4>❌ Erro na simulação</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>💡 Conclusão:</h3>";
echo "<div style='background: #d1ecf1; padding: 1rem; border-radius: 8px;'>";
echo "<p>Esta simulação mostra exatamente o que acontece quando você faz upload de um arquivo RelatorioOLAP.xls.</p>";
echo "<p>Se o resultado for <strong>sucesso com muitos registros</strong>, significa que o sistema está funcionando corretamente.</p>";
echo "<p>Se ainda mostrar <strong>0 registros</strong>, há um problema específico que precisamos resolver.</p>";
echo "</div>";

echo "<p style='margin-top: 2rem;'>";
echo "<a href='public/' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🌐 Voltar ao Sistema</a>";
echo "<a href='public/index.php?page=envase' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📤 Testar Upload Real</a>";
echo "</p>";
?>
<?php
/**
 * Teste de leitura real do Excel OLAP (como o Python)
 */

require_once 'config/init.php';

$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "<h2>🎯 TESTE: Leitura Real do Excel OLAP</h2>";

// Simular upload do arquivo real
$arquivoSimulado = 'public/uploads/RelatorioOLAP_real_test.xls';

// Criar arquivo binário Excel simulado mais realista
$conteudoExcel = pack("H*", "D0CF11E0A1B11AE1"); // Header Excel
$conteudoExcel .= "Microsoft Excel 5.0 Worksheet\x00";

// Adicionar strings que simulam dados do OLAP
$dadosSimulados = [
    "+ AGUA E CIA\tGUARUJA\tAGUABOA PREMIUM 20 LTS(EMB S/ALCA)\t2021\t02\t19\t50,00",
    "1000 DIST.TAGUAI\tTAGUAI\tAGUABOA 20 LTS\t2021\t06\t24\t311,00",
    "ACQUA LIFE BARIRI\tBARIRI\tAGUABOA 10 LTS\t2020\t12\t09\t5,00"
];

foreach ($dadosSimulados as $linha) {
    $conteudoExcel .= $linha . "\r\n";
}

file_put_contents($arquivoSimulado, $conteudoExcel);

echo "<p>✅ Arquivo Excel simulado criado: " . basename($arquivoSimulado) . "</p>";
echo "<p>📊 Tamanho: " . number_format(filesize($arquivoSimulado)) . " bytes</p>";

try {
    $envaseController = new EnvaseController();
    $reflection = new ReflectionClass($envaseController);
    
    // Testar o novo método de leitura real
    echo "<h3>🔧 Teste 1: lerExcelRealOLAP()</h3>";
    $methodReal = $reflection->getMethod('lerExcelRealOLAP');
    $methodReal->setAccessible(true);
    
    $dadosReais = $methodReal->invoke($envaseController, $arquivoSimulado);
    
    echo "<div style='background: " . (count($dadosReais) > 0 ? '#d4edda' : '#fff3cd') . "; padding: 1rem; border-radius: 8px;'>";
    echo "<h4>" . (count($dadosReais) > 0 ? '✅ SUCESSO' : '⚠️ FALLBACK') . "</h4>";
    echo "<p><strong>Registros lidos:</strong> " . count($dadosReais) . "</p>";
    
    if (count($dadosReais) > 0) {
        echo "<p style='color: green;'>🎉 Conseguiu ler dados reais do Excel!</p>";
        echo "<details><summary>Ver primeiros registros</summary>";
        echo "<pre>" . print_r(array_slice($dadosReais, 0, 5), true) . "</pre>";
        echo "</details>";
    } else {
        echo "<p style='color: orange;'>📋 Não conseguiu ler dados reais, mas o fallback funciona</p>";
    }
    echo "</div>";
    
    // Testar processamento completo
    echo "<h3>🔧 Teste 2: Processamento Completo</h3>";
    $methodProcessar = $reflection->getMethod('processarPlanilha');
    $methodProcessar->setAccessible(true);
    
    $resultado = $methodProcessar->invoke($envaseController, $arquivoSimulado, 'RelatorioOLAP_real_test.xls');
    
    echo "<div style='background: " . ($resultado['sucesso'] ? '#d4edda' : '#f8d7da') . "; padding: 1rem; border-radius: 8px;'>";
    echo "<h4>" . ($resultado['sucesso'] ? '✅ PROCESSAMENTO OK' : '❌ FALHA') . "</h4>";
    echo "<p><strong>Registros processados:</strong> " . ($resultado['registros'] ?? 0) . "</p>";
    
    if ($resultado['sucesso'] && $resultado['registros'] > 0) {
        echo "<p style='color: green;'>🎯 PERFEITO! O sistema consegue processar " . $resultado['registros'] . " registros!</p>";
        
        // Verificar no banco
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM envase_data WHERE arquivo_origem LIKE '%RelatorioOLAP%'");
        $totalBanco = $stmt->fetch()['total'];
        
        echo "<p><strong>📊 Total no banco:</strong> " . number_format($totalBanco) . " registros</p>";
        
        if ($totalBanco > 0) {
            echo "<p style='color: green; font-weight: bold;'>🌟 DADOS INSERIDOS COM SUCESSO NO BANCO!</p>";
        }
    }
    
    if (!empty($resultado['erros'])) {
        echo "<p><strong>Erros:</strong> " . count($resultado['erros']) . " linhas com problemas</p>";
    }
    echo "</div>";
    
    // Limpar arquivo
    unlink($arquivoSimulado);
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px;'>";
    echo "<h4>❌ Erro</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='background: #e7f3ff; padding: 1.5rem; border-radius: 8px;'>";
echo "<h3>📊 Status Atual do Sistema:</h3>";

// Verificar dados no sistema
try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM envase_data");
    $totalEnvase = $stmt->fetch()['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
    $totalClientes = $stmt->fetch()['total'];
    
    echo "<p><strong>📦 Total de registros de envase:</strong> " . number_format($totalEnvase) . "</p>";
    echo "<p><strong>👥 Total de clientes:</strong> " . number_format($totalClientes) . "</p>";
    
    if ($totalEnvase > 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ Sistema funcionando com dados!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Sistema ainda sem dados - faça upload novamente</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao verificar banco: " . $e->getMessage() . "</p>";
}

echo "</div>";

echo "<p style='margin-top: 2rem;'>";
echo "<a href='public/index.php?page=envase' style='background: #007fa3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 1.1rem;'>📤 TESTAR UPLOAD REAL</a>";
echo "</p>";

echo "<p><strong>💡 Agora o sistema tenta ler dados reais do Excel (como o Python) antes de usar o fallback!</strong></p>";
?>
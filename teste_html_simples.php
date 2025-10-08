<?php
/**
 * Teste simples para upload HTML
 * Sistema Aguaboa - Gestão Comercial
 */

require_once 'config/init.php';

echo "<h1>🔍 Teste Simples - Upload HTML</h1>";

// Criar arquivo HTML muito simples
$htmlSimples = '<!DOCTYPE html>
<html>
<body>
    <table>
        <tr>
            <td>TESTE EMPRESA</td>
            <td>SAO PAULO</td>
            <td>AGUABOA 20L</td>
            <td>2025</td>
            <td>10</td>
            <td>1</td>
            <td>100</td>
        </tr>
    </table>
</body>
</html>';

// Salvar arquivo
$arquivoTeste = UPLOAD_DIR . 'teste_simples.html';
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

file_put_contents($arquivoTeste, $htmlSimples);

echo "<h2>📄 Arquivo HTML Criado</h2>";
echo "<p><strong>Localização:</strong> " . htmlspecialchars($arquivoTeste) . "</p>";
echo "<p><strong>Conteúdo:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 1rem; border-radius: 4px;'>" . htmlspecialchars($htmlSimples) . "</pre>";

try {
    // Simular upload real
    echo "<h2>🔄 Simulando Upload...</h2>";
    
    // Simular $_FILES
    $_FILES = [
        'arquivo' => [
            'name' => 'teste_simples.html',
            'tmp_name' => $arquivoTeste,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($arquivoTeste)
        ]
    ];
    
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    // Criar controller e simular upload
    $envaseController = new EnvaseController();
    
    // Testar método lerHTML diretamente
    $reflection = new ReflectionClass($envaseController);
    $methodLerHTML = $reflection->getMethod('lerHTML');
    $methodLerHTML->setAccessible(true);
    
    echo "<h3>📊 Testando lerHTML diretamente...</h3>";
    
    $dados = $methodLerHTML->invoke($envaseController, $arquivoTeste);
    
    if (!empty($dados)) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724;'>";
        echo "<h4>✅ Sucesso! Dados extraídos:</h4>";
        echo "<ul>";
        foreach ($dados as $registro) {
            echo "<li><strong>{$registro['empresa']}</strong> - {$registro['produto']} - {$registro['quantidade']} unidades em {$registro['ano']}-{$registro['mes']}-{$registro['dia']}</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>";
        echo "<h4>❌ Nenhum dado extraído!</h4>";
        echo "</div>";
    }
    
    // Testar processamento completo
    echo "<h3>⚙️ Testando processamento completo...</h3>";
    
    $methodProcessar = $reflection->getMethod('processarPlanilha');
    $methodProcessar->setAccessible(true);
    
    $resultado = $methodProcessar->invoke($envaseController, $arquivoTeste, 'teste_simples.html');
    
    if ($resultado['sucesso']) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724;'>";
        echo "<h4>✅ Processamento completo bem-sucedido!</h4>";
        echo "<p><strong>Registros processados:</strong> {$resultado['registros']}</p>";
        
        if (!empty($resultado['erros'])) {
            echo "<p><strong>Erros:</strong> " . count($resultado['erros']) . "</p>";
            echo "<ul>";
            foreach ($resultado['erros'] as $erro) {
                echo "<li>" . htmlspecialchars($erro) . "</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
        
        // Verificar no banco
        echo "<h3>📊 Verificando no Banco de Dados...</h3>";
        
        $sql = "SELECT * FROM envase_data WHERE empresa = 'TESTE EMPRESA' ORDER BY id DESC LIMIT 5";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute();
        $registrosDB = $stmt->fetchAll();
        
        if (!empty($registrosDB)) {
            echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724;'>";
            echo "<h4>✅ Dados encontrados no banco!</h4>";
            echo "<table style='width: 100%; border-collapse: collapse;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>ID</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Empresa</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Produto</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Data</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Quantidade</th>";
            echo "</tr>";
            
            foreach ($registrosDB as $reg) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$reg['id']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($reg['empresa']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($reg['produto']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$reg['ano']}-{$reg['mes']}-{$reg['dia']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . number_format($reg['quantidade']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>";
            echo "<h4>❌ Nenhum dado encontrado no banco!</h4>";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>";
        echo "<h4>❌ Erro no processamento completo!</h4>";
        echo "<p>" . htmlspecialchars($resultado['erro']) . "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>";
    echo "<h3>❌ Erro no teste</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

// Limpar arquivo
if (file_exists($arquivoTeste)) {
    unlink($arquivoTeste);
}

echo "<hr>";
echo "<p><a href='" . BASE_URL . "/envase'>← Voltar para Envase</a></p>";
?>
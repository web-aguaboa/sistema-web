<?php
/**
 * Teste específico para o arquivo RelatorioOLAP.csv
 */

require_once 'config/init.php';

// Simular sessão
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "<h2>🧪 Teste do Arquivo OLAP Real</h2>";

$arquivoTeste = 'public/uploads/RelatorioOLAP.csv';

if (!file_exists($arquivoTeste)) {
    echo "<p>❌ Arquivo não encontrado: $arquivoTeste</p>";
    echo "<p>Por favor, copie o arquivo para: c:\\xampp\\htdocs\\gestao-aguaboa-php\\public\\uploads\\RelatorioOLAP.csv</p>";
    exit;
}

echo "<p>✅ Arquivo encontrado: $arquivoTeste</p>";
echo "<p>📊 Tamanho: " . number_format(filesize($arquivoTeste)) . " bytes</p>";

try {
    $envaseController = new EnvaseController();
    
    // Usar reflexão para acessar método privado
    $reflection = new ReflectionClass($envaseController);
    $method = $reflection->getMethod('lerCSV');
    $method->setAccessible(true);
    
    echo "<h3>📋 Processando arquivo...</h3>";
    $dados = $method->invoke($envaseController, $arquivoTeste);
    
    echo "<p>✅ <strong>Registros processados:</strong> " . count($dados) . "</p>";
    
    if (!empty($dados)) {
        // Mostrar primeiros 10 registros
        echo "<h4>🔍 Primeiros 10 registros processados:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 0.9rem;'>";
        echo "<thead style='background: #007fa3; color: white;'>";
        echo "<tr><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Data</th><th>Qtde</th></tr>";
        echo "</thead><tbody>";
        
        foreach (array_slice($dados, 0, 10) as $registro) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($registro['empresa']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['cidade']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['produto']) . "</td>";
            echo "<td>" . sprintf('%02d/%02d/%d', $registro['dia'], $registro['mes'], $registro['ano']) . "</td>";
            echo "<td style='text-align: right;'>" . number_format($registro['quantidade']) . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        
        // Estatísticas
        echo "<h4>📊 Estatísticas do arquivo:</h4>";
        
        $empresas = array_unique(array_column($dados, 'empresa'));
        $produtos = array_unique(array_column($dados, 'produto'));
        $anos = array_unique(array_column($dados, 'ano'));
        $totalQuantidade = array_sum(array_column($dados, 'quantidade'));
        
        echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin: 1rem 0;'>";
        echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;'>";
        echo "<h3 style='color: #007fa3; margin: 0;'>" . count($empresas) . "</h3>";
        echo "<p style='margin: 0; color: #666;'>Empresas únicas</p>";
        echo "</div>";
        
        echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;'>";
        echo "<h3 style='color: #007fa3; margin: 0;'>" . count($produtos) . "</h3>";
        echo "<p style='margin: 0; color: #666;'>Produtos diferentes</p>";
        echo "</div>";
        
        echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;'>";
        echo "<h3 style='color: #007fa3; margin: 0;'>" . implode(', ', $anos) . "</h3>";
        echo "<p style='margin: 0; color: #666;'>Anos disponíveis</p>";
        echo "</div>";
        
        echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;'>";
        echo "<h3 style='color: #007fa3; margin: 0;'>" . number_format($totalQuantidade) . "</h3>";
        echo "<p style='margin: 0; color: #666;'>Total de envases</p>";
        echo "</div>";
        echo "</div>";
        
        // Lista de empresas
        echo "<h4>🏢 Empresas encontradas:</h4>";
        echo "<ul>";
        foreach ($empresas as $empresa) {
            $count = count(array_filter($dados, function($r) use ($empresa) { return $r['empresa'] === $empresa; }));
            echo "<li><strong>" . htmlspecialchars($empresa) . "</strong> ($count registros)</li>";
        }
        echo "</ul>";
        
        // Lista de produtos
        echo "<h4>📦 Produtos encontrados:</h4>";
        echo "<ul>";
        foreach ($produtos as $produto) {
            $count = count(array_filter($dados, function($r) use ($produto) { return $r['produto'] === $produto; }));
            echo "<li><strong>" . htmlspecialchars($produto) . "</strong> ($count registros)</li>";
        }
        echo "</ul>";
        
        // Testar inserção de alguns registros
        echo "<h3>💾 Teste de Inserção no Banco:</h3>";
        
        $db = Database::getInstance()->getConnection();
        $clientModel = new Client();
        $envaseModel = new Envase();
        
        $processados = 0;
        $erros = [];
        
        // Processar primeiros 50 registros como teste
        foreach (array_slice($dados, 0, 50) as $index => $registro) {
            try {
                // Criar cliente se não existir
                $clienteExistente = $clientModel->findByName($registro['empresa']);
                if (!$clienteExistente) {
                    $clientModel->createFromEnvase($registro['empresa'], $registro['cidade']);
                }
                
                // Inserir dados de envase
                if ($envaseModel->upsert($registro)) {
                    $processados++;
                }
            } catch (Exception $e) {
                $erros[] = "Registro " . ($index + 1) . ": " . $e->getMessage();
            }
        }
        
        echo "<p>✅ <strong>Registros inseridos com sucesso:</strong> $processados de 50 testados</p>";
        
        if (!empty($erros)) {
            echo "<h4>⚠️ Erros encontrados:</h4>";
            echo "<ul>";
            foreach (array_slice($erros, 0, 10) as $erro) {
                echo "<li>" . htmlspecialchars($erro) . "</li>";
            }
            echo "</ul>";
        }
        
    } else {
        echo "<p>❌ Nenhum registro foi processado. Verificar formato do arquivo.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro durante processamento:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='public/' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👉 Ir para o Sistema</a></p>";
echo "<p><a href='public/?upload=RelatorioOLAP.csv' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📤 Testar Upload no Sistema</a></p>";
?>
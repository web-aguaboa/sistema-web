<?php
/**
 * Teste específico para upload de arquivo .xls
 */

require_once 'config/init.php';

$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "<h2>🧪 Teste de Upload .XLS Real</h2>";

// Procurar arquivos .xls no diretório de uploads
$uploadDir = 'public/uploads/';
$arquivosXls = glob($uploadDir . '*.xls');

if (empty($arquivosXls)) {
    echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 8px;'>";
    echo "<h3>📁 Nenhum arquivo .xls encontrado</h3>";
    echo "<p>Para testar, faça upload de um arquivo .xls pelo sistema ou coloque na pasta uploads/</p>";
    echo "<a href='public/index.php?page=envase' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📤 Ir para Upload</a>";
    echo "</div>";
} else {
    echo "<h3>📊 Arquivos .xls encontrados:</h3>";
    echo "<ul>";
    foreach ($arquivosXls as $arquivo) {
        $size = filesize($arquivo);
        echo "<li><strong>" . basename($arquivo) . "</strong> (" . number_format($size) . " bytes)</li>";
    }
    echo "</ul>";
    
    // Testar com o arquivo mais recente
    $arquivoTeste = $arquivosXls[0];
    
    echo "<h3>🔧 Testando: " . basename($arquivoTeste) . "</h3>";
    
    try {
        $envaseController = new EnvaseController();
        
        $reflection = new ReflectionClass($envaseController);
        $method = $reflection->getMethod('lerPlanilhaSimples');
        $method->setAccessible(true);
        
        echo "<p>🔄 Processando arquivo .xls...</p>";
        $dados = $method->invoke($envaseController, $arquivoTeste);
        
        echo "<p><strong>📊 Resultado:</strong> " . count($dados) . " registros processados</p>";
        
        if (count($dados) > 0) {
            echo "<div style='background: #d4edda; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
            echo "<h4>✅ Sucesso! Dados processados:</h4>";
            
            // Mostrar alguns registros
            echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 0.9rem;'>";
            echo "<thead style='background: #28a745; color: white;'>";
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
            $empresas = array_unique(array_column($dados, 'empresa'));
            $produtos = array_unique(array_column($dados, 'produto'));
            $totalQtde = array_sum(array_column($dados, 'quantidade'));
            
            echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin: 1rem 0;'>";
            
            echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='color: #007fa3; margin: 0;'>" . count($empresas) . "</h3>";
            echo "<p style='margin: 0;'>Empresas</p>";
            echo "</div>";
            
            echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='color: #007fa3; margin: 0;'>" . count($produtos) . "</h3>";
            echo "<p style='margin: 0;'>Produtos</p>";
            echo "</div>";
            
            echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='color: #007fa3; margin: 0;'>" . number_format($totalQtde) . "</h3>";
            echo "<p style='margin: 0;'>Total Envases</p>";
            echo "</div>";
            
            echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='color: #007fa3; margin: 0;'>" . count($dados) . "</h3>";
            echo "<p style='margin: 0;'>Registros</p>";
            echo "</div>";
            
            echo "</div>";
            
            // Simular o processamento completo
            echo "<h4>💾 Simulação do Processamento Completo:</h4>";
            
            $clientModel = new Client();
            $envaseModel = new Envase();
            $activityLog = new ActivityLog();
            
            $processados = 0;
            $clientesCriados = 0;
            
            // Processar uma amostra
            foreach (array_slice($dados, 0, 100) as $registro) {
                try {
                    // Verificar/criar cliente
                    $cliente = $clientModel->findByName($registro['empresa']);
                    if (!$cliente) {
                        $clientModel->createFromEnvase($registro['empresa'], $registro['cidade']);
                        $clientesCriados++;
                    }
                    
                    // Inserir dados de envase
                    if ($envaseModel->upsert($registro)) {
                        $processados++;
                    }
                } catch (Exception $e) {
                    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
                }
            }
            
            echo "<p><strong>✅ Resultado da simulação:</strong></p>";
            echo "<ul>";
            echo "<li>📊 Registros processados: $processados de " . min(100, count($dados)) . " testados</li>";
            echo "<li>👥 Clientes criados: $clientesCriados</li>";
            echo "<li>📈 Total potencial: " . count($dados) . " registros</li>";
            echo "</ul>";
            
            echo "</div>";
            
        } else {
            echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px; color: #721c24;'>";
            echo "<h4>⚠️ Nenhum registro processado</h4>";
            echo "<p>O arquivo .xls não pôde ser lido diretamente.</p>";
            echo "<p><strong>💡 Solução recomendada:</strong></p>";
            echo "<ol>";
            echo "<li>Abra o arquivo no Excel</li>";
            echo "<li>Vá em 'Salvar Como'</li>";
            echo "<li>Escolha 'CSV (separado por ponto-e-vírgula)'</li>";
            echo "<li>Faça upload do arquivo .csv gerado</li>";
            echo "</ol>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<h3>📋 Instruções:</h3>";
echo "<div style='background: #d1ecf1; padding: 1rem; border-radius: 8px;'>";
echo "<p><strong>Para melhor resultado com arquivos Excel:</strong></p>";
echo "<ol>";
echo "<li><strong>Conversão manual:</strong> Salve como CSV no Excel</li>";
echo "<li><strong>Upload direto:</strong> O sistema tentará processar automaticamente</li>";
echo "<li><strong>Verificação:</strong> Use esta página para testar antes do upload oficial</li>";
echo "</ol>";
echo "</div>";

echo "<p style='margin-top: 2rem;'>";
echo "<a href='public/' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🌐 Sistema Principal</a>";
echo "<a href='public/index.php?page=envase' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📤 Fazer Upload</a>";
echo "</p>";
?>
<?php
/**
 * Teste específico para arquivos Excel (.xls/.xlsx)
 */

require_once 'config/init.php';

// Simular sessão
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "<h2>🧪 Teste de Upload de Arquivo Excel</h2>";

// Verificar se há arquivos Excel no diretório de uploads
$uploadDir = 'public/uploads/';
$arquivosExcel = [];

if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['xls', 'xlsx'])) {
            $arquivosExcel[] = $file;
        }
    }
}

if (empty($arquivosExcel)) {
    echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
    echo "<h3>📁 Nenhum arquivo Excel encontrado</h3>";
    echo "<p>Para testar, coloque seu arquivo .xls ou .xlsx na pasta:</p>";
    echo "<code>c:\\xampp\\htdocs\\gestao-aguaboa-php\\public\\uploads\\</code>";
    echo "<p><strong>Ou use o sistema de upload normal:</strong></p>";
    echo "<a href='public/' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🌐 Ir para o Sistema</a>";
    echo "</div>";
} else {
    echo "<h3>📊 Arquivos Excel encontrados:</h3>";
    echo "<ul>";
    foreach ($arquivosExcel as $arquivo) {
        echo "<li><strong>$arquivo</strong> (" . number_format(filesize($uploadDir . $arquivo)) . " bytes)</li>";
    }
    echo "</ul>";
    
    // Testar o primeiro arquivo Excel encontrado
    $arquivoTeste = $uploadDir . $arquivosExcel[0];
    
    echo "<h3>🔧 Testando arquivo: " . $arquivosExcel[0] . "</h3>";
    
    try {
        $envaseController = new EnvaseController();
        
        // Usar reflexão para acessar método privado
        $reflection = new ReflectionClass($envaseController);
        $method = $reflection->getMethod('lerPlanilhaSimples');
        $method->setAccessible(true);
        
        echo "<p>📋 Processando arquivo Excel...</p>";
        $dados = $method->invoke($envaseController, $arquivoTeste);
        
        echo "<p>✅ <strong>Registros processados:</strong> " . count($dados) . "</p>";
        
        if (!empty($dados)) {
            // Mostrar primeiros registros
            echo "<h4>🔍 Primeiros registros processados:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 0.9rem;'>";
            echo "<thead style='background: #007fa3; color: white;'>";
            echo "<tr><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Data</th><th>Qtde</th><th>Origem</th></tr>";
            echo "</thead><tbody>";
            
            foreach (array_slice($dados, 0, 10) as $registro) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($registro['empresa']) . "</td>";
                echo "<td>" . htmlspecialchars($registro['cidade']) . "</td>";
                echo "<td>" . htmlspecialchars($registro['produto']) . "</td>";
                echo "<td>" . sprintf('%02d/%02d/%d', $registro['dia'], $registro['mes'], $registro['ano']) . "</td>";
                echo "<td style='text-align: right;'>" . number_format($registro['quantidade']) . "</td>";
                echo "<td style='font-size: 0.8rem;'>" . htmlspecialchars($registro['arquivo_origem']) . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody></table>";
            
            // Estatísticas
            $empresas = array_unique(array_column($dados, 'empresa'));
            $produtos = array_unique(array_column($dados, 'produto'));
            $totalQuantidade = array_sum(array_column($dados, 'quantidade'));
            
            echo "<h4>📊 Estatísticas:</h4>";
            echo "<div style='display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 1rem 0;'>";
            
            echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='color: #007fa3; margin: 0;'>" . count($empresas) . "</h3>";
            echo "<p style='margin: 0; color: #666;'>Empresas</p>";
            echo "</div>";
            
            echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='color: #007fa3; margin: 0;'>" . count($produtos) . "</h3>";
            echo "<p style='margin: 0; color: #666;'>Produtos</p>";
            echo "</div>";
            
            echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; text-align: center;'>";
            echo "<h3 style='color: #007fa3; margin: 0;'>" . number_format($totalQuantidade) . "</h3>";
            echo "<p style='margin: 0; color: #666;'>Total Envases</p>";
            echo "</div>";
            
            echo "</div>";
            
            // Teste de inserção
            echo "<h4>💾 Teste de Inserção:</h4>";
            
            $clientModel = new Client();
            $envaseModel = new Envase();
            $processados = 0;
            
            foreach (array_slice($dados, 0, 10) as $registro) {
                try {
                    // Criar cliente se necessário
                    $cliente = $clientModel->findByName($registro['empresa']);
                    if (!$cliente) {
                        $clientModel->createFromEnvase($registro['empresa'], $registro['cidade']);
                        echo "<p>✅ Cliente criado: " . htmlspecialchars($registro['empresa']) . "</p>";
                    }
                    
                    // Inserir envase
                    if ($envaseModel->upsert($registro)) {
                        $processados++;
                    }
                } catch (Exception $e) {
                    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
                }
            }
            
            echo "<p><strong>✅ Registros inseridos com sucesso: $processados</strong></p>";
            
        } else {
            echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px; color: #721c24;'>";
            echo "<h4>⚠️ Nenhum registro processado</h4>";
            echo "<p>Possíveis causas:</p>";
            echo "<ul>";
            echo "<li>Arquivo Excel pode estar em formato binário não legível</li>";
            echo "<li>Estrutura do arquivo não corresponde ao esperado</li>";
            echo "<li>Arquivo pode estar corrompido</li>";
            echo "</ul>";
            echo "<p><strong>Sugestão:</strong> Salve o arquivo como CSV (UTF-8) no Excel</p>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<h3>📋 Instruções para Upload de Excel:</h3>";
echo "<div style='background: #d1ecf1; padding: 1rem; border-radius: 8px;'>";
echo "<ol>";
echo "<li><strong>Salvar como CSV:</strong> No Excel, vá em 'Salvar Como' → 'CSV (separado por vírgulas)'</li>";
echo "<li><strong>Ou usar separador ';':</strong> 'CSV (separado por ponto-e-vírgula)'</li>";
echo "<li><strong>Verificar dados:</strong> Certifique-se que tem as colunas: Empresa;Cidade;Produto;Ano;Mês;Dia;Quantidade</li>";
echo "<li><strong>Upload normal:</strong> Use o sistema de upload na página de Envase</li>";
echo "</ol>";
echo "</div>";

echo "<p style='margin-top: 2rem;'>";
echo "<a href='public/' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🌐 Ir para o Sistema</a>";
echo "<a href='public/index.php?page=envase' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📤 Página de Upload</a>";
echo "</p>";
?>
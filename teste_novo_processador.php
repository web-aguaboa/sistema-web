<?php
/**
 * Teste rápido do novo processador OLAP
 */

require_once 'config/init.php';

$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

echo "<h2>🧪 Teste do Novo Processador OLAP</h2>";

$arquivo = 'public/uploads/RelatorioOLAP.csv';

if (!file_exists($arquivo)) {
    echo "<p>❌ Arquivo não encontrado</p>";
    exit;
}

try {
    $envaseController = new EnvaseController();
    
    $reflection = new ReflectionClass($envaseController);
    $method = $reflection->getMethod('lerCSV');
    $method->setAccessible(true);
    
    echo "<p>🔄 Testando novo processador hierárquico...</p>";
    $dados = $method->invoke($envaseController, $arquivo);
    
    echo "<p><strong>📊 Resultado:</strong> " . count($dados) . " registros processados</p>";
    
    if (!empty($dados)) {
        echo "<h3>✅ Primeiros 10 registros:</h3>";
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
        $empresas = array_unique(array_column($dados, 'empresa'));
        $produtos = array_unique(array_column($dados, 'produto'));
        $totalQuantidade = array_sum(array_column($dados, 'quantidade'));
        
        echo "<h3>📊 Estatísticas:</h3>";
        echo "<p><strong>Empresas:</strong> " . count($empresas) . "</p>";
        echo "<p><strong>Produtos:</strong> " . count($produtos) . "</p>";
        echo "<p><strong>Total Envases:</strong> " . number_format($totalQuantidade) . "</p>";
        
        echo "<h4>Empresas encontradas:</h4>";
        echo "<ul>";
        foreach ($empresas as $empresa) {
            echo "<li>" . htmlspecialchars($empresa) . "</li>";
        }
        echo "</ul>";
        
        // Testar inserção no banco
        echo "<h3>💾 Teste de Inserção no Banco:</h3>";
        
        $clientModel = new Client();
        $envaseModel = new Envase();
        $processados = 0;
        
        foreach (array_slice($dados, 0, 20) as $registro) {
            try {
                // Criar cliente se não existir
                $cliente = $clientModel->findByName($registro['empresa']);
                if (!$cliente) {
                    $clientModel->createFromEnvase($registro['empresa'], $registro['cidade']);
                }
                
                // Inserir envase
                if ($envaseModel->upsert($registro)) {
                    $processados++;
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<p><strong>✅ Registros inseridos:</strong> $processados de " . min(20, count($dados)) . " testados</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Nenhum registro foi processado</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='public/' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👉 Testar no Sistema</a></p>";
?>
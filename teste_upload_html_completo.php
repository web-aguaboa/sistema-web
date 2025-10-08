<?php
require_once 'config/init.php';

echo "<h2>Teste Completo de Upload de HTML</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    echo "✓ Conexão com banco de dados estabelecida<br><br>";
    
    // Contar registros antes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total_antes = $stmt->fetch()['total'];
    echo "Total de registros ANTES do teste: {$total_antes}<br><br>";
    
    // Simular um arquivo HTML simples
    $html_content = '<table>
        <tr><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Ano</th><th>Mês</th><th>Dia</th><th>Quantidade</th></tr>
        <tr><td>TESTE HTML EMPRESA</td><td>São Paulo</td><td>AGUA NATURAL 500ML</td><td>2024</td><td>1</td><td>15</td><td>1000</td></tr>
        <tr><td>TESTE HTML EMPRESA</td><td>São Paulo</td><td>AGUA COM GAS 500ML</td><td>2024</td><td>1</td><td>16</td><td>750</td></tr>
    </table>';
    
    // Salvar HTML temporário
    $temp_file = 'public/uploads/teste_html_temp.html';
    file_put_contents($temp_file, $html_content);
    echo "✓ Arquivo HTML temporário criado: {$temp_file}<br><br>";
    
    // Testar o método lerHTML do EnvaseController
    echo "<h3>Testando Processamento do HTML</h3>";
    
    $envaseController = new EnvaseController();
    
    // Verificar se o método lerHTML existe
    $reflection = new ReflectionClass('EnvaseController');
    if ($reflection->hasMethod('lerHTML')) {
        echo "✓ Método lerHTML encontrado<br>";
        
        // Chamar método lerHTML
        $method = $reflection->getMethod('lerHTML');
        $method->setAccessible(true);
        
        $dados = $method->invoke($envaseController, $temp_file);
        
        echo "Dados extraídos do HTML:<br>";
        echo "<pre>" . print_r($dados, true) . "</pre>";
        
        if (!empty($dados)) {
            echo "<br>✓ " . count($dados) . " registros extraídos do HTML<br>";
            
            // Testar inserção no banco
            $envaseModel = new Envase();
            $inseridos = 0;
            $erros = [];
            
            foreach ($dados as $registro) {
                try {
                    if ($envaseModel->create($registro)) {
                        $inseridos++;
                    } else {
                        $erros[] = "Erro ao inserir: " . json_encode($registro);
                    }
                } catch (Exception $e) {
                    $erros[] = "Exceção ao inserir: " . $e->getMessage();
                }
            }
            
            echo "✓ {$inseridos} registros inseridos no banco<br>";
            if (!empty($erros)) {
                echo "❌ Erros encontrados:<br>";
                foreach ($erros as $erro) {
                    echo "  - {$erro}<br>";
                }
            }
            
        } else {
            echo "❌ Nenhum dado extraído do HTML<br>";
        }
        
    } else {
        echo "❌ Método lerHTML não encontrado<br>";
    }
    
    // Contar registros depois
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total_depois = $stmt->fetch()['total'];
    echo "<br>Total de registros DEPOIS do teste: {$total_depois}<br>";
    echo "Registros adicionados: " . ($total_depois - $total_antes) . "<br><br>";
    
    // Mostrar últimos registros
    echo "<h3>Últimos 10 registros no banco:</h3>";
    $stmt = $pdo->query("SELECT * FROM envase_data ORDER BY id DESC LIMIT 10");
    $registros = $stmt->fetchAll();
    
    if (count($registros) > 0) {
        echo "<table border='1' style='border-collapse: collapse; font-size: 12px;'>";
        echo "<tr><th>ID</th><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Ano</th><th>Mês</th><th>Dia</th><th>Quantidade</th><th>Arquivo</th></tr>";
        
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
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Limpar arquivo temporário
    if (file_exists($temp_file)) {
        unlink($temp_file);
        echo "<br>✓ Arquivo temporário removido<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br><a href='public/index.php'>← Voltar ao Dashboard</a>";
?>
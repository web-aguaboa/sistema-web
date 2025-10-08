<?php
require_once 'config/init.php';

echo "<h2>Teste de Inserção Manual na Tabela envase_data</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    echo "✓ Conexão com banco de dados estabelecida<br><br>";
    
    // Verificar se há dados existentes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total_antes = $stmt->fetch()['total'];
    echo "Total de registros ANTES da inserção: {$total_antes}<br><br>";
    
    // Inserir um registro de teste
    $sql = "INSERT INTO envase_data (empresa, cidade, produto, ano, mes, dia, quantidade, arquivo_origem) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $dados_teste = [
        'TESTE HTML EMPRESA',
        'TESTE CIDADE',
        'AGUA NATURAL 500ML',
        2024,
        1,
        15,
        500,
        'teste_manual.html'
    ];
    
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute($dados_teste);
    
    if ($resultado) {
        echo "✓ Registro inserido com sucesso!<br>";
        echo "Dados inseridos: " . implode(' | ', $dados_teste) . "<br><br>";
    } else {
        echo "✗ Erro ao inserir registro<br><br>";
    }
    
    // Verificar se os dados foram inseridos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM envase_data");
    $total_depois = $stmt->fetch()['total'];
    echo "Total de registros DEPOIS da inserção: {$total_depois}<br><br>";
    
    // Mostrar os últimos 5 registros
    echo "<h3>Últimos 5 registros na tabela:</h3>";
    $stmt = $pdo->query("SELECT * FROM envase_data ORDER BY id DESC LIMIT 5");
    $registros = $stmt->fetchAll();
    
    if (count($registros) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Ano</th><th>Mês</th><th>Dia</th><th>Quantidade</th><th>Arquivo</th><th>Data Upload</th></tr>";
        
        foreach ($registros as $reg) {
            echo "<tr>";
            echo "<td>{$reg['id']}</td>";
            echo "<td>{$reg['empresa']}</td>";
            echo "<td>{$reg['cidade']}</td>";
            echo "<td>{$reg['produto']}</td>";
            echo "<td>{$reg['ano']}</td>";
            echo "<td>{$reg['mes']}</td>";
            echo "<td>{$reg['dia']}</td>";
            echo "<td>{$reg['quantidade']}</td>";
            echo "<td>{$reg['arquivo_origem']}</td>";
            echo "<td>{$reg['data_upload']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum registro encontrado.";
    }
    
    echo "<br><br>";
    
    // Testar a query que o dashboard usa para buscar dados
    echo "<h3>Teste da Query do Dashboard:</h3>";
    $sql_dashboard = "SELECT 
        empresa,
        cidade,
        produto,
        SUM(quantidade) as total_quantidade,
        COUNT(*) as total_registros,
        MIN(data_upload) as primeiro_upload,
        MAX(data_upload) as ultimo_upload
    FROM envase_data 
    GROUP BY empresa, cidade, produto 
    ORDER BY total_quantidade DESC 
    LIMIT 10";
    
    $stmt = $pdo->query($sql_dashboard);
    $dados_dashboard = $stmt->fetchAll();
    
    if (count($dados_dashboard) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Empresa</th><th>Cidade</th><th>Produto</th><th>Total Quantidade</th><th>Total Registros</th><th>Primeiro Upload</th><th>Último Upload</th></tr>";
        
        foreach ($dados_dashboard as $dado) {
            echo "<tr>";
            echo "<td>{$dado['empresa']}</td>";
            echo "<td>{$dado['cidade']}</td>";
            echo "<td>{$dado['produto']}</td>";
            echo "<td>{$dado['total_quantidade']}</td>";
            echo "<td>{$dado['total_registros']}</td>";
            echo "<td>{$dado['primeiro_upload']}</td>";
            echo "<td>{$dado['ultimo_upload']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum dado encontrado pela query do dashboard.";
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage();
}

echo "<br><br><a href='public/index.php'>← Voltar ao Dashboard</a>";
?>
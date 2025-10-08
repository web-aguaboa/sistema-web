<?php
// Verificação específica da estrutura do banco
require_once 'config/init.php';

echo "<h2>🔍 Verificação do Banco de Dados</h2>";

try {
    // 1. Verificar se a tabela existe
    echo "<h3>1. Verificando tabela actions:</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'actions'");
    $table = $stmt->fetch();
    
    if ($table) {
        echo "✅ Tabela 'actions' existe<br>";
    } else {
        echo "❌ Tabela 'actions' não existe!<br>";
        exit;
    }
    
    // 2. Verificar estrutura da tabela
    echo "<h3>2. Estrutura da tabela:</h3>";
    $stmt = $pdo->query("DESCRIBE actions");
    $columns = $stmt->fetchAll();
    
    $hasCol = false;
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Key']}<br>";
        if ($column['Field'] === 'prazo_conclusao') {
            $hasCol = true;
        }
    }
    
    if ($hasCol) {
        echo "<br>✅ Coluna 'prazo_conclusao' existe<br>";
    } else {
        echo "<br>❌ Coluna 'prazo_conclusao' não existe! Criando...<br>";
        
        // Criar a coluna
        $sql = "ALTER TABLE actions ADD COLUMN prazo_conclusao DATE AFTER data_acao";
        $pdo->exec($sql);
        echo "✅ Coluna criada com sucesso!<br>";
    }
    
    // 3. Verificar dados existentes
    echo "<h3>3. Dados na tabela:</h3>";
    $stmt = $pdo->query("SELECT id, descricao, data_acao, prazo_conclusao FROM actions LIMIT 5");
    $actions = $stmt->fetchAll();
    
    if ($actions) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Descrição</th><th>Data Ação</th><th>Prazo Conclusão</th></tr>";
        foreach ($actions as $action) {
            echo "<tr>";
            echo "<td>{$action['id']}</td>";
            echo "<td>" . substr($action['descricao'], 0, 30) . "...</td>";
            echo "<td>{$action['data_acao']}</td>";
            echo "<td>{$action['prazo_conclusao']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhuma ação encontrada<br>";
    }
    
    // 4. Teste de inserção simples
    echo "<h3>4. Teste de inserção:</h3>";
    try {
        $sql = "INSERT INTO actions (client_id, descricao, data_acao, prazo_conclusao) VALUES (1, 'Teste prazo', '2025-10-02', '2025-12-01')";
        $pdo->exec($sql);
        echo "✅ Inserção teste funcionou!<br>";
        
        // Buscar o registro inserido
        $stmt = $pdo->query("SELECT * FROM actions WHERE descricao = 'Teste prazo'");
        $testAction = $stmt->fetch();
        if ($testAction) {
            echo "<pre>";
            print_r($testAction);
            echo "</pre>";
            
            // Deletar o teste
            $pdo->exec("DELETE FROM actions WHERE descricao = 'Teste prazo'");
            echo "Registro de teste removido<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erro na inserção: " . $e->getMessage() . "<br>";
    }
    
    // 5. Teste de update
    echo "<h3>5. Teste de update:</h3>";
    try {
        $sql = "UPDATE actions SET prazo_conclusao = '2025-11-01' WHERE id = 1";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute();
        
        if ($resultado) {
            echo "✅ Update teste funcionou!<br>";
            echo "Linhas afetadas: " . $stmt->rowCount() . "<br>";
        } else {
            echo "❌ Update falhou<br>";
            print_r($stmt->errorInfo());
        }
    } catch (Exception $e) {
        echo "❌ Erro no update: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h3>✅ Verificação concluída</h3>";
?>
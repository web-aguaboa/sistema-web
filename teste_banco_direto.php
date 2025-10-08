<?php
/**
 * Teste direto de inserção no banco
 * Sistema Aguaboa - Gestão Comercial
 */

require_once 'config/init.php';

echo "<h1>🔍 Teste Direto - Banco de Dados</h1>";

try {
    // Conectar ao banco
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>📊 Status do Banco</h2>";
    
    // Verificar tabela envase_data
    $stmt = $db->query("SHOW TABLES LIKE 'envase_data'");
    $tabelaExiste = $stmt->fetchColumn();
    
    if ($tabelaExiste) {
        echo "<p>✅ Tabela 'envase_data' existe</p>";
        
        // Verificar estrutura da tabela
        $stmt = $db->query("DESCRIBE envase_data");
        $colunas = $stmt->fetchAll();
        
        echo "<h3>📋 Estrutura da Tabela:</h3>";
        echo "<table style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Campo</th>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Tipo</th>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Null</th>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Chave</th>";
        echo "</tr>";
        
        foreach ($colunas as $coluna) {
            echo "<tr>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$coluna['Field']}</td>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$coluna['Type']}</td>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$coluna['Null']}</td>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$coluna['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar registros
        $stmt = $db->query("SELECT COUNT(*) FROM envase_data");
        $totalRegistros = $stmt->fetchColumn();
        
        echo "<p><strong>Total de registros:</strong> " . number_format($totalRegistros) . "</p>";
        
        if ($totalRegistros > 0) {
            // Mostrar últimos registros
            echo "<h3>📋 Últimos 10 Registros:</h3>";
            
            $stmt = $db->query("SELECT * FROM envase_data ORDER BY id DESC LIMIT 10");
            $registros = $stmt->fetchAll();
            
            echo "<table style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>ID</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Empresa</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Produto</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Data</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Quantidade</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Arquivo</th>";
            echo "</tr>";
            
            foreach ($registros as $reg) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$reg['id']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($reg['empresa']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($reg['produto']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>{$reg['ano']}-{$reg['mes']}-{$reg['dia']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . number_format($reg['quantidade']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($reg['arquivo_origem'] ?? '') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p>❌ Tabela 'envase_data' NÃO existe!</p>";
        
        // Tentar criar a tabela
        echo "<h3>🛠️ Tentando criar tabela...</h3>";
        
        $sqlCreateTable = "
        CREATE TABLE IF NOT EXISTS envase_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            empresa VARCHAR(255) NOT NULL,
            cidade VARCHAR(255),
            produto VARCHAR(255) NOT NULL,
            ano INT NOT NULL,
            mes INT NOT NULL,
            dia INT NOT NULL,
            quantidade INT NOT NULL,
            arquivo_origem VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($db->exec($sqlCreateTable)) {
            echo "<p>✅ Tabela criada com sucesso!</p>";
        } else {
            echo "<p>❌ Erro ao criar tabela</p>";
        }
    }
    
    echo "<h2>🧪 Teste de Inserção Direta</h2>";
    
    // Testar inserção direta
    $testData = [
        'empresa' => 'TESTE DIRETO BANCO',
        'cidade' => 'SAO PAULO',
        'produto' => 'AGUABOA 20L TESTE',
        'ano' => 2025,
        'mes' => 10,
        'dia' => 1,
        'quantidade' => 999,
        'arquivo_origem' => 'teste_direto.html'
    ];
    
    $sql = "INSERT INTO envase_data (empresa, cidade, produto, ano, mes, dia, quantidade, arquivo_origem) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $resultado = $stmt->execute([
        $testData['empresa'],
        $testData['cidade'],
        $testData['produto'],
        $testData['ano'],
        $testData['mes'],
        $testData['dia'],
        $testData['quantidade'],
        $testData['arquivo_origem']
    ]);
    
    if ($resultado) {
        $novoId = $db->lastInsertId();
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724;'>";
        echo "<h4>✅ Inserção direta bem-sucedida!</h4>";
        echo "<p><strong>ID gerado:</strong> $novoId</p>";
        echo "<p><strong>Dados inseridos:</strong> " . json_encode($testData) . "</p>";
        echo "</div>";
        
        // Verificar se foi inserido
        $stmt = $db->prepare("SELECT * FROM envase_data WHERE id = ?");
        $stmt->execute([$novoId]);
        $registroInserido = $stmt->fetch();
        
        if ($registroInserido) {
            echo "<h4>📊 Registro recuperado do banco:</h4>";
            echo "<pre>" . print_r($registroInserido, true) . "</pre>";
        }
        
        // Limpar teste
        $stmt = $db->prepare("DELETE FROM envase_data WHERE id = ?");
        $stmt->execute([$novoId]);
        echo "<p><em>Registro de teste removido.</em></p>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>";
        echo "<h4>❌ Erro na inserção direta!</h4>";
        echo "<p>Erro: " . print_r($stmt->errorInfo(), true) . "</p>";
        echo "</div>";
    }
    
    echo "<h2>🔍 Teste do Modelo Envase</h2>";
    
    // Testar modelo Envase
    $envaseModel = new Envase();
    $stats = $envaseModel->getStats();
    
    echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 6px;'>";
    echo "<h4>📈 Estatísticas do Modelo:</h4>";
    echo "<ul>";
    echo "<li><strong>Total registros:</strong> " . number_format($stats['total_registros']) . "</li>";
    echo "<li><strong>Empresas únicas:</strong> " . number_format($stats['empresas_unicas']) . "</li>";
    echo "<li><strong>Produtos únicos:</strong> " . number_format($stats['produtos_unicos']) . "</li>";
    echo "<li><strong>Total quantidade:</strong> " . number_format($stats['total_quantidade']) . "</li>";
    echo "<li><strong>Anos disponíveis:</strong> " . implode(', ', $stats['anos_disponiveis']) . "</li>";
    echo "</ul>";
    echo "</div>";
    
    // Testar método create do modelo
    echo "<h3>🧪 Teste do método create()</h3>";
    
    $testModelData = [
        'empresa' => 'TESTE MODELO ENVASE',
        'cidade' => 'RIO DE JANEIRO',
        'produto' => 'AGUABOA 10L TESTE',
        'ano' => 2025,
        'mes' => 10,
        'dia' => 2,
        'quantidade' => 777,
        'arquivo_origem' => 'teste_modelo.html'
    ];
    
    if ($envaseModel->create($testModelData)) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724;'>";
        echo "<h4>✅ Método create() funcionou!</h4>";
        echo "<p>Dados: " . json_encode($testModelData) . "</p>";
        echo "</div>";
        
        // Verificar se apareceu nas estatísticas
        $statsApos = $envaseModel->getStats();
        $diferenca = $statsApos['total_registros'] - $stats['total_registros'];
        
        echo "<p><strong>Diferença nos registros:</strong> +$diferenca</p>";
        
        // Limpar teste
        $stmt = $db->prepare("DELETE FROM envase_data WHERE empresa = ?");
        $stmt->execute(['TESTE MODELO ENVASE']);
        echo "<p><em>Registro de teste do modelo removido.</em></p>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>";
        echo "<h4>❌ Método create() falhou!</h4>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>";
    echo "<h3>❌ Erro no teste</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='" . BASE_URL . "/envase'>← Voltar para Envase</a></p>";
?>
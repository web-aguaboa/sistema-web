<?php
require_once 'config/init.php';

echo "<h2>🔧 Verificação e Criação de Tabelas</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    
    echo "<h3>1. Verificando tabelas existentes</h3>";
    
    // Verificar tabelas
    $tabelas_necessarias = [
        'users' => 'Usuários do sistema',
        'clients' => 'Clientes',
        'envase_data' => 'Dados de envase',
        'upload_history' => 'Histórico de uploads',
        'activity_log' => 'Log de atividades',
        'actions' => 'Ações dos clientes',
        'client_infos' => 'Informações adicionais dos clientes'
    ];
    
    $tabelas_existentes = [];
    $tabelas_faltando = [];
    
    foreach ($tabelas_necessarias as $tabela => $descricao) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tabela]);
        
        if ($stmt->rowCount() > 0) {
            $tabelas_existentes[] = $tabela;
            echo "✅ Tabela '{$tabela}' existe ({$descricao})<br>";
        } else {
            $tabelas_faltando[] = $tabela;
            echo "❌ Tabela '{$tabela}' NÃO existe ({$descricao})<br>";
        }
    }
    
    echo "<br>";
    
    if (count($tabelas_faltando) > 0) {
        echo "<h3>2. Criando tabelas faltando</h3>";
        
        // SQL para criar tabelas faltando
        $sqls_criacao = [
            'client_infos' => "
                CREATE TABLE client_infos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    client_id INT NOT NULL,
                    info_json TEXT,
                    data_info DATE,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
                )
            "
        ];
        
        foreach ($tabelas_faltando as $tabela) {
            if (isset($sqls_criacao[$tabela])) {
                try {
                    $pdo->query($sqls_criacao[$tabela]);
                    echo "✅ Tabela '{$tabela}' criada com sucesso<br>";
                } catch (Exception $e) {
                    echo "❌ Erro ao criar tabela '{$tabela}': " . $e->getMessage() . "<br>";
                }
            } else {
                echo "⚠️ SQL de criação não definido para tabela '{$tabela}'<br>";
            }
        }
        
    } else {
        echo "<h3>✅ Todas as tabelas necessárias existem!</h3>";
    }
    
    echo "<br><h3>3. Testando função 'Limpar Tudo'</h3>";
    
    // Testar se agora funciona
    echo "<p>Agora você pode testar a função 'Limpar Todos os Dados' no dashboard.</p>";
    
    // Mostrar contagem atual
    echo "<h4>Dados atuais no sistema:</h4>";
    foreach ($tabelas_existentes as $tabela) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM `{$tabela}`");
            $total = $stmt->fetch()['total'];
            echo "• {$tabela}: " . number_format($total) . " registros<br>";
        } catch (Exception $e) {
            echo "• {$tabela}: Erro ao contar - " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ ERRO</h3>";
    echo "Erro: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<br><br>";
echo "<div style='text-align: center;'>";
echo "<a href='public/index.php?path=/envase' style='background: #007fa3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔄 TESTAR FUNÇÃO LIMPAR TUDO</a>";
echo "</div>";
?>
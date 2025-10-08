<?php
/**
 * Teste da funcionalidade de clientes com dados de envase
 */

require_once 'config/init.php';

try {
    echo "<h2>🧪 Teste: Clientes com Dados de Envase</h2>";
    
    $clientModel = new Client();
    
    // Buscar clientes com estatísticas
    $clientes = $clientModel->findAllWithEnvaseStats();
    
    echo "<h3>📋 Clientes encontrados: " . count($clientes) . "</h3>";
    
    if (!empty($clientes)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
        echo "<thead style='background: #007fa3; color: white;'>";
        echo "<tr>";
        echo "<th>Cliente</th>";
        echo "<th>Empresa</th>";
        echo "<th>Cidade</th>";
        echo "<th>Total Envases</th>";
        echo "<th>Produtos</th>";
        echo "<th>Último Envase</th>";
        echo "<th>Produto Principal</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($clientes as $cliente) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($cliente['cliente']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($cliente['empresa'] ?: '-') . "</td>";
            echo "<td>" . htmlspecialchars($cliente['cidade'] ?: '-') . "</td>";
            
            if ($cliente['total_envases'] > 0) {
                echo "<td style='text-align: right; font-weight: bold; color: #007fa3;'>";
                echo number_format($cliente['total_envases']);
                echo "</td>";
                
                echo "<td style='text-align: center;'>" . $cliente['produtos_diferentes'] . "</td>";
                
                echo "<td style='text-align: center;'>";
                if ($cliente['ultimo_envase']) {
                    echo date('d/m/Y', strtotime($cliente['ultimo_envase']));
                } else {
                    echo '-';
                }
                echo "</td>";
                
                echo "<td style='font-weight: bold; color: #28a745;'>";
                echo htmlspecialchars($cliente['produto_principal'] ?: '-');
                echo "</td>";
            } else {
                echo "<td style='color: #999; text-align: center;' colspan='4'>Sem dados de envase</td>";
            }
            
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>❌ Nenhum cliente encontrado</p>";
    }
    
    // Verificar dados no banco
    echo "<hr>";
    echo "<h3>🔍 Verificação dos Dados no Banco:</h3>";
    
    $db = Database::getInstance()->getConnection();
    
    // Verificar clientes
    $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
    $totalClientes = $stmt->fetch()['total'];
    echo "<p>👥 <strong>Total de clientes:</strong> $totalClientes</p>";
    
    // Verificar envases
    $stmt = $db->query("SELECT COUNT(*) as total FROM envase_data");
    $totalEnvases = $stmt->fetch()['total'];
    echo "<p>📦 <strong>Total de registros de envase:</strong> $totalEnvases</p>";
    
    // Verificar empresas em envase vs clientes
    $stmt = $db->query("SELECT DISTINCT empresa FROM envase_data ORDER BY empresa");
    $empresasEnvase = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt = $db->query("SELECT DISTINCT cliente FROM clients ORDER BY cliente");
    $empresasClientes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h4>📊 Comparação Empresas:</h4>";
    echo "<p><strong>Empresas em envase_data:</strong> " . implode(', ', $empresasEnvase) . "</p>";
    echo "<p><strong>Empresas em clients:</strong> " . implode(', ', $empresasClientes) . "</p>";
    
    // Teste do JOIN manual
    echo "<h4>🔗 Teste do JOIN Manual:</h4>";
    $sql = "SELECT c.cliente, c.empresa, c.cidade, 
                   COALESCE(e.total_quantidade, 0) as total_envases,
                   e.produtos_diferentes,
                   e.ultimo_envase,
                   e.produto_principal
            FROM clients c
            LEFT JOIN (
                SELECT empresa,
                       SUM(quantidade) as total_quantidade,
                       COUNT(DISTINCT produto) as produtos_diferentes,
                       MAX(CONCAT(ano, '-', LPAD(mes, 2, '0'), '-', LPAD(dia, 2, '0'))) as ultimo_envase,
                       (
                           SELECT produto 
                           FROM envase_data e2 
                           WHERE e2.empresa = e1.empresa 
                           GROUP BY produto 
                           ORDER BY SUM(quantidade) DESC 
                           LIMIT 1
                       ) as produto_principal
                FROM envase_data e1
                GROUP BY empresa
            ) e ON (c.cliente = e.empresa OR c.empresa = e.empresa)
            ORDER BY c.cliente";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll();
    
    echo "<p><strong>Resultados do JOIN:</strong> " . count($resultados) . " registros</p>";
    
    if ($resultados) {
        echo "<table border='1' style='border-collapse: collapse; font-size: 0.9rem;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>Cliente</th><th>Empresa</th><th>Total</th><th>Produtos</th><th>Principal</th>";
        echo "</tr>";
        
        foreach ($resultados as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['cliente']) . "</td>";
            echo "<td>" . htmlspecialchars($row['empresa'] ?: '-') . "</td>";
            echo "<td>" . number_format($row['total_envases']) . "</td>";
            echo "<td>" . ($row['produtos_diferentes'] ?: '0') . "</td>";
            echo "<td>" . htmlspecialchars($row['produto_principal'] ?: '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p>✅ <strong>Teste concluído!</strong></p>";
    echo "<p><a href='public/' style='background: #007fa3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👉 Ir para o Sistema</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
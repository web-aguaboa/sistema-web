<?php
/**
 * Teste avançado da funcionalidade de unificação de clientes
 * Sistema Aguaboa - Gestão Comercial
 */

require_once 'config/init.php';

echo "<h1>🔗 Teste Avançado - Unificação de Clientes</h1>";

try {
    $clientModel = new Client();
    
    echo "<h2>📊 Análise de Clientes Duplicados</h2>";
    
    $duplicates = $clientModel->findDuplicateClients();
    
    if (empty($duplicates)) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724;'>";
        echo "<h3>✅ Nenhum cliente duplicado encontrado!</h3>";
        echo "<p>Todos os clientes do sistema já estão únicos.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 6px; color: #856404; margin-bottom: 1rem;'>";
        echo "<h3>⚠️ Encontrados " . count($duplicates) . " grupo(s) de clientes duplicados</h3>";
        echo "</div>";
        
        $totalDuplicates = 0;
        foreach ($duplicates as $index => $group) {
            $totalDuplicates += count($group) - 1; // Subtrair 1 porque um permanecerá
            
            echo "<div style='border: 2px solid #ffc107; padding: 1rem; margin: 1rem 0; border-radius: 8px; background: #fffdf0;'>";
            echo "<h4 style='color: #856404; margin-bottom: 1rem;'>📋 Grupo " . ($index + 1) . " (" . count($group) . " clientes)</h4>";
            
            echo "<table style='width: 100%; border-collapse: collapse;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Status</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Nome do Cliente</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Empresa</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Cidade</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Total Envases</th>";
            echo "</tr>";
            
            foreach ($group as $clientIndex => $client) {
                $bgColor = $clientIndex === 0 ? '#e7f3ff' : '#ffffff';
                $status = $clientIndex === 0 ? '👑 PERMANECERÁ' : '🔗 SERÁ UNIFICADO';
                $statusColor = $clientIndex === 0 ? '#007fa3' : '#dc3545';
                
                echo "<tr style='background: $bgColor;'>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem; color: $statusColor; font-weight: bold;'>$status</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($client['cliente']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($client['empresa'] ?: '-') . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($client['cidade'] ?: '-') . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . number_format($client['total_envases']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        }
        
        echo "<div style='background: #e7f3ff; padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; border-left: 4px solid #007fa3;'>";
        echo "<h3 style='color: #0c5460; margin-bottom: 1rem;'>📈 Resumo da Unificação</h3>";
        echo "<ul style='margin: 0; color: #0c5460;'>";
        echo "<li><strong>Grupos detectados:</strong> " . count($duplicates) . "</li>";
        echo "<li><strong>Total de clientes duplicados:</strong> $totalDuplicates</li>";
        echo "<li><strong>Clientes que permanecerão:</strong> " . count($duplicates) . "</li>";
        echo "<li><strong>Economia de registros:</strong> $totalDuplicates clientes serão removidos</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    echo "<h2>🛠️ Ferramentas de Teste</h2>";
    
    // Testar algoritmo de similaridade
    echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 6px; margin: 1rem 0;'>";
    echo "<h4>🔍 Teste de Similaridade</h4>";
    echo "<p>Exemplos de nomes que o algoritmo detectaria como similares:</p>";
    
    $testCases = [
        ['AGUA PURA TATUI', 'AGUA PURA TATUI 2'],
        ['ENTREPOSTO CENTRO (CLIENTE A)', 'ENTREPOSTO CENTRO (CLIENTE B)'],
        ['EMBU DISTR (ADEGA TROPICAL)', 'EMBU DISTR (ADEGA TROPICAL)'],
        ['DISTRIBUIDORA SAO PAULO', 'DISTRIBUIDORA SAO PAULO LTDA'],
        ['COMERCIAL ABC', 'COMERCIAL ABC 1']
    ];
    
    echo "<table style='width: 100%; border-collapse: collapse; margin-top: 0.5rem;'>";
    echo "<tr style='background: #e9ecef;'>";
    echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Cliente 1</th>";
    echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Cliente 2</th>";
    echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Similaridade</th>";
    echo "</tr>";
    
    foreach ($testCases as $case) {
        // Simular clientes para teste
        $client1 = ['cliente' => $case[0], 'empresa' => null];
        $client2 = ['cliente' => $case[1], 'empresa' => null];
        
        // Usar reflexão para acessar método privado
        $reflection = new ReflectionClass($clientModel);
        $method = $reflection->getMethod('clientsAreSimilar');
        $method->setAccessible(true);
        $similar = $method->invoke($clientModel, $client1, $client2);
        
        $result = $similar ? '✅ SIMILARES' : '❌ DIFERENTES';
        $color = $similar ? '#28a745' : '#dc3545';
        
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($case[0]) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($case[1]) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 0.5rem; color: $color; font-weight: bold;'>$result</td>";
        echo "</tr>";
    }\n    echo \"</table>\";\n    echo \"</div>\";\n    \n    echo \"<h2>🚀 Ações Disponíveis</h2>\";\n    echo \"<div style='display: flex; gap: 1rem; flex-wrap: wrap;'>\";\n    \n    echo \"<a href='\" . BASE_URL . \"/crm/unify-clients' class='btn' style='background: #ffc107; color: #000; padding: 1rem; text-decoration: none; border-radius: 6px; font-weight: bold;'>\";\n    echo \"🔗 Unificação Manual<br><small>Revisar e escolher quais unificar</small>\";\n    echo \"</a>\";\n    \n    if (!empty($duplicates)) {\n        echo \"<a href='\" . BASE_URL . \"/crm/unify-clients?auto=1' class='btn' style='background: #28a745; color: white; padding: 1rem; text-decoration: none; border-radius: 6px; font-weight: bold;' onclick='return confirm(\\\"⚠️ Unificar todos automaticamente?\\\\n\\\\nEsta ação não pode ser desfeita!\\\");'>\";\n        echo \"🤖 Unificação Automática<br><small>Unificar todos os grupos</small>\";\n        echo \"</a>\";\n    }\n    \n    echo \"<a href='\" . BASE_URL . \"/crm' class='btn' style='background: #007fa3; color: white; padding: 1rem; text-decoration: none; border-radius: 6px; font-weight: bold;'>\";\n    echo \"👥 Voltar ao CRM<br><small>Lista de clientes</small>\";\n    echo \"</a>\";\n    \n    echo \"</div>\";\n    \n} catch (Exception $e) {\n    echo \"<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24;'>\";\n    echo \"<h3>❌ Erro no teste</h3>\";\n    echo \"<p>\" . htmlspecialchars($e->getMessage()) . \"</p>\";\n    echo \"<pre>\" . htmlspecialchars($e->getTraceAsString()) . \"</pre>\";\n    echo \"</div>\";\n}\n\necho \"<hr style='margin: 2rem 0;'>\";\necho \"<p style='text-align: center; color: #666;'>\";\necho \"<small>Sistema Aguaboa - Gestão Comercial | Teste de Unificação de Clientes</small>\";\necho \"</p>\";\n?>
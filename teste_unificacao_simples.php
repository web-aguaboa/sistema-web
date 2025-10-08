<?php
/**
 * Versão simplificada para teste da unificação
 * Sistema Aguaboa - Gestão Comercial
 */

require_once 'config/init.php';

echo "<h1>🔗 Teste Simples - Unificação de Clientes</h1>";

try {
    $clientModel = new Client();
    
    // Buscar clientes duplicados usando versão simplificada
    echo "<h2>📋 Buscando clientes duplicados...</h2>";
    
    // Obter todos os clientes
    $sql = "SELECT c.*, COALESCE(e.total_envases, 0) as total_envases
            FROM clients c
            LEFT JOIN (
                SELECT empresa, SUM(quantidade) as total_envases
                FROM envase_data
                GROUP BY empresa
            ) e ON (c.cliente = e.empresa OR c.empresa = e.empresa)
            ORDER BY c.cliente";
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $allClients = $stmt->fetchAll();
    
    echo "<p>Total de clientes encontrados: <strong>" . count($allClients) . "</strong></p>";
    
    // Agrupar clientes similares manualmente
    $groups = [];
    $processed = [];
    
    foreach ($allClients as $client1) {
        if (in_array($client1['id'], $processed)) {
            continue;
        }
        
        $group = [$client1];
        $processed[] = $client1['id'];
        
        $name1 = strtolower(trim($client1['cliente']));
        
        foreach ($allClients as $client2) {
            if ($client1['id'] >= $client2['id'] || in_array($client2['id'], $processed)) {
                continue;
            }
            
            $name2 = strtolower(trim($client2['cliente']));
            
            // Verificações simples de similaridade
            $similar = false;
            
            // 1. Nomes idênticos
            if ($name1 === $name2) {
                $similar = true;
            }
            
            // 2. EMBU DISTR - mesmo conteúdo entre parênteses
            if (strpos($name1, 'embu distr') !== false && strpos($name2, 'embu distr') !== false) {
                preg_match('/embu distr \\(([^)]+)\\)/', $name1, $matches1);
                preg_match('/embu distr \\(([^)]+)\\)/', $name2, $matches2);
                
                if (isset($matches1[1]) && isset($matches2[1])) {
                    if (strtolower(trim($matches1[1])) === strtolower(trim($matches2[1]))) {
                        $similar = true;
                    }
                }
            }
            
            // 3. ENTREPOSTO - mesmo padrão antes dos parênteses
            if (strpos($name1, 'entreposto') !== false && strpos($name2, 'entreposto') !== false) {
                $pattern1 = explode('(', $name1)[0];
                $pattern2 = explode('(', $name2)[0];
                if (trim($pattern1) === trim($pattern2)) {
                    $similar = true;
                }
            }
            
            // 4. Nome base similar (removendo números do final)
            $base1 = preg_replace('/\\s*[0-9]+\\s*$/', '', $name1);
            $base2 = preg_replace('/\\s*[0-9]+\\s*$/', '', $name2);
            
            if (strlen($base1) > 8 && strlen($base2) > 8 && $base1 === $base2) {
                $similar = true;
            }
            
            // 5. Empresas idênticas
            if (!empty($client1['empresa']) && !empty($client2['empresa'])) {
                if (strtolower(trim($client1['empresa'])) === strtolower(trim($client2['empresa']))) {
                    $similar = true;
                }
            }
            
            if ($similar) {
                $group[] = $client2;
                $processed[] = $client2['id'];
            }
        }
        
        // Só adicionar grupos com mais de 1 cliente
        if (count($group) > 1) {
            // Ordenar por maior volume de envase
            usort($group, function($a, $b) {
                return $b['total_envases'] <=> $a['total_envases'];
            });
            $groups[] = $group;
        }
    }
    
    if (empty($groups)) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724; margin: 1rem 0;'>";
        echo "<h3>✅ Nenhum cliente duplicado encontrado!</h3>";
        echo "<p>Todos os clientes do sistema já estão únicos.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 6px; color: #856404; margin: 1rem 0;'>";
        echo "<h3>⚠️ Encontrados " . count($groups) . " grupo(s) de clientes duplicados</h3>";
        echo "</div>";
        
        foreach ($groups as $index => $group) {
            echo "<div style='border: 2px solid #ffc107; padding: 1rem; margin: 1rem 0; border-radius: 8px; background: #fffdf0;'>";
            echo "<h4 style='color: #856404;'>📋 Grupo " . ($index + 1) . " (" . count($group) . " clientes)</h4>";
            
            echo "<table style='width: 100%; border-collapse: collapse; margin-top: 0.5rem;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Status</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Nome</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Empresa</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Cidade</th>";
            echo "<th style='border: 1px solid #ddd; padding: 0.5rem; text-align: left;'>Envases</th>";
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
        
        echo "<div style='text-align: center; margin: 2rem 0;'>";
        echo "<a href='" . BASE_URL . "/crm/unify-clients?auto=1' ";
        echo "onclick='return confirm(\"⚠️ Unificar todos os grupos automaticamente?\\n\\nEsta ação não pode ser desfeita!\");' ";
        echo "class='btn' style='background: #28a745; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 1.1rem;'>";
        echo "🤖 Unificar Todos Automaticamente";
        echo "</a>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24; margin: 1rem 0;'>";
    echo "<h3>❌ Erro</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre style='background: #fff; padding: 0.5rem; border-radius: 4px; font-size: 0.8rem;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='text-align: center; margin: 1rem 0;'>";
echo "<a href='" . BASE_URL . "/crm' class='btn' style='background: #007fa3; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 6px; margin: 0.5rem;'>← Voltar ao CRM</a>";
echo "<a href='" . BASE_URL . "/crm/unify-clients' class='btn' style='background: #ffc107; color: #000; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 6px; margin: 0.5rem;'>🔗 Unificação Manual</a>";
echo "</div>";
?>
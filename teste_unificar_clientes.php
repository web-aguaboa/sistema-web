<?php
/**
 * Teste da funcionalidade de unificação de clientes
 * Sistema Aguaboa - Gestão Comercial
 */

// Este arquivo é para testar a funcionalidade de unificação
// Não deve ser executado em produção

require_once 'config/init.php';

echo "<h1>🔗 Teste - Funcionalidade de Unificação de Clientes</h1>";

// Simular criação de clientes duplicados para teste
try {
    $clientModel = new Client();
    
    echo "<h2>📝 Verificando clientes duplicados...</h2>";
    
    $duplicates = $clientModel->findDuplicateClients();
    
    if (empty($duplicates)) {
        echo "<p style='color: green;'>✅ Nenhum cliente duplicado encontrado no momento.</p>";
        
        echo "<h3>💡 Para testar a funcionalidade, você pode:</h3>";
        echo "<ul>";
        echo "<li>Cadastrar clientes com nomes similares através do CRM</li>";
        echo "<li>Exemplos de nomes que seriam detectados como duplicados:</li>";
        echo "<ul>";
        echo "<li>'AGUA PURA TATUI' e 'AGUA PURA TATUI 2'</li>";
        echo "<li>'ENTREPOSTO CENTRO (CLIENTE A)' e 'ENTREPOSTO CENTRO (CLIENTE B)'</li>";
        echo "<li>'DISTRIBUIDORA SAO PAULO' e 'DISTRIBUIDORA SAO PAULO LTDA'</li>";
        echo "</ul>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Encontrados " . count($duplicates) . " grupo(s) de clientes duplicados:</p>";
        
        foreach ($duplicates as $index => $group) {
            echo "<div style='border: 1px solid #ccc; padding: 1rem; margin: 0.5rem 0; border-radius: 6px;'>";
            echo "<h4>Grupo " . ($index + 1) . ":</h4>";
            echo "<ul>";
            foreach ($group as $client) {
                echo "<li><strong>" . htmlspecialchars($client['cliente']) . "</strong> - " . number_format($client['total_envases']) . " envases</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    }
    
    echo "<h2>🚀 Como usar a funcionalidade:</h2>";
    echo "<ol>";
    echo "<li>Acesse: <a href='" . BASE_URL . "/crm'>" . BASE_URL . "/crm</a></li>";
    echo "<li>Clique no botão <strong>'🔗 Unificar Clientes'</strong> (disponível apenas para admins)</li>";
    echo "<li>Revise os grupos de clientes duplicados detectados</li>";
    echo "<li>Selecione quais clientes devem ser unificados</li>";
    echo "<li>Confirme a unificação</li>";
    echo "</ol>";
    
    echo "<h2>🔍 Algoritmo de Detecção:</h2>";
    echo "<p>O sistema identifica clientes duplicados através de:</p>";
    echo "<ul>";
    echo "<li><strong>Nomes idênticos:</strong> Clientes com exatamente o mesmo nome</li>";
    echo "<li><strong>Nomes similares:</strong> Removendo números e espaços extras do final</li>";
    echo "<li><strong>Padrões específicos:</strong> Como 'ENTREPOSTO (...)' ou 'AGUA PURA X'</li>";
    echo "<li><strong>Empresas similares:</strong> Comparação de nomes de empresa</li>";
    echo "</ul>";
    
    echo "<h2>💾 O que acontece na unificação:</h2>";
    echo "<ul>";
    echo "<li>O cliente com maior volume de envase se torna o principal</li>";
    echo "<li>Todos os dados de envase são transferidos para o cliente principal</li>";
    echo "<li>Todas as ações/histórico são transferidas</li>";
    echo "<li>Os clientes duplicados são removidos</li>";
    echo "<li>A ação é registrada no log de atividades</li>";
    echo "</ul>";
    
    echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 6px; margin: 1rem 0;'>";
    echo "<strong>⚠️ IMPORTANTE:</strong> A unificação é uma ação irreversível. ";
    echo "Certifique-se de revisar cuidadosamente antes de confirmar.";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='" . BASE_URL . "/crm'>← Voltar para o CRM</a></p>";
?>
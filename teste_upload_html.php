<?php
/**
 * Teste de upload e processamento de arquivos HTML
 * Sistema Aguaboa - Gestão Comercial
 */

require_once 'config/init.php';

echo "<h1>🌐 Teste - Upload de Arquivos HTML</h1>";

try {
    $envaseController = new EnvaseController();
    
    echo "<h2>📋 Suporte a Arquivos HTML</h2>";
    
    echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 6px; margin: 1rem 0;'>";
    echo "<h3>✅ Formatos HTML Suportados:</h3>";
    echo "<ul>";
    echo "<li><strong>.html</strong> - Páginas HTML padrão</li>";
    echo "<li><strong>.htm</strong> - Páginas HTML (extensão alternativa)</li>";
    echo "<li><strong>Tabelas HTML</strong> - Exportadas do Excel/Edge</li>";
    echo "<li><strong>HTML mal formado</strong> - O sistema tenta processar mesmo HTML com problemas</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>🔄 Como o Sistema Processa HTML:</h2>";
    
    echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 6px; margin: 1rem 0;'>";
    echo "<h4>📊 Algoritmo de Processamento:</h4>";
    echo "<ol>";
    echo "<li><strong>Detecção de Tabelas:</strong> Busca elementos &lt;table&gt; no HTML</li>";
    echo "<li><strong>Extração de Dados:</strong> Processa &lt;tr&gt; e &lt;td&gt;/&lt;th&gt;</li>";
    echo "<li><strong>Contexto Hierárquico:</strong> Mantém estado como nos CSVs OLAP</li>";
    echo "<li><strong>Validação:</strong> Verifica campos obrigatórios (empresa, ano, mês, dia, quantidade)</li>";
    echo "<li><strong>Fallback:</strong> Se não encontra tabelas, processa como texto</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h2>🧪 Teste de Arquivo HTML de Exemplo</h2>";
    
    // Criar arquivo HTML de exemplo
    $htmlExemplo = '<!DOCTYPE html>
<html>
<head>
    <title>Relatório OLAP - Sistema Aguaboa</title>
</head>
<body>
    <h1>Relatório de Envase</h1>
    <table border="1">
        <tr>
            <th>Empresa</th>
            <th>Cidade</th>
            <th>Produto</th>
            <th>Ano</th>
            <th>Mês</th>
            <th>Dia</th>
            <th>Quantidade</th>
        </tr>
        <tr>
            <td>AGUA E CIA DISTRIBUIDORA</td>
            <td>GUARUJA</td>
            <td>AGUABOA PREMIUM 20 LTS</td>
            <td>2025</td>
            <td>10</td>
            <td>15</td>
            <td>1500</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>AGUABOA 20 LTS</td>
            <td></td>
            <td></td>
            <td>16</td>
            <td>800</td>
        </tr>
        <tr>
            <td>DISTRIBUIDORA TAGUAI</td>
            <td>TAGUAI</td>
            <td>AGUABOA 10 LTS</td>
            <td>2025</td>
            <td>10</td>
            <td>17</td>
            <td>600</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>18</td>
            <td>400</td>
        </tr>
        <tr>
            <td colspan="7"><strong>Total Geral: 3300</strong></td>
        </tr>
    </table>
</body>
</html>';
    
    // Salvar arquivo temporário
    $arquivoTeste = UPLOAD_DIR . 'teste_relatorio.html';
    
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    file_put_contents($arquivoTeste, $htmlExemplo);
    
    echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 6px; margin: 1rem 0;'>";
    echo "<h4>📄 Arquivo HTML de Teste Criado:</h4>";
    echo "<p><strong>Local:</strong> " . htmlspecialchars($arquivoTeste) . "</p>";
    echo "<p><strong>Tamanho:</strong> " . number_format(filesize($arquivoTeste)) . " bytes</p>";
    echo "</div>";
    
    // Tentar processar o arquivo
    echo "<h3>🔄 Processando Arquivo HTML...</h3>";
    
    // Usar reflexão para acessar método privado lerHTML
    $reflection = new ReflectionClass($envaseController);
    $method = $reflection->getMethod('lerHTML');
    $method->setAccessible(true);
    
    $dadosProcessados = $method->invoke($envaseController, $arquivoTeste);
    
    if (!empty($dadosProcessados)) {
        echo "<div style='background: #d4edda; padding: 1rem; border-radius: 6px; color: #155724; margin: 1rem 0;'>";
        echo "<h4>✅ Sucesso! Dados Extraídos do HTML:</h4>";
        echo "<p><strong>Total de registros:</strong> " . count($dadosProcessados) . "</p>";
        echo "</div>";
        
        echo "<table style='width: 100%; border-collapse: collapse; margin: 1rem 0;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Empresa</th>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Cidade</th>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Produto</th>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Data</th>";
        echo "<th style='border: 1px solid #ddd; padding: 0.5rem;'>Quantidade</th>";
        echo "</tr>";
        
        foreach (array_slice($dadosProcessados, 0, 10) as $registro) {
            echo "<tr>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($registro['empresa']) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($registro['cidade']) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . htmlspecialchars($registro['produto']) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . sprintf('%04d-%02d-%02d', $registro['ano'], $registro['mes'], $registro['dia']) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 0.5rem;'>" . number_format($registro['quantidade']) . "</td>";
            echo "</tr>";
        }
        
        if (count($dadosProcessados) > 10) {
            echo "<tr><td colspan='5' style='text-align: center; padding: 0.5rem; font-style: italic;'>... e mais " . (count($dadosProcessados) - 10) . " registros</td></tr>";
        }
        
        echo "</table>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24; margin: 1rem 0;'>";
        echo "<h4>❌ Nenhum dado foi extraído do HTML</h4>";
        echo "<p>O arquivo pode não ter o formato esperado ou pode estar vazio.</p>";
        echo "</div>";
    }
    
    echo "<h2>📝 Como Exportar HTML do Excel/Edge:</h2>";
    
    echo "<div style='background: #e8f5e8; padding: 1rem; border-radius: 6px; margin: 1rem 0;'>";
    echo "<h4>💡 Instruções para Gerar HTML:</h4>";
    echo "<ol>";
    echo "<li><strong>No Excel:</strong>";
    echo "<ul>";
    echo "<li>Abra sua planilha</li>";
    echo "<li>Vá em 'Arquivo' → 'Salvar Como'</li>";
    echo "<li>Escolha 'Página da Web (*.html; *.htm)'</li>";
    echo "<li>Salve o arquivo</li>";
    echo "</ul></li>";
    echo "<li><strong>No Microsoft Edge:</strong>";
    echo "<ul>";
    echo "<li>Abra a planilha online</li>";
    echo "<li>Pressione Ctrl+S ou clique em 'Salvar página'</li>";
    echo "<li>Escolha 'Página da Web, completa'</li>";
    echo "<li>Salve com extensão .html</li>";
    echo "</ul></li>";
    echo "<li><strong>No Google Sheets:</strong>";
    echo "<ul>";
    echo "<li>Abra a planilha</li>";
    echo "<li>Vá em 'Arquivo' → 'Download' → 'Página da Web (.html, compactado)'</li>";
    echo "</ul></li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h2>🚀 Testar Upload Real:</h2>";
    
    echo "<div style='text-align: center; margin: 2rem 0;'>";
    echo "<a href='" . BASE_URL . "/envase' class='btn' style='background: #007fa3; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 6px; font-size: 1.1rem;'>";
    echo "📤 Ir para Upload de Planilhas";
    echo "</a>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 6px; border-left: 4px solid #ffc107; margin: 1rem 0;'>";
    echo "<h4>⚠️ Dicas Importantes:</h4>";
    echo "<ul>";
    echo "<li>O sistema mantém <strong>contexto hierárquico</strong> - valores vazios usam o valor da linha anterior</li>";
    echo "<li>Linhas com 'Total' são automaticamente <strong>ignoradas</strong></li>";
    echo "<li>Se não conseguir processar como tabela, tentará extrair dados como <strong>texto simples</strong></li>";
    echo "<li>Funciona melhor com <strong>HTML bem estruturado</strong> com tabelas</li>";
    echo "<li>Suporte a <strong>HTML mal formado</strong> com fallback para texto</li>";
    echo "</ul>";
    echo "</div>";
    
    // Limpar arquivo de teste
    if (file_exists($arquivoTeste)) {
        unlink($arquivoTeste);
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 6px; color: #721c24; margin: 1rem 0;'>";
    echo "<h3>❌ Erro no teste</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='" . BASE_URL . "/envase'>← Voltar para Envase</a></p>";
?>
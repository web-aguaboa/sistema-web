<?php
echo "=== TESTE PYTHON/PANDAS DIRETO ===" . PHP_EOL;

// Simular o método lerComPythonPandas
function testarPythonPandas($arquivo) {
    $dados = [];
    
    try {
        // Criar script Python temporário
        $pythonScript = sys_get_temp_dir() . '/' . uniqid() . '.py';
        $outputJson = sys_get_temp_dir() . '/' . uniqid() . '.json';
        
        $scriptContent = "
import pandas as pd
import json
import sys

def process_hierarchical_excel(filepath):
    try:
        # Ler arquivo Excel
        if filepath.endswith('.xls'):
            df = pd.read_excel(filepath, engine='xlrd')
        else:
            df = pd.read_excel(filepath)
        
        print(f'Arquivo lido com sucesso: {len(df)} linhas')
        
        normalized_data = []
        
        # Variáveis para contexto hierárquico
        current_empresa = None
        current_cidade = None
        current_produto = None
        current_ano = None
        current_mes = None
        
        for idx, row in df.iterrows():
            try:
                # Extrair valores
                col1 = str(row.iloc[0]).strip() if pd.notna(row.iloc[0]) else None
                col2 = str(row.iloc[1]).strip() if pd.notna(row.iloc[1]) else None
                col3 = str(row.iloc[2]).strip() if pd.notna(row.iloc[2]) else None
                col4 = str(row.iloc[3]).strip() if pd.notna(row.iloc[3]) else None
                col5 = str(row.iloc[4]).strip() if pd.notna(row.iloc[4]) else None
                col6 = row.iloc[5] if pd.notna(row.iloc[5]) else None
                col7 = row.iloc[6] if pd.notna(row.iloc[6]) else None
                
                # Pular linhas de total
                if any('total' in str(val).lower() for val in [col1, col2, col3, col4, col5] if val):
                    continue
                
                # Atualizar contexto
                if col1 and col1 != 'nan' and len(col1) > 3:
                    current_empresa = col1.strip()
                
                if col2 and col2 != 'nan' and len(col2) > 3:
                    current_cidade = col2.strip()
                
                if col3 and col3 != 'nan' and len(col3) > 3:
                    current_produto = col3.strip()
                
                if col4 and col4 != 'nan' and col4.isdigit():
                    current_ano = int(col4)
                
                if col5 and col5 != 'nan' and col5.isdigit() and len(col5) <= 2:
                    current_mes = int(col5)
                
                # Criar registro se temos dia e quantidade
                if col6 is not None and col7 is not None:
                    try:
                        dia = int(float(col6))
                        quantidade = int(float(col7))
                        
                        if (current_empresa and current_cidade and current_produto and 
                            current_ano and current_mes and dia > 0 and quantidade > 0):
                            
                            if (current_ano >= 2020 and current_ano <= 2030 and
                                current_mes >= 1 and current_mes <= 12 and
                                dia >= 1 and dia <= 31):
                                
                                normalized_data.append({
                                    'empresa': current_empresa,
                                    'cidade': current_cidade,
                                    'produto': current_produto,
                                    'ano': current_ano,
                                    'mes': current_mes,
                                    'dia': dia,
                                    'quantidade': quantidade
                                })
                    except (ValueError, TypeError):
                        continue
                        
            except Exception:
                continue
        
        print(f'Processados {len(normalized_data)} registros válidos')
        return normalized_data
        
    except Exception as e:
        print(f'ERRO: {str(e)}')
        return []

# Processar arquivo
try:
    filepath = sys.argv[1]
    output_file = sys.argv[2]
    
    data = process_hierarchical_excel(filepath)
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)
        
    print(f'SUCCESS: {len(data)} registros processados')
    
except Exception as e:
    print(f'ERROR: {str(e)}')
";
        
        file_put_contents($pythonScript, $scriptContent);
        
        // Executar Python
        $comando = "python \"$pythonScript\" \"$arquivo\" \"$outputJson\" 2>&1";
        echo "Executando: $comando" . PHP_EOL;
        
        exec($comando, $output, $return_code);
        
        echo "Saída do Python:" . PHP_EOL;
        foreach ($output as $linha) {
            echo "  $linha" . PHP_EOL;
        }
        echo "Código de retorno: $return_code" . PHP_EOL;
        
        if ($return_code === 0 && file_exists($outputJson)) {
            $jsonContent = file_get_contents($outputJson);
            $dados = json_decode($jsonContent, true);
            
            // Limpar arquivos temporários
            unlink($pythonScript);
            unlink($outputJson);
            
            if (is_array($dados) && !empty($dados)) {
                echo "JSON lido com sucesso: " . count($dados) . " registros" . PHP_EOL;
                return $dados;
            }
        }
        
        // Limpar arquivos em caso de erro
        if (file_exists($pythonScript)) unlink($pythonScript);
        if (file_exists($outputJson)) unlink($outputJson);
        
    } catch (Exception $e) {
        echo "Erro ao usar Python: " . $e->getMessage() . PHP_EOL;
    }
    
    return [];
}

// Testar com arquivo disponível
$arquivos = glob('public/uploads/*');
echo "Arquivos disponíveis:" . PHP_EOL;
foreach ($arquivos as $arquivo) {
    echo "- " . basename($arquivo) . PHP_EOL;
}

if (!empty($arquivos)) {
    $arquivoTeste = $arquivos[0];
    echo PHP_EOL . "Testando com: $arquivoTeste" . PHP_EOL;
    
    $inicio = microtime(true);
    $dados = testarPythonPandas($arquivoTeste);
    $fim = microtime(true);
    
    echo "Tempo: " . round($fim - $inicio, 2) . "s" . PHP_EOL;
    echo "Registros: " . count($dados) . PHP_EOL;
    
    if (!empty($dados) && count($dados) > 0) {
        echo "Primeiro registro:" . PHP_EOL;
        print_r($dados[0]);
    }
} else {
    echo "Nenhum arquivo encontrado em public/uploads/" . PHP_EOL;
}

echo "=== FIM ===" . PHP_EOL;
?>
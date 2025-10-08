<?php
echo "=== TESTE PROCESSAMENTO HIERÁRQUICO CSV ===" . PHP_EOL;

function testarCSVHierarquico($arquivo) {
    $dados = [];
    
    if (($handle = fopen($arquivo, "r")) !== FALSE) {
        $linha_numero = 0;
        
        // Variáveis para contexto hierárquico (IGUAL AO PYTHON)
        $current_empresa = null;
        $current_cidade = null;
        $current_produto = null;
        $current_ano = null;
        $current_mes = null;
        
        while (($linha = fgetcsv($handle, 10000, ";")) !== FALSE) {
            $linha_numero++;
            
            echo "Linha $linha_numero: " . implode(" | ", $linha) . PHP_EOL;
            
            // Pular cabeçalho
            if ($linha_numero === 1 && strpos(implode('', $linha), 'Qtde') !== false) {
                echo "  -> Cabeçalho ignorado" . PHP_EOL;
                continue;
            }
            
            if (count($linha) < 7) {
                echo "  -> Poucas colunas (" . count($linha) . ")" . PHP_EOL;
                continue;
            }
            
            try {
                // Extrair valores (IGUAL AO PYTHON)
                $col1 = !empty(trim($linha[0])) ? trim($linha[0]) : null;
                $col2 = !empty(trim($linha[1])) ? trim($linha[1]) : null;
                $col3 = !empty(trim($linha[2])) ? trim($linha[2]) : null;
                $col4 = !empty(trim($linha[3])) ? trim($linha[3]) : null;
                $col5 = !empty(trim($linha[4])) ? trim($linha[4]) : null;
                $col6 = !empty(trim($linha[5])) ? trim($linha[5]) : null;
                $col7 = !empty(trim($linha[6])) ? trim($linha[6]) : null;
                
                // Pular linhas de total
                $linha_texto = implode('', $linha);
                if (stripos($linha_texto, 'total') !== false) {
                    echo "  -> Total ignorado" . PHP_EOL;
                    continue;
                }
                
                // Atualizar contexto hierárquico (IGUAL AO PYTHON)
                if ($col1 && strlen($col1) > 3) {
                    $current_empresa = $col1;
                    echo "  -> Nova empresa: $current_empresa" . PHP_EOL;
                }
                
                if ($col2 && strlen($col2) > 3) {
                    $current_cidade = $col2;
                    echo "  -> Nova cidade: $current_cidade" . PHP_EOL;
                }
                
                if ($col3 && strlen($col3) > 3) {
                    $current_produto = $col3;
                    echo "  -> Novo produto: $current_produto" . PHP_EOL;
                }
                
                if ($col4 && is_numeric($col4)) {
                    $current_ano = (int)$col4;
                    echo "  -> Novo ano: $current_ano" . PHP_EOL;
                }
                
                if ($col5 && is_numeric($col5) && strlen($col5) <= 2) {
                    $current_mes = (int)$col5;
                    echo "  -> Novo mês: $current_mes" . PHP_EOL;
                }
                
                // Criar registro se temos dia e quantidade (IGUAL AO PYTHON)
                if ($col6 !== null && $col7 !== null) {
                    $dia = (int)floatval($col6);
                    $quantidade = (int)floatval(str_replace(',', '.', $col7));
                    
                    echo "  -> Dia: $dia, Quantidade: $quantidade" . PHP_EOL;
                    
                    if ($current_empresa && $current_cidade && $current_produto && 
                        $current_ano && $current_mes && $dia > 0 && $quantidade > 0) {
                        
                        // Validar ranges (IGUAL AO PYTHON)
                        if ($current_ano >= 2020 && $current_ano <= 2030 &&
                            $current_mes >= 1 && $current_mes <= 12 &&
                            $dia >= 1 && $dia <= 31) {
                            
                            $registro = [
                                'empresa' => $current_empresa,
                                'cidade' => $current_cidade,
                                'produto' => $current_produto,
                                'ano' => $current_ano,
                                'mes' => $current_mes,
                                'dia' => $dia,
                                'quantidade' => $quantidade,
                                'arquivo_origem' => basename($arquivo)
                            ];
                            
                            $dados[] = $registro;
                            echo "  -> REGISTRO CRIADO: " . json_encode($registro) . PHP_EOL;
                        } else {
                            echo "  -> Fora do range de datas" . PHP_EOL;
                        }
                    } else {
                        echo "  -> Contexto incompleto: emp=" . ($current_empresa ? 'OK' : 'NULL') . 
                             ", cid=" . ($current_cidade ? 'OK' : 'NULL') . 
                             ", prod=" . ($current_produto ? 'OK' : 'NULL') . 
                             ", ano=$current_ano, mes=$current_mes" . PHP_EOL;
                    }
                }
                
            } catch (Exception $e) {
                echo "  -> Erro: " . $e->getMessage() . PHP_EOL;
                continue;
            }
            
            // Parar após 20 linhas para não encher tela
            if ($linha_numero >= 20) {
                echo "... parando após 20 linhas ..." . PHP_EOL;
                break;
            }
        }
        
        fclose($handle);
    }
    
    return $dados;
}

// Testar com CSV disponível
$arquivo = 'public/uploads/RelatorioOLAP.csv';

if (file_exists($arquivo)) {
    echo "Testando processamento hierárquico com: $arquivo" . PHP_EOL . PHP_EOL;
    
    $dados = testarCSVHierarquico($arquivo);
    
    echo PHP_EOL . "=== RESULTADO ===" . PHP_EOL;
    echo "Registros processados: " . count($dados) . PHP_EOL;
    
    if (!empty($dados)) {
        echo "Primeiros registros:" . PHP_EOL;
        for ($i = 0; $i < min(3, count($dados)); $i++) {
            print_r($dados[$i]);
        }
    }
} else {
    echo "Arquivo não encontrado: $arquivo" . PHP_EOL;
}

echo "=== FIM ===" . PHP_EOL;
?>
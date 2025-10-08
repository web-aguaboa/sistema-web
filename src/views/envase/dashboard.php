<?php ob_start(); ?>

<div class="stats">
    <div class="stat-card">
        <div class="stat-number"><?= number_format($stats['total_registros']) ?></div>
        <div class="stat-label">Total de Registros</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= number_format($stats['empresas_unicas']) ?></div>
        <div class="stat-label">Empresas Cadastradas</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= number_format($stats['produtos_unicos']) ?></div>
        <div class="stat-label">Produtos Diferentes</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= number_format($stats['total_quantidade']) ?></div>
        <div class="stat-label">Total Envases</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        📊 Dashboard de Envase
        <div style="float: right;">
            <button class="btn btn-success" onclick="openModal('uploadModal')" style="margin-right: 10px;">
                📤 Upload de Planilha
            </button>
            <button class="btn btn-danger" onclick="confirmarExclusao()" title="Excluir todos os dados de envase" style="margin-right: 10px;">
                🗑️ Limpar Dados
            </button>
            <button class="btn btn-danger" onclick="confirmarLimpezaCompleta()" title="Excluir TODOS os dados (envase + clientes)" style="background-color: #dc3545; border-color: #dc3545;">
                🔥 Limpar Todos os Dados
            </button>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <h3>📈 Anos Disponíveis</h3>
                <?php if (!empty($stats['anos_disponiveis'])): ?>
                    <ul>
                        <?php foreach ($stats['anos_disponiveis'] as $ano): ?>
                            <li><?= $ano ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Nenhum dado de envase cadastrado ainda.</p>
                <?php endif; ?>
            </div>
            
            <div>
                <h3>📋 Últimos Uploads</h3>
                <?php if (!empty($uploads)): ?>
                    <table style="width: 100%; font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th>Arquivo</th>
                                <th>Status</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($uploads, 0, 5) as $upload): ?>
                            <tr>
                                <td><?= htmlspecialchars($upload['nome_arquivo']) ?></td>
                                <td>
                                    <?php if ($upload['status'] === 'concluido'): ?>
                                        <span style="color: green;">✅ Concluído</span>
                                    <?php elseif ($upload['status'] === 'erro'): ?>
                                        <span style="color: red;">❌ Erro</span>
                                    <?php else: ?>
                                        <span style="color: orange;">⏳ Processando</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($upload['data_upload'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nenhum upload realizado ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        📁 Funcionalidades Disponíveis
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div style="text-align: center; padding: 1rem; border: 2px solid #007fa3; border-radius: 8px;">
                <h4>� Upload de Planilhas</h4>
                <p>Faça upload de arquivos Excel (.xls, .xlsx), CSV ou HTML (.html, .htm) com dados de envase</p>
                <button class="btn btn-primary" onclick="openModal('uploadModal')">Fazer Upload</button>
            </div>
            
            <div style="text-align: center; padding: 1rem; border: 2px solid #28a745; border-radius: 8px;">
                <h4>📊 Gráficos</h4>
                <p>Visualize estatísticas e gráficos dos dados de envase</p>
                <a href="<?= BASE_URL ?>/envase/charts" class="btn btn-success">Ver Gráficos</a>
            </div>
            
            <div style="text-align: center; padding: 1rem; border: 2px solid #17a2b8; border-radius: 8px;">
                <h4>👥 Por Cliente</h4>
                <p>Consulte dados de envase específicos por cliente</p>
                <a href="<?= BASE_URL ?>/crm" class="btn" style="background: #17a2b8; color: white;">Ver Clientes</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('uploadModal')">&times;</span>
        <h3>📤 Upload de Planilha de Envase</h3>
        
        <div style="background: #e7f3ff; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <h4>📋 Formato da Planilha:</h4>
            <p>O sistema aceita arquivos CSV, Excel ou HTML com os seguintes formatos:</p>
            <ul>
                <li><strong>Formato OLAP:</strong> Arquivo com separador ponto-e-vírgula (;)</li>
                <li><strong>Colunas:</strong> Empresa;Cidade;Produto;Ano;Mês;Dia;Quantidade</li>
                <li><strong>Exemplo:</strong> AGUABOA DISTRIBUIDORA;SÃO PAULO;ÁGUA 20L;2025;10;15;1500</li>
                <li><strong>HTML:</strong> Tabelas exportadas do Excel ou Edge como HTML</li>
                <li><strong>Observações:</strong> Linhas de "Total" são automaticamente ignoradas</li>
            </ul>
            <p><strong>📁 Formatos aceitos:</strong> .csv (ponto-e-vírgula), .xls, .xlsx, .html, .htm</p>
        </div>
        
        <form id="uploadForm" method="POST" action="<?= BASE_URL ?>/envase/upload" enctype="multipart/form-data">
            <div class="form-group">
                <label>Selecionar Arquivo:</label>
                <input type="file" name="arquivo" accept=".xls,.xlsx,.csv,.html,.htm" required>
                <small style="color: #666;">Formatos aceitos: .xls, .xlsx, .csv, .html, .htm (máximo <?= number_format(MAX_UPLOAD_SIZE / 1024 / 1024) ?>MB)</small>
            </div>
            
            <!-- Barra de Progresso -->
            <div id="progressContainer" style="display: none; margin: 20px 0;">
                <div style="background: #f0f0f0; border-radius: 10px; overflow: hidden; position: relative; height: 30px; border: 2px solid #007fa3;">
                    <div id="progressBar" style="
                        width: 0%; 
                        height: 100%; 
                        background: linear-gradient(90deg, #007fa3 0%, #28a745 100%); 
                        transition: width 0.3s ease; 
                        position: relative;
                    "></div>
                    <div id="progressText" style="
                        position: absolute; 
                        top: 50%; 
                        left: 50%; 
                        transform: translate(-50%, -50%); 
                        font-weight: bold; 
                        color: #333; 
                        font-size: 14px;
                        z-index: 10;
                    ">0%</div>
                </div>
                <div id="progressStatus" style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;">
                    Preparando upload...
                </div>
            </div>
            
            <div id="uploadButtons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">📤 Fazer Upload</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('uploadModal')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmarExclusao() {
    const totalRegistros = <?= $stats['total_registros'] ?? 0 ?>;
    
    if (totalRegistros === 0) {
        alert('Não há dados para excluir.');
        return;
    }
    
    const confirmacao = confirm(
        `⚠️ ATENÇÃO!\n\n` +
        `Esta ação irá excluir TODOS os ${totalRegistros.toLocaleString()} registros de envase do sistema.\n\n` +
        `Esta operação NÃO PODE ser desfeita!\n\n` +
        `Deseja realmente continuar?`
    );
    
    if (confirmacao) {
        const confirmacaoFinal = confirm(
            '🚨 CONFIRMAÇÃO FINAL\n\n' +
            'Tem certeza absoluta que deseja excluir todos os dados?\n\n' +
            'Digite SIM para confirmar:'
        );
        
        if (confirmacaoFinal) {
            // Mostrar loading
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '⏳ Excluindo...';
            
            // Fazer requisição
            fetch('<?= BASE_URL ?>/envase/limpar-dados', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({action: 'limpar_todos'})
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert(`✅ Sucesso!\n\n${data.registros_excluidos} registros foram excluídos.`);
                    location.reload();
                } else {
                    alert(`❌ Erro: ${data.erro}`);
                    btn.disabled = false;
                    btn.innerHTML = '🗑️ Limpar Dados';
                }
            })
            .catch(error => {
                alert('❌ Erro na requisição: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = '🗑️ Limpar Dados';
            });
        }
    }
}

function confirmarLimpezaCompleta() {
    const totalRegistros = <?= $stats['total_registros'] ?? 0 ?>;
    
    const confirmacao = confirm(
        `🔥 ATENÇÃO - LIMPEZA COMPLETA!\n\n` +
        `Esta ação irá excluir:\n` +
        `• TODOS os ${totalRegistros.toLocaleString()} registros de envase\n` +
        `• TODOS os clientes do CRM\n` +
        `• TODAS as ações e informações dos clientes\n` +
        `• TODO o histórico de uploads\n\n` +
        `⚠️ Esta operação NÃO PODE ser desfeita!\n\n` +
        `Deseja realmente ZERAR TODO O SISTEMA?`
    );
    
    if (confirmacao) {
        const senha = prompt(
            '🚨 CONFIRMAÇÃO DE SEGURANÇA\n\n' +
            'Para confirmar a limpeza completa,\n' +
            'digite: ZERAR TUDO'
        );
        
        if (senha === 'ZERAR TUDO') {
            // Mostrar loading
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '🔥 Zerando Sistema...';
            
            // Fazer requisição
            fetch('<?= BASE_URL ?>/envase/limpar-tudo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({action: 'limpar_sistema_completo'})
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    let detalhes = `🎉 SISTEMA ZERADO COM SUCESSO!\n\n`;
                    detalhes += `• ${data.registros_envase || 0} registros de envase excluídos\n`;
                    detalhes += `• ${data.clientes_excluidos || 0} clientes excluídos\n`;
                    detalhes += `• ${data.acoes_excluidas || 0} ações excluídas\n`;
                    if (data.infos_excluidas) {
                        detalhes += `• ${data.infos_excluidas} informações excluídas\n`;
                    }
                    detalhes += `• ${data.uploads_excluidos || 0} uploads excluídos\n`;
                    detalhes += `• ${data.arquivos_removidos || 0} arquivos removidos\n\n`;
                    detalhes += `O sistema está completamente limpo!`;
                    
                    alert(detalhes);
                    location.reload();
                } else {
                    alert(`❌ Erro na limpeza: ${data.erro}`);
                    btn.disabled = false;
                    btn.innerHTML = '🔥 Limpar Todos os Dados';
                }
            })
            .catch(error => {
                alert('❌ Erro na requisição: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = '🔥 Limpar Todos os Dados';
            });
        } else if (senha !== null) {
            alert('❌ Senha incorreta. Operação cancelada.');
        }
    }
}

// Controle da barra de progresso do upload
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressStatus = document.getElementById('progressStatus');
    const uploadButtons = document.getElementById('uploadButtons');
    
    // Mostrar barra de progresso
    progressContainer.style.display = 'block';
    uploadButtons.style.display = 'none';
    
    // Simular progresso de upload
    let progress = 0;
    const progressInterval = setInterval(() => {
        if (progress < 90) {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
            
            // Atualizar status baseado no progresso
            if (progress < 20) {
                progressStatus.textContent = '📤 Enviando arquivo...';
            } else if (progress < 50) {
                progressStatus.textContent = '🔍 Analisando conteúdo...';
            } else if (progress < 80) {
                progressStatus.textContent = '⚙️ Processando dados...';
            } else {
                progressStatus.textContent = '💾 Salvando no banco de dados...';
            }
        }
    }, 150);
    
    // Fazer upload real
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Completar progresso
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        progressText.textContent = '100%';
        progressStatus.textContent = '✅ Upload concluído!';
        
        // Aguardar um pouco antes de processar resposta
        setTimeout(() => {
            if (response.ok) {
                // Redirecionar para a página de envase para ver os resultados
                window.location.href = '<?= BASE_URL ?>/envase';
            } else {
                throw new Error('Erro no servidor');
            }
        }, 1000);
    })
    .catch(error => {
        clearInterval(progressInterval);
        progressBar.style.background = '#dc3545';
        progressText.textContent = 'Erro!';
        progressStatus.textContent = '❌ Erro no upload: ' + error.message;
        
        // Restaurar botões após erro
        setTimeout(() => {
            progressContainer.style.display = 'none';
            uploadButtons.style.display = 'block';
        }, 3000);
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include '../src/views/layouts/main.php'; ?>
<?php ob_start(); ?>

<style>
.envase-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 0;
    margin: 0;
}

.envase-header {
    background: #17a2b8;
    color: white;
    padding: 1rem 2rem;
    margin-bottom: 2rem;
}

.envase-header h1 {
    margin: 0 0 1rem 0;
    font-size: 1.5rem;
    font-weight: normal;
}

.header-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.header-btn {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.9rem;
}

.header-btn:hover {
    background: rgba(255,255,255,0.3);
    color: white;
    text-decoration: none;
}

.client-info {
    background: white;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    border-left: 4px solid #17a2b8;
}

.client-info h2 {
    color: #17a2b8;
    margin: 0 0 0.5rem 0;
    font-size: 1.4rem;
    font-weight: bold;
}

.client-details {
    color: #666;
    font-size: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #17a2b8;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 1rem;
}

.section-card {
    background: white;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    border-radius: 8px 8px 0 0;
}

.section-title {
    color: #17a2b8;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-content {
    padding: 1.5rem;
}

.produtos-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.produto-tag {
    background: #17a2b8;
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    cursor: pointer;
}

.produto-tag:hover {
    background: #138496;
}

.table-container {
    overflow-x: auto;
}

/* Estilos personalizados para barras de rolagem */
.table-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.data-table th {
    background: #f8f9fa;
    padding: 0.8rem;
    text-align: left;
    border-bottom: 2px solid #e9ecef;
    font-weight: 600;
    color: #495057;
}

.data-table td {
    padding: 0.8rem;
    border-bottom: 1px solid #e9ecef;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.evolution-positive {
    color: #28a745;
    font-weight: bold;
}

.evolution-negative {
    color: #dc3545;
    font-weight: bold;
}

.manage-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.manage-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.manage-btn {
    padding: 0.6rem 1.2rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    border: none;
    cursor: pointer;
}

.btn-edit {
    background: #28a745;
    color: white;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.manage-description {
    font-size: 0.9rem;
    color: #666;
}
</style>

<div class="envase-container">
    <!-- Header -->
    <div class="envase-header">
        <h1>📊 Dados de Envase - <?= strtoupper(htmlspecialchars($client['cliente'])) ?></h1>
        <div class="header-buttons">
            <a href="<?= BASE_URL ?>/crm/client/<?= $client['id'] ?>" class="header-btn">
                ← Voltar ao Cliente
            </a>
            <a href="<?= BASE_URL ?>/envase" class="header-btn">
                📊 Dashboard Envase
            </a>
            <a href="<?= BASE_URL ?>/envase/charts?client_id=<?= $client['id'] ?>" class="header-btn">
                📈 Evolução
            </a>
            <a href="<?= BASE_URL ?>/crm" class="header-btn">
                👥 CRM Principal
            </a>
        </div>
    </div>

    <div style="max-width: 1400px; margin: 0 auto; padding: 0 2rem;">
        <!-- Informações do Cliente -->
        <div class="client-info">
            <h2><?= strtoupper(htmlspecialchars($client['empresa'])) ?></h2>
            <div class="client-details">
                <strong>Cliente:</strong> <?= htmlspecialchars($client['cliente']) ?> | 
                <strong>Cidade:</strong> <?= htmlspecialchars($client['cidade'] ?? 'SANTANA DO ITARARE') ?>, 
                <?= htmlspecialchars($client['estado'] ?? 'SP') ?>
            </div>
            
            <!-- Debug Info (remover em produção) -->
            <?php if (empty($summary['yearly_evolution']) && empty($summary['monthly_evolution']) && empty($summary['ultimos_registros'])): ?>
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 1rem; margin-top: 1rem; border-radius: 4px;">
                <strong>⚠️ Debug:</strong> Nenhum dado encontrado. 
                Verificando empresa: "<?= htmlspecialchars($client['empresa']) ?>"
                <br>
                <small>Total de registros no summary: <?= $summary['total_registros'] ?></small>
            </div>
            <?php endif; ?>
        </div>

        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($summary['total_quantidade']) ?></div>
                <div class="stat-label">Total de Envases</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($summary['quantidade_media_mes'], 0) ?></div>
                <div class="stat-label">Média por Mês</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($summary['frequencia_media'], 1) ?></div>
                <div class="stat-label">Dias por Mês</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($summary['produtos_unicos']) ?></div>
                <div class="stat-label">Produtos Diferentes</div>
            </div>
        </div>

        <!-- Produtos -->
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">📦 Produtos</h3>
            </div>
            <div class="section-content">
                <div class="produtos-list">
                    <?php foreach ($summary['produtos_unicos'] as $produto => $quantidade): ?>
                        <span class="produto-tag" title="<?= number_format($quantidade) ?> envases">
                            <?= htmlspecialchars($produto) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Evolução Anual -->
        <?php if (!empty($summary['yearly_evolution'])): ?>
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">📈 Evolução Anual</h3>
            </div>
            <div class="section-content">
                <div class="table-container" style="max-height: 400px; overflow-y: auto;">
                    <table class="data-table">
                        <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                            <tr>
                                <th>Ano</th>
                                <th style="text-align: right;">Total</th>
                                <th style="text-align: center;">Meses</th>
                                <th style="text-align: right;">Média</th>
                                <th style="text-align: right;">Evolução</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($summary['yearly_evolution'] as $year_data): ?>
                            <tr>
                                <td><strong><?= $year_data['ano'] ?></strong></td>
                                <td style="text-align: right;">
                                    <?= number_format($year_data['quantidade']) ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php 
                                    $meses = count(array_unique(array_map(function($r) { 
                                        return $r['mes']; 
                                    }, $year_data['registros'])));
                                    echo $meses . ' meses';
                                    ?>
                                </td>
                                <td style="text-align: right;">
                                    <?= $meses > 0 ? number_format($year_data['quantidade'] / $meses, 0) : '0' ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($year_data['evolucao'] !== null): ?>
                                        <span class="<?= $year_data['evolucao'] >= 0 ? 'evolution-positive' : 'evolution-negative' ?>">
                                            <?= $year_data['evolucao'] >= 0 ? '▲' : '▼' ?>
                                            <?= number_format(abs($year_data['evolucao']), 1) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #6c757d;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">📈 Evolução Anual</h3>
            </div>
            <div class="section-content" style="text-align: center; padding: 3rem; color: #666;">
                📊 Nenhum dado de evolução anual encontrado para este cliente.
                <br><br>
                <small>Verifique se há dados de envase carregados no sistema.</small>
            </div>
        </div>
        <?php endif; ?>

        <!-- Evolução Mensal -->
        <?php if (!empty($summary['monthly_evolution'])): ?>
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">📅 Evolução Mensal</h3>
            </div>
            <div class="section-content">
                <div class="table-container" style="max-height: 500px; overflow-y: auto;">
                    <table class="data-table">
                        <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                            <tr>
                                <th>Mês</th>
                                <th style="text-align: right;">Quantidade</th>
                                <th style="text-align: right;">Média</th>
                                <th style="text-align: center;">Dias</th>
                                <th style="text-align: right;">Evolução</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Mostrar todos os meses, não apenas os últimos 12
                            $monthly_data = $summary['monthly_evolution'];
                            // Reverter para mostrar mais recentes primeiro
                            $monthly_data = array_reverse($monthly_data);
                            
                            foreach ($monthly_data as $month_data): 
                                $dias = count(array_unique(array_map(function($r) { 
                                    return $r['dia']; 
                                }, $month_data['registros'])));
                            ?>
                            <tr>
                                <td><strong><?= $month_data['mes_ano'] ?></strong></td>
                                <td style="text-align: right;">
                                    <?= number_format($month_data['quantidade']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <?= $dias > 0 ? number_format($month_data['quantidade'] / $dias, 0) : '0' ?>
                                </td>
                                <td style="text-align: center;">
                                    <?= $dias ?> dias
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($month_data['evolucao'] !== null): ?>
                                        <span class="<?= $month_data['evolucao'] >= 0 ? 'evolution-positive' : 'evolution-negative' ?>">
                                            <?= $month_data['evolucao'] >= 0 ? '▲' : '▼' ?>
                                            <?= number_format(abs($month_data['evolucao']), 1) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #6c757d;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">📅 Evolução Mensal</h3>
            </div>
            <div class="section-content" style="text-align: center; padding: 3rem; color: #666;">
                📊 Nenhum dado de evolução mensal encontrado para este cliente.
                <br><br>
                <small>Verifique se há dados de envase carregados no sistema.</small>
            </div>
        </div>
        <?php endif; ?>

        <!-- Últimos Registros -->
        <?php if (!empty($summary['ultimos_registros'])): ?>
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">📋 Últimos Registros de Envase</h3>
            </div>
            <div class="section-content">
                <div class="table-container" style="max-height: 600px; overflow-y: auto;">
                    <table class="data-table">
                        <thead style="position: sticky; top: 0; background: #f8f9fa; z-index: 10;">
                            <tr>
                                <th>Data</th>
                                <th>Produto</th>
                                <th style="text-align: right;">Quantidade</th>
                                <th>Cidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Mostrar mais registros, não apenas 10
                            $registros_para_mostrar = array_slice($summary['ultimos_registros'], 0, 50);
                            foreach ($registros_para_mostrar as $registro): 
                            ?>
                            <tr>
                                <td><?= sprintf('%02d/%02d/%d', $registro['dia'], $registro['mes'], $registro['ano']) ?></td>
                                <td><?= htmlspecialchars($registro['produto']) ?></td>
                                <td style="text-align: right;">
                                    <strong><?= number_format($registro['quantidade']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($registro['cidade'] ?? 'N/A') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (count($summary['ultimos_registros']) > 50): ?>
                <div style="text-align: center; margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 4px;">
                    <small style="color: #666;">
                        Mostrando os 50 registros mais recentes de <?= count($summary['ultimos_registros']) ?> total.
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">📋 Últimos Registros de Envase</h3>
            </div>
            <div class="section-content" style="text-align: center; padding: 3rem; color: #666;">
                📊 Nenhum registro de envase encontrado para este cliente.
                <br><br>
                <small>Verifique se há dados de envase carregados no sistema.</small>
            </div>
        </div>
        <?php endif; ?>

        <!-- Gerenciar Dados -->
        <div class="manage-section">
            <div class="section-header" style="background: transparent; border: none; padding: 0 0 1rem 0;">
                <h3 class="section-title">⚙️ Gerenciar Dados</h3>
            </div>
            
            <div class="manage-buttons">
                <a href="<?= BASE_URL ?>/envase/edit-client-data/<?= $client['id'] ?>" class="manage-btn btn-edit">
                    ✏️ Editar Dados
                </a>
                <a href="<?= BASE_URL ?>/envase/delete-client-data/<?= $client['id'] ?>" 
                   class="manage-btn btn-delete" 
                   onclick="return confirm('Tem certeza que deseja excluir todos os dados de envase deste cliente?')">
                    🗑️ Excluir Dados
                </a>
            </div>
            
            <div class="manage-description">
                <strong>Editar:</strong> Permite modificar registros individuais de envase<br>
                <strong>Excluir:</strong> Remove todos os dados de envase desta empresa
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include '../src/views/layouts/client_detail.php'; ?>
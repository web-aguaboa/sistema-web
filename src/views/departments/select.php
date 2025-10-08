<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selecionar Departamento - Web Aguaboa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #007fa3, #00a8cc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .department-container {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 900px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .logo-brand {
            max-width: 400px;
            width: 100%;
            height: auto;
            margin: 0 auto 1rem;
            display: block;
        }
        
        .system-title {
            color: #007fa3;
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .departments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .department-card {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }
        
        .department-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 127, 163, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .department-card:hover {
            border-color: #007fa3;
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 127, 163, 0.2);
        }
        
        .department-card:hover::before {
            left: 100%;
        }
        
        .department-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #007fa3, #00a8cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .department-title {
            color: #007fa3;
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .department-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .logout-section {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-logout {
            background: #6c757d;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-logout:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .departments-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .department-container {
                padding: 2rem;
            }
            
            .system-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="department-container">
        <div class="header">
            <img src="<?= BASE_URL ?>/images/logo-aguaboa.svg" alt="Aguaboa - Águas de Santa Bárbara" class="logo-brand">
            <h1 class="system-title">Web Aguaboa</h1>
            <p class="subtitle">Selecione o Departamento</p>
            <div class="user-info">
                👤 Bem-vindo(a), <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> 
                (<?= $_SESSION['role'] === 'admin' ? 'Administrador' : 'Equipe' ?>)
            </div>
        </div>
        
        <div class="departments-grid">
            <?php foreach ($accessibleDepartments as $key => $department): ?>
                <a href="<?= BASE_URL ?>/<?= $key === 'comercial' ? 'crm' : $key ?>" class="department-card">
                    <div class="department-icon"><?= $department['icon'] ?></div>
                    <div class="department-title"><?= $department['name'] ?></div>
                    <div class="department-description">
                        <?= $department['description'] ?>
                        <?php if ($department['can_edit']): ?>
                            <br><small style="color: #28a745; font-weight: bold;">✏️ Permissão de edição</small>
                        <?php else: ?>
                            <br><small style="color: #ffc107; font-weight: bold;">👁️ Apenas visualização</small>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
            
            <?php if (empty($accessibleDepartments)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #666;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔒</div>
                    <h3>Nenhum departamento disponível</h3>
                    <p>Entre em contato com o administrador para solicitar acesso aos departamentos.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="logout-section">
            <a href="<?= BASE_URL ?>/auth/logout" class="btn-logout">
                🚪 Sair do Sistema
            </a>
        </div>
    </div>
</body>
</html>
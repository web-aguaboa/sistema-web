<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?? 'Detalhes do Cliente - Sistema Aguaboa' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/envase-crm.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        /* Reset alguns estilos que podem interferir */
        .card, .card-header, .card-body {
            all: unset;
        }
        
        /* Estilos específicos para mensagens flash */
        .flash-messages {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            max-width: 400px;
        }
        
        .flash-message {
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .flash-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .flash-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .flash-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .flash-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <!-- Flash Messages -->
    <?php if (!empty($flashMessages)): ?>
        <div class="flash-messages">
            <?php foreach ($flashMessages as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="flash-message flash-<?= $type ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Conteúdo da página -->
    <?= $content ?>
    
    <script>
        // Auto-hide flash messages
        setTimeout(function() {
            const flashMessages = document.querySelector('.flash-messages');
            if (flashMessages) {
                flashMessages.style.opacity = '0';
                flashMessages.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    flashMessages.remove();
                }, 500);
            }
        }, 5000);
    </script>
</body>
</html>
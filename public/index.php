<?php
/**
 * Arquivo principal de entrada
 * Sistema Aguaboa - Gestão Comercial
 */

require_once __DIR__ . '/../config/init.php';

// Router simples
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remover base URL
$path = str_replace(BASE_URL, '', $path);

// Rotas
try {
    switch (true) {
        // Autenticação
        case $path === '/' || $path === '':
            if (isset($_SESSION['user_id'])) {
                redirect('/departments');
            } else {
                redirect('/auth/login');
            }
            break;
            
        case $path === '/auth/login':
            $controller = new AuthController();
            $controller->login();
            break;
            
        case $path === '/auth/logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case $path === '/auth/change-password':
            $controller = new AuthController();
            $controller->changePassword();
            break;
            
        // Departamentos
        case $path === '/departments' || $path === '/departments/':
            $controller = new DepartmentController();
            $controller->select();
            break;
            
        case $path === '/financeiro' || $path === '/financeiro/':
            $controller = new DepartmentController();
            $controller->financeiro();
            break;
            
        case $path === '/rh' || $path === '/rh/':
            $controller = new DepartmentController();
            $controller->rh();
            break;
            
        case $path === '/qualidade' || $path === '/qualidade/':
            $controller = new DepartmentController();
            $controller->qualidade();
            break;
            
        case $path === '/atendimento' || $path === '/atendimento/':
            $controller = new DepartmentController();
            $controller->atendimento();
            break;
            
        case $path === '/producao' || $path === '/producao/':
            $controller = new DepartmentController();
            $controller->producao();
            break;
            
        // CRM
        case $path === '/crm' || $path === '/crm/':
            $controller = new CrmController();
            $controller->index();
            break;
            
        case preg_match('/^\/crm\/client\/(\d+)$/', $path, $matches):
            $controller = new CrmController();
            $controller->clientDetail($matches[1]);
            break;
            
        case $path === '/crm/create-client':
            $controller = new CrmController();
            $controller->createClient();
            break;
            
        case preg_match('/^\/crm\/edit-client\/(\d+)$/', $path, $matches):
            $controller = new CrmController();
            $controller->editClient($matches[1]);
            break;
            
        case preg_match('/^\/crm\/delete-client\/(\d+)$/', $path, $matches):
            $controller = new CrmController();
            $controller->deleteClient($matches[1]);
            break;
            
        case $path === '/crm/unify-clients':
            $controller = new CrmController();
            $controller->unifyClients();
            break;
            
        case $path === '/crm/acoes-vigentes':
            $controller = new CrmController();
            $controller->acoesVigentes();
            break;
            
        // Admin
        case $path === '/admin/users':
            $controller = new AdminController();
            $controller->users();
            break;
            
        case $path === '/admin/logs':
            $controller = new AdminController();
            $controller->logs();
            break;
            
        case $path === '/admin/create-user':
            $controller = new AdminController();
            $controller->createUser();
            break;
            
        case preg_match('/^\/admin\/edit-user\/(\d+)$/', $path, $matches):
            $controller = new AdminController();
            $controller->editUser($matches[1]);
            break;
            
        case preg_match('/^\/admin\/delete-user\/(\d+)$/', $path, $matches):
            $controller = new AdminController();
            $controller->deleteUser($matches[1]);
            break;
            
        case preg_match('/^\/admin\/toggle-user\/(\d+)$/', $path, $matches):
            $controller = new AdminController();
            $controller->toggleUser($matches[1]);
            break;
            
        case preg_match('/^\/admin\/manage-permissions\/(\d+)$/', $path, $matches):
            $controller = new AdminController();
            $controller->managePermissions($matches[1]);
            break;
            
        // Envase
        case $path === '/envase' || $path === '/envase/':
            $controller = new EnvaseController();
            $controller->dashboard();
            break;
            
        case $path === '/envase/upload':
            $controller = new EnvaseController();
            $controller->upload();
            break;
            
        case preg_match('/^\/envase\/cliente\/(\d+)$/', $path, $matches):
            try {
                $controller = new EnvaseController();
                $controller->clientData($matches[1]);
            } catch (Exception $e) {
                error_log("Erro no EnvaseController::clientData: " . $e->getMessage());
                echo "<h1>Erro</h1><p>Erro interno: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            break;
            
        case $path === '/envase/charts':
            $controller = new EnvaseController();
            $controller->charts();
            break;
            
        case preg_match('/^\/envase\/edit\/(\d+)$/', $path, $matches):
            $controller = new EnvaseController();
            $controller->editRecord($matches[1]);
            break;
            
        case preg_match('/^\/envase\/delete\/(\d+)$/', $path, $matches):
            $controller = new EnvaseController();
            $controller->deleteRecord($matches[1]);
            break;
            
        case $path === '/envase/limpar-dados':
            $controller = new EnvaseController();
            $controller->limparDados();
            break;
            
        case $path === '/envase/limpar-tudo':
            $controller = new EnvaseController();
            $controller->limparTudo();
            break;
            
        // Actions (ações do cliente)
        case $path === '/action':
            $controller = new ActionsController();
            $controller->create();
            break;
            
        case preg_match('/^\/action\/(\d+)$/', $path, $matches):
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller = new ActionsController();
                $controller->get($matches[1]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new ActionsController();
                $controller->update($matches[1]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $controller = new ActionsController();
                $controller->update($matches[1]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $controller = new ActionsController();
                $controller->delete($matches[1]);
            }
            break;
            
        // Uploads
        case preg_match('/^\/uploads\/(.+)$/', $path, $matches):
            $filename = $matches[1];
            
            // Determinar o caminho correto baseado na estrutura
            if (strpos($filename, 'actions/') === 0) {
                // Arquivo de ação: uploads/actions/arquivo.ext
                $filepath = UPLOAD_DIR . $filename;
            } else {
                // Arquivo geral: uploads/arquivo.ext
                $filepath = UPLOAD_DIR . $filename;
            }
            
            if (file_exists($filepath)) {
                $mimeType = mime_content_type($filepath);
                header('Content-Type: ' . $mimeType);
                header('Content-Disposition: inline; filename="' . basename($filename) . '"');
                readfile($filepath);
            } else {
                http_response_code(404);
                echo 'Arquivo não encontrado: ' . $filepath;
            }
            break;
            
        // 404
        default:
            http_response_code(404);
            echo '<h1>404 - Página não encontrada</h1>';
            break;
    }
} catch (Exception $e) {
    error_log("Erro na aplicação: " . $e->getMessage());
    http_response_code(500);
    echo '<h1>500 - Erro interno do servidor</h1>';
    if (ini_get('display_errors')) {
        echo '<pre>' . $e->getMessage() . '</pre>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
}
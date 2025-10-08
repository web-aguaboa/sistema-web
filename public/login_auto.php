<?php
// Script para fazer login automático e testar ações
session_start();

// Fazer login automático
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';

echo "✅ Login automático realizado!\n";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . $_SESSION['user_id'] . "\n";
echo "Role: " . $_SESSION['role'] . "\n\n";

echo "🌐 Agora você pode testar o sistema em:\n";
echo "http://localhost:8080/teste_acao_direto.html\n";
echo "http://localhost:8080/crm\n\n";

echo "🔧 Ou use este código JavaScript no console do navegador:\n";
echo "
// Teste direto no console
fetch('/action/3')
.then(r => r.text())
.then(t => {
    console.log('Response:', t);
    try {
        const data = JSON.parse(t);
        console.log('JSON válido:', data);
    } catch(e) {
        console.log('JSON inválido:', e.message);
    }
});
";
?>
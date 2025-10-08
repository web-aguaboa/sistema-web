<?php
echo "<h1>🎉 Sistema Aguaboa PHP - Funcionando!</h1>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<hr>";
echo "<h2>🔗 Links para acessar:</h2>";
echo "<ul>";
echo "<li><a href='/gestao-aguaboa-php/public/'>🌐 Sistema Principal</a></li>";
echo "<li><a href='/gestao-aguaboa-php/public/test.php'>🧪 Teste Completo</a></li>";
echo "<li><a href='/phpmyadmin/'>🗄️ phpMyAdmin</a></li>";
echo "</ul>";
echo "<hr>";
echo "<p><strong>✅ Tudo funcionando perfeitamente!</strong></p>";
?>
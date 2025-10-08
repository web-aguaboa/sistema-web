    </div> <!-- fecha main-container -->
    
    <script>
        // Função para mostrar mensagens de feedback
        function showMessage(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 6px;
                color: white;
                font-weight: 500;
                z-index: 1000;
                max-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideIn 0.3s ease-out;
            `;
            
            // Cores baseadas no tipo
            switch(type) {
                case 'success':
                    alertDiv.style.background = '#28a745';
                    break;
                case 'error':
                    alertDiv.style.background = '#dc3545';
                    break;
                case 'warning':
                    alertDiv.style.background = '#ffc107';
                    alertDiv.style.color = '#212529';
                    break;
                default:
                    alertDiv.style.background = '#17a2b8';
            }
            
            alertDiv.textContent = message;
            document.body.appendChild(alertDiv);
            
            // Auto-remover após 4 segundos
            setTimeout(() => {
                alertDiv.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 300);
            }, 4000);
        }
        
        // Adicionar estilos de animação
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            .loading {
                pointer-events: none;
                opacity: 0.6;
            }
            
            .loading::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 20px;
                height: 20px;
                margin: -10px 0 0 -10px;
                border: 2px solid #f3f3f3;
                border-radius: 50%;
                border-top: 2px solid #3498db;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
        
        // Função para confirmar exclusões
        function confirmDelete(message = 'Tem certeza que deseja excluir este item?') {
            return confirm(message);
        }
        
        // Função para formatar datas
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR');
        }
        
        // Função para validar formulários
        function validateForm(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ced4da';
                }
            });
            
            return isValid;
        }
    </script>
</body>
</html>
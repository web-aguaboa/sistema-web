# Sistema Aguaboa - Gestão Comercial (PHP)

Sistema PHP equivalente ao sistema Python original, com todas as funcionalidades replicadas.

## 🚀 Instalação e Configuração

### 📋 Pré-requisitos
- XAMPP (Apache + MySQL + PHP 7.4+)
- Extensões PHP: pdo_mysql, gd, zip

### 🔧 Configuração

1. **Certifique-se que o XAMPP está rodando:**
   - Apache
   - MySQL

2. **Configure o banco de dados:**
   ```bash
   # Execute o script de configuração
   C:\xampp\php\php.exe "C:\xampp\htdocs\gestao-aguaboa-php\scripts\setup_database.php"
   ```

3. **Acesse o sistema:**
   ```
   http://localhost/gestao-aguaboa-php
   ```

### 🔐 Credenciais de Acesso

**Administrador:**
- Usuário: `Branco`
- Senha: `652409`

**Equipe:**
- Usuário: `equipe`
- Senha: `equipe123`

## 📊 Funcionalidades Implementadas

### ✅ Sistema de Autenticação
- Login/logout com validação
- Controle de sessão
- Diferentes níveis de acesso (admin/equipe)
- Alteração de senha
- Log de atividades de login

### ✅ CRM (Gestão de Clientes)
- **Listagem completa** de clientes com paginação
- **Busca inteligente** por nome, empresa ou cidade
- **Filtros** por tipo (Premium, Exclusivo)
- **Visualização detalhada** do cliente
- **Cadastro de novos clientes** (apenas admin)
- **Edição de clientes** (apenas admin)
- **Exclusão de clientes** (apenas admin)
- **Classificações**: Exclusivo/Multimarcas, Normal/Master, Premium
- **Gerenciamento de frete**: Próprio ou Freteiro

### ✅ Sistema de Logs
- **Auditoria completa** de todas as ações
- **Rastreamento por usuário** e IP
- **Visualização de logs** para administradores
- **Limpeza automática** de logs antigos

### 🔄 Em Desenvolvimento
- Sistema de Envase (upload de planilhas Excel)
- Dashboard com gráficos e estatísticas
- Gestão de ações por cliente
- Relatórios e exportação

## 📁 Estrutura do Projeto

```
gestao-aguaboa-php/
├── config/
│   ├── database.php         # Configuração do banco
│   └── init.php            # Autoloader e configurações
├── public/
│   ├── index.php           # Arquivo principal (router)
│   ├── css/               # Estilos
│   ├── js/                # Scripts
│   └── uploads/           # Arquivos enviados
├── scripts/
│   └── setup_database.php # Script de configuração
├── sql/
│   └── create_database.sql # SQL para criação manual
├── src/
│   ├── controllers/       # Controladores
│   │   ├── AuthController.php
│   │   ├── CrmController.php
│   │   └── AdminController.php
│   ├── models/           # Modelos de dados
│   │   ├── User.php
│   │   ├── Client.php
│   │   ├── ActivityLog.php
│   │   └── Action.php
│   ├── views/            # Templates
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── crm/
│   │   └── admin/
│   └── utils/           # Utilitários
└── README.md
```

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+ (orientado a objetos)
- **Banco de dados**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (puro)
- **Arquitetura**: MVC (Model-View-Controller)
- **Segurança**: 
  - Hash de senhas com `password_hash()`
  - Sanitização de dados
  - Proteção contra SQL Injection (PDO)
  - Controle de sessão
  - Log de auditoria

## 🔒 Segurança

- Todas as senhas são armazenadas com hash seguro
- Proteção contra SQL Injection usando PDO
- Sanitização de todos os dados de entrada
- Controle de acesso baseado em funções
- Log completo de atividades para auditoria
- Validação de arquivos de upload

## 📈 Próximas Funcionalidades

1. **Sistema de Envase**
   - Upload de planilhas Excel (.xls/.xlsx)
   - Processamento automático de dados
   - Integração com clientes existentes

2. **Dashboard Avançado**
   - Gráficos de evolução
   - Estatísticas em tempo real
   - Relatórios customizados

3. **Gestão de Ações**
   - Histórico de ações por cliente
   - Upload de arquivos/fotos
   - Calendário de atividades

## 🤝 Equivalência com Sistema Python

Este sistema PHP replica **exatamente** as funcionalidades do sistema Python original:

- ✅ Mesma estrutura de banco de dados
- ✅ Mesmas funcionalidades de CRM
- ✅ Mesmo sistema de autenticação
- ✅ Mesmos níveis de acesso
- ✅ Mesmo sistema de logs
- 🔄 Sistema de envase (em desenvolvimento)

## 📞 Suporte

Sistema desenvolvido para Aguaboa - Águas de Santa Bárbara  
Versão PHP 1.0 (Outubro 2025)

Para suporte técnico ou dúvidas sobre o sistema, consulte a documentação ou entre em contato com o desenvolvedor.
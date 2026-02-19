# DECISÕES TÉCNICAS

**Desenvolvedor:** Marcus Vinnicus Rodrigues de Souza
**Data:** Fev/2026

---

## 🗄️ MODELAGEM DO BANCO DE DADOS

### Estrutura:

**3 Tabelas:**

- `suppliers`: id, name, cnpj, email, phone, status, timestamps
- `products`: id, name, description, code, status, timestamps
- `product_supplier`: id, product_id, supplier_id, created_at

**Relacionamento N:N:**

- Um produto tem vários fornecedores
- Um fornecedor fornece vários produtos
- Tabela intermediária com FKs e índices (tabela de relacionamento)

**VIEW criada:**

```sql
view_suppliers
view_products
view_product_suppliers
```

---

## 🏗️ POR QUE ESSA ESTRUTURA?

### 1. Relacionamento N:N

**Por quê?** Produto pode ter múltiplos fornecedores e vice-versa.

### 2. UNIQUE Constraints

```sql
cnpj UNIQUE
code UNIQUE
(product_id, supplier_id) UNIQUE
```

**Por quê?** Evita duplicação no nível do banco, segurança extra.

---

### 3. VIEW com JOIN

**Por quê?**

- Encapsula query complexa
- Facilita consultas no PHP
- Código mais limpo

---

### 4. Índices

```sql
INDEX (product_id)
INDEX (supplier_id)
```

**Por quê?** Otimiza performance dos JOINs.

---

## 🎨 ARQUITETURA

**Padrão:** MVC Simplificado

```
VIEW (HTML/JS) → SERVICE (AJAX) → API ENDPOINT → CONTROLLER → MODEL → DATABASE
```

**Camadas:**

1. **VIEW**: Interface usuário (HTML/CSS/JS)
2. **SERVICE**: Abstração AJAX (JavaScript)
3. **ENDPOINT**: Recebe requisições (PHP)
4. **CONTROLLER**: Valida e processa
5. **MODEL**: Queries SQL
6. **DATABASE**: MySQL

**Por que MVC?**

- Separação de responsabilidades
- Fácil manutenção
- Código reutilizável
- Testável

## 🚀 MELHORIAS FUTURAS

### Interface:

- Framework CSS (Bootstrap/Tailwind)
- Design responsivo
- Filtros e busca avançada
- Atalho para criar produtos ou fonecedores
- Usuario deletar ou adicionar produtos já estando dentro da lista do fornecedore
- Exibir quais ID podem ser deletados e quais não

### Performance:

- Cache (Redis)
- Lazy loading
- Índices adicionais

### DevOps:

- Docker
- CI/CD
- Migrations
- Testes automatizados

---

## 📊 TECNOLOGIAS

- PHP 7.4+ (Backend)
- MySQL 5.7+ (Banco)
- jQuery 3.x (AJAX)
- JavaScript ES6+ (Frontend)
- HTML5/CSS3

---

## ✅ CONCLUSÃO

Sistema desenvolvido com:

- Código limpo e organizado
- MVC para separação de responsabilidades
- Normalização e integridade de dados
- Prepared statements para segurança

Base sólida para expansão futura.

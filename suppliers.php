<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Sistema de Gestão</title>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/suppliers.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/js/helpers/masks.js" defer></script>
    <script src="/js/suppliers.js" defer type="module"></script>
</head>

<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Sistema de Gestão</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="relations.php">🔗 Vínculos</a></li>
            <li><a href="suppliers.php" class="active">📦 Fornecedores</a></li>
            <li><a href="products.php">📋 Produtos</a></li>

        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <h1>Fornecedores</h1>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="toolbar-left">
                <button class="btn btn-primary" id="btnNewSupplier">
                    Novo Fornecedor
                </button>
                <button class="btn btn-danger" id="btnDeleteSelected" disabled>
                    Deletar Selecionados
                </button>
            </div>
            <div class="toolbar-right">
                <button class="btn btn-outline" id="btnToggleFilter">
                    Filtros
                </button>
            </div>
        </div>

        <div class="filter-panel" id="filterPanel">
            <p class="filter-panel-title">Filtros</p>
            <div class="filter-row">

                <div class="filter-group">
                    <label for="filterName">Nome</label>
                    <input type="text" id="filterName" placeholder="Buscar por nome...">
                </div>

                <div class="filter-group">
                    <label for="filterCnpj">CNPJ</label>
                    <input type="text" id="filterCnpj" placeholder="00.000.000/0000-00" minlength="18">
                </div>

                <div class="filter-group">
                    <label for="filterEmail">Email</label>
                    <input type="text" id="filterEmail" placeholder="Buscar por email...">
                </div>

                <div class="filter-group-status">
                    <label>Status</label>
                    <div class="checkbox-wrapper">
                        <input type="checkbox" class="filterStatus" value="1">
                        <span> Ativos</span>
                        <input type="checkbox" class="filterStatus" value="0">
                        <span>Inativos</span>
                    </div>
                </div>

                <div class="filter-actions">
                    <button class="btn-filter" id="btnApplyFilter">Aplicar</button>
                    <button class="btn-clear" id="btnClearFilter">Limpar</button>
                </div>

            </div>
        </div>

        <!-- TABLE -->
        <div class="table-container">
            <table id="suppliersTable">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="suppliersTableBody">
                    <!-- Exemplo de linha (será populado via JS) -->
                    <td>
                    <th>Tabela vazia</th>
                    </td>
                </tbody>
            </table>
        </div>
    </main>

    <!-- MODAL CRIAR/EDITAR FORNECEDOR -->
    <div class="modal" id="supplierModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Novo Fornecedor</h3>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="supplierForm">
                    <input type="hidden" id="supplierId">

                    <div class="form-group">
                        <label for="supplierName">Nome *</label>
                        <input type="text" class="form-control" id="supplierName" maxlength="180" required>
                    </div>

                    <div class="form-group">
                        <label for="supplierCnpj">CNPJ *</label>
                        <input type="text" class="form-control" id="supplierCnpj" placeholder="00.000.000/0000-00"
                            minlength="18" required>
                    </div>

                    <div class="form-group">
                        <label for="supplierEmail">Email *</label>
                        <input type="email" class="form-control" id="supplierEmail" required>
                    </div>

                    <div class="form-group">
                        <label for="supplierPhone">Telefone *</label>
                        <input type="text" class="form-control" id="supplierPhone" placeholder="(00) 00000-0000"
                            minlength="15" required>
                    </div>

                    <div class="form-group">
                        <label for="supplierStatus">Status</label>
                        <select class="form-control" id="supplierStatus">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="btn btn-primary" onclick="saveSupplier()">Salvar</button>
            </div>
        </div>
    </div>
</body>

</html>
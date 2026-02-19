<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Sistema de Gestão</title>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/products.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/js/products.js" defer type="module"></script>
</head>

<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Sistema de Gestão</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="relations.php">🔗 Vínculos</a></li>
            <li><a href="suppliers.php">📦 Fornecedores</a></li>
            <li><a href="products.php" class="active">📋 Produtos</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <h1>Produtos</h1>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="toolbar-left">
                <button class="btn btn-primary" id="btnNewProduct">
                    Novo Produto
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


        <!-- FILTER PANEL (ESSE AQUI PRECISA ESTAR NO HTML!) -->
        <div class="filter-panel" id="filterPanel">
            <p class="filter-panel-title">Filtros</p>
            <div class="filter-row">

                <div class="filter-group">
                    <label for="filterName">Nome</label>
                    <input type="text" id="filterName" placeholder="Buscar por nome...">
                </div>

                <div class="filter-group">
                    <label for="filterCode">Código</label>
                    <input type="text" id="filterCode" placeholder="Ex: 001"
                        oninput="this.value = this.value.replace(/\D/g, '')">
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
            <table id="productsTable">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Código</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <td>
                    <th>Tabela vazia</th>
                    </td>
                </tbody>
            </table>
        </div>
    </main>

    <!-- MODAL CRIAR/EDITAR PRODUTO -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Novo Produto</h3>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId">

                    <div class="form-group">
                        <label for="productName">Nome *</label>
                        <input type="text" class="form-control" id="productName" required>
                    </div>

                    <div class="form-group">
                        <label for="productDescription">Descrição</label>
                        <textarea class="form-control" id="productDescription"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="productCode">Código *</label>
                        <input type="number" class="form-control" id="productCode" required>
                    </div>

                    <div class="form-group">
                        <label for="productStatus">Status</label>
                        <select class="form-control" id="productStatus">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="btn btn-primary" onclick="saveProduct()">Salvar</button>
            </div>
        </div>
    </div>

</body>

</html>
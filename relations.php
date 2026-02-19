<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vínculos - Sistema de Gestão</title>
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/relations.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/js/helpers/masks.js" defer></script>
    <script src="/js/relations.js" defer type="module"></script>
</head>

<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Sistema de Gestão</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="relations.php" class="active">🔗 Vínculos</a></li>
            <li><a href="suppliers.php">📦 Fornecedores</a></li>
            <li><a href="products.php">📋 Produtos</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <h1>Vínculos Produto-Fornecedor</h1>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="toolbar-left">
                <button class="btn btn-primary" id="btnNewRelation">
                    Novo Vínculo
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

                <div class="filter-actions">
                    <button class="btn-filter" id="btnApplyFilter">Aplicar</button>
                    <button class="btn-clear" id="btnClearFilter">Limpar</button>
                </div>

            </div>
        </div>

        <!-- TABLE -->
        <div class="table-container">
            <table id="relationsTable">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>ID</th>
                        <th>Fornecedor</th>
                        <th>CNPJ</th>
                        <th>Produtos</th>
                        <th>Vinculado em</th>
                    </tr>
                </thead>
                <tbody id="relationsTableBody">
                    <td>
                    <th>Tabela vazia</th>
                    </td>
                </tbody>
            </table>
        </div>
    </main>

    <!-- MODAL CRIAR VÍNCULO (REFORMULADA) -->
    <div class="modal" id="relationModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Novo Vínculo</h3>
                <button class="modal-close" onclick="closeRelationModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="relationForm">
                    <!-- Seleção de Fornecedor -->
                    <div class="form-group">
                        <label for="relationSupplier">Fornecedor *</label>
                        <select class="form-control" id="relationSupplier" required>
                            <option value="">Selecione um fornecedor</option>
                            <!-- Preenchido dinamicamente pelo JS -->
                        </select>
                    </div>

                    <!-- Tabela de Produtos -->
                    <div class="form-group">
                        <label>Produtos *</label>
                        <div class="table-container">
                            <table class="table" id="productsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Código</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Preenchido dinamicamente pelo JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeRelationModal()">Cancelar</button>
                <button class="btn btn-primary" onclick="saveRelation()">Criar Vínculo</button>
            </div>
        </div>
    </div>

    <!-- MODAL VER PRODUTOS DO FORNECEDOR -->
    <div class="modal" id="productsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="productsModalTitle">Produtos</h3>
                <button class="modal-close" onclick="closeProductsModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="products-list" id="productsList">
                    <!-- Exemplo de produtos (será populado via JS) -->
                    <div class="product-item">
                        <div class="product-info">
                            <h4>Mouse Gamer RGB</h4>
                            <p>Mouse óptico com iluminação RGB, 7 botões programáveis</p>
                        </div>
                        <span class="product-code">MOU-001</span>
                    </div>
                    <div class="product-item">
                        <div class="product-info">
                            <h4>Teclado Mecânico</h4>
                            <p>Teclado mecânico com switches blue, RGB</p>
                        </div>
                        <span class="product-code">KEY-001</span>
                    </div>
                    <div class="product-item">
                        <div class="product-info">
                            <h4>Headset Gamer</h4>
                            <p>Headset com som surround 7.1</p>
                        </div>
                        <span class="product-code">HEA-001</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeProductsModal()">Fechar</button>
            </div>
        </div>
    </div>
</body>

</html>
import productService from './services/products.js';
import response from './helpers/response.js';

document.addEventListener('DOMContentLoaded', () => {

    const loadProducts = () => {
        productService.getAll()
            .done((res) => {
                if (res.success) {
                    renderTable(res.data);
                } else {
                    response.error(res.message);
                }
            })
            .fail(() => response.error('Erro de conexão!'));
    };

    const filterProducts = () => {
        var name = document.getElementById('filterName').value;
        var code = document.getElementById('filterCode').value;

        // Pega todos os checkboxes de status marcados
        var statusFilters = [];
        document.querySelectorAll('.filterStatus:checked').forEach((cb) => {
            statusFilters.push(cb.value);
        });

        // Se os 2 checkboxes estão marcados, manda vazio (= sem filtro de status)
        var status = statusFilters.length === 2 ? '' : statusFilters.join(',');

        productService.filter({ name, code, status })
            .done((res) => {
                if (res.success) {
                    renderTable(res.data);
                } else {
                    response.error(res.message);
                }
            })
            .fail(() => response.error('Erro de conexão!'));
    };


    // Monta as linhas da tabela com os produtos recebidos
    const renderTable = (products) => {
        var tbody = document.getElementById('productsTableBody');

        tbody.innerHTML = '';

        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#6c757d;">
                        Nenhum produto encontrado.
                    </td>
                </tr>
            `;
            return;
        }

        products.forEach((product) => {
            var status = product.status == 1
                ? '<span class="status-badge status-active">Ativo</span>'
                : '<span class="status-badge status-inactive">Inativo</span>';

            var row = `
                <tr>
                    <td class="checkbox-cell">
                        <input type="checkbox" class="row-checkbox" data-id="${product.id}">
                    </td>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.description ?? '-'}</td>
                    <td>${product.code}</td>
                    <td>${status}</td>
                    <td>${formatDate(product.created_at)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action" onclick="editProduct(${product.id})">✏️ Editar</button>
                        </div>
                    </td>
                </tr>
            `;

            tbody.insertAdjacentHTML('beforeend', row);
        });

        bindCheckboxes();
    };

    // FORMAT DATE - Formata data para o padrão brasileiro (dd/mm/aaaa hh:mm)
    const formatDate = (dateString) => {

        if (!dateString) return '-';

        var date = new Date(dateString);
        return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit'
        });

    };


    // campos  obrigatórios foram preenchidos
    const validateProduct = () => {

        var name = document.getElementById('productName').value.trim();
        var code = document.getElementById('productCode').value.trim();
        var status = document.getElementById('productStatus').value;

        if (!name) { response.warning('Preencha o nome do produto', 'Campo obrigatório'); return false; }
        if (!code) { response.warning('Preencha o código do produto', 'Campo obrigatório'); return false; }
        if (!status) { response.warning('Preencha o status do produto', 'Campo obrigatório'); return false; }

        return true;

    };

    // Abre o modal para criar um novo produto
    document.getElementById('btnNewProduct').addEventListener('click', () => {

        document.getElementById('modalTitle').textContent = 'Novo Produto';
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productModal').classList.add('show');

    });

    const closeModal = () => {
        document.getElementById('productModal').classList.remove('show');
    };

    // EDIT 
    const editProduct = (id) => {
        response.loading('Carregando...');

        productService.getById(id)
            .done((res) => {
                response.closeLoading();

                if (res.success) {

                    var product = res.data[0];

                    document.getElementById('modalTitle').textContent = 'Editar Produto';
                    document.getElementById('productId').value = product.id;
                    document.getElementById('productName').value = product.name;
                    document.getElementById('productDescription').value = product.description || '';
                    document.getElementById('productCode').value = product.code;
                    document.getElementById('productStatus').value = product.status;
                    document.getElementById('productModal').classList.add('show');

                } else {
                    response.error(res.errors);
                }
            })
            .fail(() => {
                response.closeLoading();
                response.error('Erro de conexão!');
            });
    };

    // CREATE / UPDATE - Salva ou atualiza um produto
    const saveProduct = () => {

        if (!validateProduct()) return;

        var productId = document.getElementById('productId').value;

        // é edição
        var isEdit = productId !== '';

        var data = {
            name: document.getElementById('productName').value.trim(),
            description: document.getElementById('productDescription').value.trim(),
            code: document.getElementById('productCode').value.trim(),
            status: document.getElementById('productStatus').value,
        };

        response.loading('Salvando...');

        var promise = isEdit
            ? productService.update(productId, data)
            : productService.create(data);

        promise
            .done((res) => {
                response.closeLoading();

                if (res.success) {

                    response.success(res.message, isEdit ? 'Produto atualizado!' : 'Produto criado!');
                    closeModal();
                    loadProducts();

                } else {
                    response.error(res.errors);
                }
            })
            .fail((xhr) => {
                response.closeLoading();

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;

                    let message = '';

                    if (Array.isArray(errors) || typeof errors === 'object') {
                        message = 'Não foi possível deletar, ID está vinculado.';

                    } else if (typeof errors === 'string') {
                        message = errors;
                    }


                    response.error(message);
                }

            });
    };

    // Fecha modal ao clicar no fundo escuro
    document.getElementById('productModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeModal();
    });

    // Registra evento de mudança nos checkboxes da tabela
    const bindCheckboxes = () => {
        document.querySelectorAll('.row-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', updateDeleteButton);
        });
    };

    // Marcar/desmarcar todos os checkboxes de uma vez
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach((cb) => {
            cb.checked = this.checked;
        });
        updateDeleteButton();
    });

    // Habilita ou desabilita o botão deletar
    const updateDeleteButton = () => {
        var checked = document.querySelectorAll('.row-checkbox:checked').length;
        document.getElementById('btnDeleteSelected').disabled = checked === 0;
    };

    //  Deleta os produtos selecionados
    document.getElementById('btnDeleteSelected').addEventListener('click', () => {

        var ids = Array.from(document.querySelectorAll('.row-checkbox:checked'))
            .map((cb) => cb.getAttribute('data-id'));

        if (ids.length === 0) {
            response.warning('Selecione pelo menos um produto');
            return;
        }

        response.confirmDelete(`Deletar ${ids.length} produto(s)?`)
            .then((result) => {

                if (result.isConfirmed) {
                    response.loading('Deletando...');

                    productService.delete(ids)
                        .done((res) => {
                            response.closeLoading();

                            if (res.success) {

                                response.success(res.message, 'Deletado!');
                                document.getElementById('selectAll').checked = false;
                                loadProducts();

                            } else {

                                var errorMsg = res.errors ? Object.values(res.errors).join(', ') : res.error;
                                response.error(errorMsg);
                            }
                        })
                        .fail((xhr) => {

                            response.closeLoading();

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;

                                let message = '';

                                if (Array.isArray(errors) || typeof errors === 'object') {
                                    message = 'Não foi possível deletar, ID está vinculado.';

                                } else if (typeof errors === 'string') {
                                    message = errors;
                                }

                                response.error(message);
                            }

                        });
                }
            });
    });


    // FILTER 
    document.getElementById('btnToggleFilter').addEventListener('click', function () {
        document.getElementById('filterPanel').classList.toggle('show');
        this.classList.toggle('active');
    });

    document.getElementById('btnApplyFilter').addEventListener('click', () => filterProducts());

    document.getElementById('btnClearFilter').addEventListener('click', () => {

        document.getElementById('filterName').value = '';
        document.getElementById('filterCode').value = '';
        document.querySelectorAll('.filterStatus').forEach((filtro) => filtro.checked = false);
        loadProducts();

    });

    // EXPOR FUNÇÕES GLOBAIS
    window.closeModal = closeModal;
    window.editProduct = editProduct;
    window.saveProduct = saveProduct;

    loadProducts();

});
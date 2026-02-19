import relationService from './services/relations.js';
import productService from './services/products.js';
import supplierService from './services/suppliers.js';
import response from './helpers/response.js';

document.addEventListener('DOMContentLoaded', () => {

    const loadRelations = () => {
        relationService.getAll()
            .done((res) => {
                if (res.success) {
                    renderTable(res.data);
                } else {
                    response.error(res.error || res.message || 'Erro desconhecido');
                }
            })
            .fail((xhr) => response.error(`Erro de conexão! Status: ${xhr.status}`));
    };

    const filterRelations = () => {
        var supplier_name = document.getElementById('filterName').value;
        var supplier_cnpj = document.getElementById('filterCnpj').value;
        var supplier_email = document.getElementById('filterEmail').value;

        relationService.filter({ supplier_name, supplier_cnpj, supplier_email })
            .done((res) => {
                if (res.success) {
                    renderTable(res.data);
                } else {
                    response.error(res.message);
                }
            })
            .fail(() => response.error('Erro de conexão!'));
    };

    const renderTable = (suppliers) => {
        var tbody = document.getElementById('relationsTableBody');
        tbody.innerHTML = '';

        if (!suppliers || suppliers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#6c757d;">
                        Nenhum vínculo encontrado.
                    </td>
                </tr>
            `;
            return;
        }

        suppliers.forEach((supplier) => {

            var productCount = supplier.products ? supplier.products.length : 0;
            var productsText = productCount > 0 ? `Ver ${productCount} produto(s)` : 'Nenhum produto';

            var row = `
                <tr>
                    <td class="checkbox-cell">
                        <input type="checkbox" class="row-checkbox" data-id="${supplier.supplier_id}">
                    </td>
                    <td>${supplier.supplier_id}</td>
                    <td>${supplier.supplier_name}</td>
                    <td>${supplier.supplier_cnpj || '-'}</td>
                    <td>
                        <span class="clickable-link"
                              onclick="showProducts(${supplier.supplier_id}, '${supplier.supplier_name.replace(/'/g, "\\'")}')">
                            ${productsText}
                        </span>
                    </td>
                    <td>${formatDate(supplier.created_at)}</td>
                </tr>
            `;

            tbody.insertAdjacentHTML('beforeend', row);
        });

        bindCheckboxes();
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        var date = new Date(dateString);
        return date.toLocaleDateString('pt-BR') + ' ' +
            date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    };

    //Exibe uma mensagem centralizada na tabela de produtos do modal
    const setProductsTableMessage = (message, isError = false) => {
        var color = isError ? 'color: #dc3545;' : '';
        document.getElementById('productsTableBody').innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; ${color}">
                    ${message}
                </td>
            </tr>
        `;
    };

    // MODAL NOVO VÍNCULO - Abre o modal e carrega a lista de fornecedores
    document.getElementById('btnNewRelation').addEventListener('click', () => {
        document.getElementById('relationForm').reset();
        setProductsTableMessage('Carregando...');

        supplierService.getAll()
            .done((res) => {
                if (res.success) {
                    var select = document.getElementById('relationSupplier');
                    select.innerHTML = '<option value="">Selecione um fornecedor</option>';

                    res.data.forEach((supplier) => {
                        if (supplier.status !== 1) return;

                        var option = document.createElement('option');
                        option.value = supplier.id;
                        option.textContent = `${supplier.name} (${supplier.cnpj})`;
                        select.appendChild(option);
                    });
                } else {

                    setProductsTableMessage('Erro ao carregar fornecedores', true);

                }
            })
            .fail(() => setProductsTableMessage('Erro de conexão!', true));

        document.getElementById('relationModal').classList.add('show');
    });


    // Carrega os produtos ao trocar o fornecedor no select
    document.getElementById('relationSupplier').addEventListener('change', function () {
        var supplierId = this.value;

        if (!supplierId) {
            setProductsTableMessage('Selecione um fornecedor para ver os produtos');
            return;
        }

        setProductsTableMessage('Carregando produtos...');

        productService.getAll()
            .done((res) => {
                if (res.success) {
                    renderProductsTable(res.data);
                } else {
                    setProductsTableMessage('Erro ao carregar produtos', true);
                }
            })
            .fail(() => setProductsTableMessage('Erro de conexão!', true));
    });

    // Monta as linhas de produtos dentro do modal de vínculo
    const renderProductsTable = (products) => {
        var tbody = document.getElementById('productsTableBody');
        tbody.innerHTML = '';

        if (products.length === 0) {
            setProductsTableMessage('Nenhum produto encontrado');
            return;
        }

        products.forEach((product) => {
            // Exibe apenas produtos ativos (status 1)
            if (product.status !== 1) return;

            var row = `
                <tr>
                    <td class="checkbox-cell">
                        <input type="checkbox" class="product-checkbox" data-id="${product.id}">
                    </td>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.code || '-'}</td>
                    <td><span style="color: #28a745;">Ativo</span></td>
                </tr>
            `;

            tbody.insertAdjacentHTML('beforeend', row);
        });
    };

    // FECHAR MODAIS
    const closeRelationModal = () => {
        document.getElementById('relationModal').classList.remove('show');
    };

    const closeProductsModal = () => {
        document.getElementById('productsModal').classList.remove('show');
    };

    // Abre o modal de visualização dos produtos de um fornecedor
    const showProducts = (supplierId, supplierName) => {

        document.getElementById('productsModalTitle').textContent = `Produtos de ${supplierName}`;
        document.getElementById('productsList').innerHTML =
            '<p style="text-align:center; padding:20px;">Carregando produtos...</p>';

        relationService.getAll({ supplier_id: supplierId })
            .done((res) => {
                if (res.success && res.data.length > 0) {
                    // .find percorre o array e retorna o primeiro item que satisfaz a condição
                    var supplier = res.data.find((s) => s.supplier_id === supplierId);

                    if (supplier) {
                        renderProductsModal(supplierName, supplier.products || []);
                    } else {
                        document.getElementById('productsList').innerHTML =
                            '<p style="text-align:center; padding:20px;">Nenhum produto vinculado.</p>';
                    }
                } else {
                    document.getElementById('productsList').innerHTML =
                        '<p style="text-align:center; padding:20px;">Nenhum produto vinculado.</p>';
                }
                document.getElementById('productsModal').classList.add('show');
            })
            .fail((xhr) => {
                document.getElementById('productsList').innerHTML =
                    `<p style="text-align:center; color:#dc3545; padding:20px;">Erro ao carregar produtos: ${xhr.status}</p>`;
                document.getElementById('productsModal').classList.add('show');
            });
    };

    // Monta a lista de produtos dentro do modal de visualização
    const renderProductsModal = (supplierName, products) => {

        document.getElementById('productsModalTitle').textContent = `Produtos de: ${supplierName}`;

        var list = document.getElementById('productsList');
        list.innerHTML = '';

        if (!products || products.length === 0) {
            list.innerHTML = '<p style="text-align:center; padding:20px;">Nenhum produto vinculado.</p>';
            return;
        }

        products.forEach((product) => {
            var status = product.product_status == 1 ? 'Ativo' : 'Inativo';
            var item = `
                <div class="product-item">
                    <div class="product-info">
                        <h4>${product.product_name}</h4>
                        <p><strong>Código:</strong> ${product.product_code || 'Sem código'}</p>
                        <p><strong>Status:</strong> ${status}</p>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', item);
        });

    };

    //  Cria o vínculo entre fornecedor e produtos selecionados
    const saveRelation = () => {

        var supplierId = document.getElementById('relationSupplier').value;
        var selectedProducts = document.querySelectorAll('.product-checkbox:checked');

        if (!supplierId) {
            response.warning('Selecione um fornecedor');
            return;
        }

        if (selectedProducts.length === 0) {
            response.warning('Selecione pelo menos um produto');
            return;
        }

        // pega o data-id e converte para inteiro
        var productIds = Array.from(selectedProducts).map((cb) => parseInt(cb.dataset.id));

        relationService.create({
            supplier_id: parseInt(supplierId),
            product_ids: productIds
        })
            .done((res) => {
                if (res.success) {
                    response.success(res.message || `${productIds.length} produto(s) vinculado(s) com sucesso!`);
                    closeRelationModal();
                    loadRelations();
                } else {
                    var errorMsg = res.errors
                        ? Object.values(res.errors).join(', ')
                        : (res.error || 'Erro ao criar vínculo');
                    response.error(errorMsg);
                }
            })
            .fail((xhr) => {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    response.error(xhr.responseJSON.errors);
                }
            });
    };

    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach((cb) => cb.checked = this.checked);
        updateDeleteButton();
    });

    // Habilita ou desabilita o botão deletar conforme checkboxes marcados
    const updateDeleteButton = () => {
        var checked = document.querySelectorAll('.row-checkbox:checked').length;
        document.getElementById('btnDeleteSelected').disabled = checked === 0;
    };

    // Registra o evento de change nos checkboxes da tabela
    const bindCheckboxes = () => {
        document.querySelectorAll('.row-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', updateDeleteButton);
        });
    };

    //  Deleta os vínculos selecionados
    document.getElementById('btnDeleteSelected').addEventListener('click', () => {

        var ids = Array.from(document.querySelectorAll('.row-checkbox:checked'))
            .map((cb) => cb.getAttribute('data-id'));

        if (ids.length === 0) {
            response.warning('Selecione pelo menos um vínculo');
            return;
        }

        response.confirmDelete(`Deletar ${ids.length} vinculo(s)?`)
            .then((result) => {

                if (result.isConfirmed) {
                    response.loading('Deletando...');

                    relationService.delete(ids)
                        .done((res) => {
                            response.closeLoading();

                            if (res.success) {
                                response.success(res.message, 'Deletado!');
                                document.getElementById('selectAll').checked = false;
                                loadRelations();
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

    // FILTER - Mostra/oculta o painel de filtros
    document.getElementById('btnToggleFilter').addEventListener('click', function () {
        document.getElementById('filterPanel').classList.toggle('show');
        this.classList.toggle('active');
    });

    document.getElementById('btnApplyFilter').addEventListener('click', () => filterRelations());

    document.getElementById('btnClearFilter').addEventListener('click', () => {
        document.getElementById('filterName').value = '';
        document.getElementById('filterCnpj').value = '';
        loadRelations();
    });

    document.getElementById('relationModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeRelationModal();
    });

    document.getElementById('productsModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeProductsModal();
    });

    window.saveRelation = saveRelation;
    window.showProducts = showProducts;
    window.closeRelationModal = closeRelationModal;
    window.closeProductsModal = closeProductsModal;

    loadRelations();

});
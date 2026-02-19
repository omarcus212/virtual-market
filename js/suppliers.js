import suppliersService from './services/suppliers.js';
import response from './helpers/response.js';

document.addEventListener('DOMContentLoaded', () => {

    const loadSuppliers = () => {
        suppliersService.getAll()
            .done((res) => {
                if (res.success) {
                    renderTable(res.data);
                } else {
                    response.error(res.message);
                }
            })
            .fail(() => response.error('Erro de conexão!'));
    };

    const filterSuppliers = () => {
        var name = document.getElementById('filterName').value;
        var cnpj = document.getElementById('filterCnpj').value;
        var email = document.getElementById('filterEmail').value;

        var statusFilters = [];
        document.querySelectorAll('.filterStatus:checked').forEach((cb) => {
            statusFilters.push(cb.value);
        });

        // Se os 2 checkboxes estão marcados, manda vazio (= sem filtro de status)
        var status = statusFilters.length === 2 ? '' : statusFilters.join(',');

        suppliersService.filter({ name, cnpj, email, status })
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
        var tbody = document.getElementById('suppliersTableBody');

        tbody.innerHTML = '';

        if (suppliers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#6c757d;">
                        Nenhum fornecedor encontrado.
                    </td>
                </tr>
            `;
            return;
        }

        suppliers.forEach((supplier) => {
            var status = supplier.status == 1
                ? '<span class="status-badge status-active">Ativo</span>'
                : '<span class="status-badge status-inactive">Inativo</span>';

            var row = `
                <tr>
                    <td class="checkbox-cell">
                        <input type="checkbox" class="row-checkbox" data-id="${supplier.id}">
                    </td>
                    <td>${supplier.id}</td>
                    <td>${supplier.name}</td>
                    <td>${supplier.cnpj ?? '-'}</td>
                    <td>${supplier.email}</td>
                    <td>${supplier.phone ?? '-'}</td>
                    <td>${status}</td>
                    <td>${formatDate(supplier.created_at)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action" onclick="editSupplier(${supplier.id})">✏️ Editar</button>
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

    //  campos obrigatórios foram preenchidos
    const validateSupplier = () => {
        var name = document.getElementById('supplierName').value.trim();
        var cnpj = document.getElementById('supplierCnpj').value.trim();
        var email = document.getElementById('supplierEmail').value.trim();
        var phone = document.getElementById('supplierPhone').value.trim();
        var status = document.getElementById('supplierStatus').value;

        if (!name) { response.warning('Preencha o nome do fornecedor'); return false; }
        if (name.length < 3) { response.warning('O nome precisa ter pelo menos 3 caracteres'); return false; }
        if (!cnpj) { response.warning('Preencha o CNPJ do fornecedor'); return false; }
        if (!email) { response.warning('Preencha o email do fornecedor'); return false; }
        if (!phone) { response.warning('Preencha o telefone do fornecedor'); return false; }
        if (!status) { response.warning('Preencha o status do fornecedor'); return false; }

        return true;
    };

    document.getElementById('btnNewSupplier').addEventListener('click', () => {
        document.getElementById('modalTitle').textContent = 'Novo Fornecedor';
        document.getElementById('supplierForm').reset();
        document.getElementById('supplierId').value = '';
        document.getElementById('supplierModal').classList.add('show');
    });

    const closeModal = () => {
        document.getElementById('supplierModal').classList.remove('show');
    };

    // EDIT
    const editSupplier = (id) => {
        response.loading('Carregando...');

        suppliersService.getById(id)
            .done((res) => {
                response.closeLoading();

                if (res.success) {
                    var supplier = res.data[0];

                    document.getElementById('modalTitle').textContent = 'Editar Fornecedor';
                    document.getElementById('supplierId').value = supplier.id;
                    document.getElementById('supplierName').value = supplier.name;
                    document.getElementById('supplierCnpj').value = supplier.cnpj || '';
                    document.getElementById('supplierEmail').value = supplier.email || '';
                    document.getElementById('supplierPhone').value = supplier.phone || '';
                    document.getElementById('supplierStatus').value = supplier.status;
                    document.getElementById('supplierModal').classList.add('show');

                } else {
                    response.error(res.error || res.message);
                }
            })
            .fail((xhr) => {
                response.closeLoading();
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    response.error(xhr.responseJSON.errors);
                }
            });
    };

    // CREATE / UPDATE - Salva ou atualiza um fornecedor
    const saveSupplier = () => {
        if (!validateSupplier()) return;

        var supplierId = document.getElementById('supplierId').value;

        // Se supplierId está preenchido, é edição; se não, é criação
        var isEdit = supplierId !== '';

        var data = {
            name: document.getElementById('supplierName').value.trim(),
            cnpj: document.getElementById('supplierCnpj').value.trim(),
            email: document.getElementById('supplierEmail').value.trim(),
            phone: document.getElementById('supplierPhone').value.trim(),
            status: document.getElementById('supplierStatus').value,
        };

        response.loading('Salvando...');

        var promise = isEdit
            ? suppliersService.update(supplierId, data)
            : suppliersService.create(data);

        promise
            .done((res) => {
                response.closeLoading();

                if (res.success) {

                    response.success(res.message, isEdit ? 'Fornecedor atualizado!' : 'Fornecedor criado!');
                    closeModal();
                    loadSuppliers();

                } else {

                    response.error(res.errors);
                }
            })
            .fail((xhr) => {
                response.closeLoading();
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    response.error(xhr.responseJSON.errors);
                }
            });
    };

    document.getElementById('supplierModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeModal();
    });

    const bindCheckboxes = () => {
        document.querySelectorAll('.row-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', updateDeleteButton);
        });
    };

    // Marcar/desmarcar todos os checkboxes de uma vez
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach((cb) => cb.checked = this.checked);
        updateDeleteButton();
    });

    // Habilita ou desabilita o botão deletar conforme checkboxes marcados
    const updateDeleteButton = () => {
        var checked = document.querySelectorAll('.row-checkbox:checked').length;
        document.getElementById('btnDeleteSelected').disabled = checked === 0;
    };

    // DELETE - Deleta os fornecedores selecionados
    document.getElementById('btnDeleteSelected').addEventListener('click', () => {

        var ids = Array.from(document.querySelectorAll('.row-checkbox:checked'))
            .map((cb) => cb.getAttribute('data-id'));

        if (ids.length === 0) {
            response.warning('Selecione pelo menos um fornecedor');
            return;
        }

        response.confirmDelete(`Deletar ${ids.length} fornecedor(s)?`)
            .then((result) => {

                if (result.isConfirmed) {
                    response.loading('Deletando...');

                    suppliersService.delete(ids)
                        .done((res) => {
                            response.closeLoading();

                            if (res.success) {
                                response.success(res.message, 'Deletado!');
                                document.getElementById('selectAll').checked = false;
                                loadSuppliers();
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

    document.getElementById('btnApplyFilter').addEventListener('click', () => filterSuppliers());

    document.getElementById('btnClearFilter').addEventListener('click', () => {
        document.getElementById('filterName').value = '';
        document.getElementById('filterCnpj').value = '';
        document.getElementById('filterEmail').value = '';
        document.querySelectorAll('.filterStatus').forEach((filtro) => filtro.checked = false);
        loadSuppliers();
    });

    // MÁSCARAS - Formata o campo enquanto o usuário digita
    document.getElementById('supplierCnpj').addEventListener('input', function () {
        this.value = maskCnpj(this.value);
    });

    document.getElementById('supplierPhone').addEventListener('input', function () {
        this.value = maskPhone(this.value);
    });

    window.closeModal = closeModal;
    window.editSupplier = editSupplier;
    window.saveSupplier = saveSupplier;

    loadSuppliers();

});
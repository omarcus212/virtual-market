const suppliersService = {

    getAll: function () {
        return $.ajax({
            url: 'api/suppliers',
            method: 'GET',
            dataType: 'json'
        });
    },

    getById: function (id) {
        return $.ajax({
            url: 'api/suppliers/' + id,
            method: 'GET',
            dataType: 'json'
        });
    },

    create: function (data) {
        return $.ajax({
            url: 'api/suppliers',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json'
        });
    },

    update: function (id, data) {
        return $.ajax({
            url: 'api/suppliers/' + id,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json'
        });
    },

    delete: function (ids) {
        return $.ajax({
            url: 'api/suppliers',
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ data: ids }),
            dataType: 'json'
        });
    },

    filter: function (filters) {
        return $.ajax({
            url: 'api/suppliers',
            method: 'GET',
            data: {
                name: filters.name || '',
                cnpj: filters.cnpj || '',
                email: filters.email || '',
                status: filters.status ?? ''
            },
            dataType: 'json'
        });
    }

};

export default suppliersService;
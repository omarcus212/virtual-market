const relationService = {

    getAll: function () {
        return $.ajax({
            url: 'api/links',
            method: 'GET',
            dataType: 'json'
        });
    },

    create: function (data) {
        return $.ajax({
            url: 'api/links',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                supplier_id: data.supplier_id,
                product_ids: data.product_ids
            }),
            dataType: 'json'
        });
    },

    delete: function (ids) {
        return $.ajax({
            url: 'api/links',
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ data: ids }),
            dataType: 'json'
        });
    },

    filter: function (filters) {
        return $.ajax({
            url: 'api/links',
            method: 'GET',
            data: {
                supplier_name: filters.supplier_name || '',
                suppier_email: filters.suppier_email || '',
                supplier_cnpj: filters.supplier_cnpj || ''
            },
            dataType: 'json'
        });
    }

};
export default relationService;

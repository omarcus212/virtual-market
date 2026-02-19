const productService = {

    getAll: function () {
        return $.ajax({
            url: 'api/products',
            method: 'GET',
            dataType: 'json'
        });
    },

    getById: function (id) {
        return $.ajax({
            url: 'api/products/' + id,
            method: 'GET',
            dataType: 'json'
        });
    },

    create: function (data) {
        return $.ajax({
            url: 'api/products',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json'
        });
    },

    update: function (id, data) {
        return $.ajax({
            url: 'api/products/' + id,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json'
        });
    },

    delete: function (ids) {
        return $.ajax({
            url: 'api/products',
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ data: ids }),
            dataType: 'json'
        });
    },

    filter: function (filters) {
        return $.ajax({
            url: 'api/products',
            method: 'GET',
            data: {
                name: filters.name || '',
                code: filters.code || '',
                status: filters.status ?? ''
            },
            dataType: 'json'
        });
    }

};

export default productService;
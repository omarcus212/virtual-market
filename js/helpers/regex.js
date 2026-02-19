const validate = {

    email: function (value) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(value);
    },

    phone: function (value) {
        const regex = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
        return regex.test(value);
    },

    cnpj: function (value) {
        const regex = /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/;
        return regex.test(value);
    }

};
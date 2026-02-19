const response = {

    loading: function (message = 'Carregando...') {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    closeLoading: function () {
        Swal.close();
    },

    success: function (message, title = 'Sucesso!') {
        Swal.fire({
            toast: true,
            icon: 'success',
            title: title,
            text: message,
            timer: 2000,
            position: "top-end",
            showConfirmButton: false
        });
    },

    error: function (message, title = 'Erro!') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: title,
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    },

    confirmDelete: function (message = 'Essa ação não pode ser desfeita!') {
        return Swal.fire({
            icon: 'warning',
            title: 'Tem certeza?',
            text: message,
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, deletar!',
            cancelButtonText: 'Cancelar'
        });
    },

    warning: function (message, title = 'Atenção!') {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: title,
            text: message,
            position: "top-end",
            timer: 2000,
            showConfirmButton: false
        });
    }

};

export default response;
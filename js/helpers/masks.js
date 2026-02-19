// CNPJ - 00.000.000/0000-00
function maskCnpj(value) {
    return value
        .replace(/\D/g, '')
        .slice(0, 14)
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2');
}

// PHONE - (00) 00000-0000
function maskPhone(value) {
    return value
        .replace(/\D/g, '')
        .slice(0, 11)
        .replace(/^(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{5})(\d)/, '$1-$2');
}

document.getElementById('filterCnpj').addEventListener('input', function () {
    this.value = maskCnpj(this.value);
});

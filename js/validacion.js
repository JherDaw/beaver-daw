// Validación en el navegador de los formularios de registro e inicio de sesión.
// Es solo una ayuda para el usuario: el servidor vuelve a validar todo.

document.addEventListener('DOMContentLoaded', () => {
    const formularios = document.querySelectorAll('#form-registro, #form-login');

    formularios.forEach((form) => {
        form.addEventListener('submit', (evento) => {
            limpiarErrores(form);
            let valido = true;

            form.querySelectorAll('input[required]').forEach((campo) => {
                if (!campo.checkValidity()) {
                    valido = false;
                    marcarError(campo, mensajePara(campo));
                }
            });

            if (!valido) {
                evento.preventDefault();
                form.querySelector('.campo--error')?.focus();
            }
        });
    });
});

function mensajePara(campo) {
    if (campo.validity.valueMissing) return 'Este campo es obligatorio.';
    if (campo.type === 'email') return 'Escribe un correo válido.';
    if (campo.validity.tooShort) return `Mínimo ${campo.minLength} caracteres.`;
    return 'Revisa este campo.';
}

function marcarError(campo, mensaje) {
    campo.classList.add('campo--error');
    campo.setAttribute('aria-invalid', 'true');
    const aviso = document.createElement('small');
    aviso.className = 'error-campo';
    aviso.textContent = mensaje;
    campo.insertAdjacentElement('afterend', aviso);
}

function limpiarErrores(form) {
    form.querySelectorAll('.error-campo').forEach((n) => n.remove());
    form.querySelectorAll('.campo--error').forEach((c) => {
        c.classList.remove('campo--error');
        c.removeAttribute('aria-invalid');
    });
}

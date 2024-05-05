document.addEventListener('DOMContentLoaded', function() {
    const loginContainer = document.getElementById('loginContainer');
    const registerContainer = document.getElementById('registerContainer');

    document.getElementById('toggleBtn').addEventListener('click', function() {
        if (loginContainer.style.display !== 'none') {
            loginContainer.style.display = 'none';
            registerContainer.style.display = 'flex';
        } else {
            loginContainer.style.display = 'flex';
            registerContainer.style.display = 'none';
        }
    });
});

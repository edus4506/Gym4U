let input = document.getElementById('password');
let eyeIcon = document.getElementById('eye-icon');
if(input && eyeIcon) {
    eyeIcon.addEventListener('click', function () {
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.textContent = '🙉';
    } else {
        input.type = 'password';
        eyeIcon.textContent = '👁️';
    }
});
}

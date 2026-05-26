document.querySelectorAll('.password-wrapper').forEach(wrapper => {
    const input = wrapper.querySelector('input');
    const eye = wrapper.querySelector('.eye-icon');
    if (!input || !eye) return;

    eye.addEventListener('click', () => {
        if (input.type === 'password') {
            input.type = 'text';
            eye.textContent = '🙉';
        } else {
            input.type = 'password';
            eye.textContent = '👁️';
        }
    });
});

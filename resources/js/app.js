import './bootstrap';

document.querySelectorAll('[data-password-toggle-button]').forEach((button) => {
    const input = button.parentElement.querySelector('[data-password-toggle]');
    if (!input) return;

    button.addEventListener('click', () => {
        const reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
        button.setAttribute('aria-pressed', String(reveal));
    });
});

const namePattern = /^[\p{L}]+(?:[ '\-][\p{L}]+)*$/u;

const removeDisallowedCharacters = (input, disallowedPattern) => {
    const cursor = input.selectionStart ?? input.value.length;
    const beforeCursor = input.value.slice(0, cursor);
    const cleanedValue = input.value.replace(disallowedPattern, '');

    if (cleanedValue !== input.value) {
        input.value = cleanedValue;
        const nextCursor = beforeCursor.replace(disallowedPattern, '').length;
        input.setSelectionRange(nextCursor, nextCursor);
    }
};

document.querySelectorAll('[data-name-only]').forEach((input) => {
    const validateName = () => {
        removeDisallowedCharacters(input, /[^\p{L}\s'-]/gu);
        const value = input.value.trim();
        const valid = value === '' && !input.required ? true : namePattern.test(value);
        input.setCustomValidity(valid ? '' : 'Use letters only; spaces, apostrophes, and hyphens are allowed.');
    };

    input.addEventListener('input', validateName);
    validateName();
});

document.querySelectorAll('[data-digits-only]').forEach((input) => {
    const validateDigits = () => {
        removeDisallowedCharacters(input, /[^0-9]/g);
        const valid = /^[0-9]{7,15}$/.test(input.value);
        input.setCustomValidity(valid ? '' : 'Enter 7 to 15 digits only, without spaces or symbols.');
    };

    input.addEventListener('input', validateDigits);
    validateDigits();
});

const logoutDialog = document.querySelector('#logout-dialog');
const logoutOpenButton = document.querySelector('[data-logout-open]');

if (logoutDialog && logoutOpenButton) {
    logoutOpenButton.addEventListener('click', () => logoutDialog.showModal());
    logoutDialog.querySelector('[data-logout-close]')?.addEventListener('click', () => logoutDialog.close());
    logoutDialog.addEventListener('click', (event) => {
        if (event.target === logoutDialog) logoutDialog.close();
    });
}

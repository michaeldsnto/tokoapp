import './bootstrap';

let deferredInstallPrompt = null;

window.promptPwaInstall = async () => {
    if (!deferredInstallPrompt) {
        return false;
    }

    deferredInstallPrompt.prompt();
    const choice = await deferredInstallPrompt.userChoice;
    deferredInstallPrompt = null;
    document.getElementById('install-app-button')?.classList.remove('ready');

    return choice.outcome === 'accepted';
};

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    window.dispatchEvent(new CustomEvent('tokoapp:pwa-installable'));
});

window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;
    document.getElementById('install-app-button')?.classList.remove('ready');
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            await navigator.serviceWorker.register('/sw.js');
        } catch (error) {
            console.error('Service worker registration failed.', error);
        }
    });
}

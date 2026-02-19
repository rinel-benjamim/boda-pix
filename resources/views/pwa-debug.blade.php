<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PWA Debug - BodaPix</title>
    <link rel="manifest" href="/manifest.json">
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 PWA Debug - BodaPix</h1>
    
    <div id="status"></div>
    
    <h2>Ações</h2>
    <button onclick="checkPWASupport()">Verificar Suporte PWA</button>
    <button onclick="registerServiceWorker()">Registar Service Worker</button>
    <button onclick="installPWA()">Instalar PWA</button>
    <button onclick="checkManifest()">Verificar Manifest</button>
    
    <h2>Logs</h2>
    <pre id="logs"></pre>

    <script>
        let deferredPrompt = null;
        const statusDiv = document.getElementById('status');
        const logsDiv = document.getElementById('logs');

        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            logsDiv.textContent += `[${timestamp}] ${message}\n`;
            console.log(message);
        }

        function showStatus(message, type = 'info') {
            statusDiv.innerHTML = `<div class="status ${type}">${message}</div>`;
        }

        // Capturar evento beforeinstallprompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            log('✅ beforeinstallprompt event captured!', 'success');
            showStatus('PWA pode ser instalado! Clique em "Instalar PWA"', 'success');
        });

        function checkPWASupport() {
            log('Verificando suporte PWA...');
            
            const checks = {
                'Service Worker': 'serviceWorker' in navigator,
                'HTTPS': location.protocol === 'https:' || location.hostname === 'localhost',
                'Manifest': document.querySelector('link[rel="manifest"]') !== null,
                'Standalone Mode': window.matchMedia('(display-mode: standalone)').matches,
                'beforeinstallprompt': deferredPrompt !== null
            };

            let html = '<h3>Verificação de Suporte:</h3><ul>';
            for (const [check, result] of Object.entries(checks)) {
                html += `<li>${result ? '✅' : '❌'} ${check}: ${result}</li>`;
                log(`${check}: ${result}`);
            }
            html += '</ul>';
            
            statusDiv.innerHTML = html;
        }

        async function registerServiceWorker() {
            if ('serviceWorker' in navigator) {
                try {
                    const registration = await navigator.serviceWorker.register('/sw.js');
                    log('✅ Service Worker registado: ' + registration.scope);
                    showStatus('Service Worker registado com sucesso!', 'success');
                } catch (error) {
                    log('❌ Erro ao registar Service Worker: ' + error.message);
                    showStatus('Erro ao registar Service Worker: ' + error.message, 'error');
                }
            } else {
                log('❌ Service Worker não suportado');
                showStatus('Service Worker não suportado neste navegador', 'error');
            }
        }

        async function installPWA() {
            log('Tentando instalar PWA...');
            
            if (deferredPrompt) {
                try {
                    await deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    log(`Resultado da instalação: ${outcome}`);
                    
                    if (outcome === 'accepted') {
                        showStatus('PWA instalado com sucesso!', 'success');
                    } else {
                        showStatus('Instalação cancelada pelo utilizador', 'info');
                    }
                    
                    deferredPrompt = null;
                } catch (error) {
                    log('❌ Erro ao instalar: ' + error.message);
                    showStatus('Erro ao instalar: ' + error.message, 'error');
                }
            } else {
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
                
                if (isStandalone) {
                    showStatus('A aplicação já está instalada!', 'info');
                    log('Aplicação já instalada');
                } else if (isIOS) {
                    showStatus('iOS: Use Safari > Partilhar > Adicionar ao Ecrã Principal', 'info');
                    log('Instruções iOS mostradas');
                } else {
                    showStatus('Prompt não disponível. Use o menu do navegador para instalar.', 'info');
                    log('Prompt não disponível');
                }
            }
        }

        async function checkManifest() {
            try {
                const response = await fetch('/manifest.json');
                const manifest = await response.json();
                log('Manifest carregado com sucesso');
                statusDiv.innerHTML = '<h3>Manifest.json:</h3><pre>' + JSON.stringify(manifest, null, 2) + '</pre>';
            } catch (error) {
                log('❌ Erro ao carregar manifest: ' + error.message);
                showStatus('Erro ao carregar manifest: ' + error.message, 'error');
            }
        }

        // Auto-check ao carregar
        window.addEventListener('load', () => {
            log('Página carregada');
            checkPWASupport();
            registerServiceWorker();
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWA Debug - BodaPix</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #0F172A;
            color: #fff;
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            background: #1E293B;
        }
        .success { border-left: 4px solid #10b981; }
        .error { border-left: 4px solid #ef4444; }
        .warning { border-left: 4px solid #f59e0b; }
        button {
            background: #FF5A1F;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        button:hover {
            background: #E11D48;
        }
        pre {
            background: #000;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 PWA Diagnóstico - BodaPix</h1>
    
    <div id="results"></div>
    
    <button onclick="runTests()">Executar Testes</button>
    <button onclick="installPWA()">Instalar PWA</button>
    <button onclick="location.href='/'">Voltar ao App</button>

    <script>
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            addResult('✅ Evento beforeinstallprompt detectado - App pode ser instalado!', 'success');
        });

        window.addEventListener('appinstalled', () => {
            addResult('✅ App instalado com sucesso!', 'success');
            deferredPrompt = null;
        });

        function addResult(message, type = 'status') {
            const div = document.createElement('div');
            div.className = `status ${type}`;
            div.innerHTML = message;
            document.getElementById('results').appendChild(div);
        }

        async function runTests() {
            document.getElementById('results').innerHTML = '';
            
            addResult('<h2>🧪 Iniciando Testes...</h2>');

            // Test 1: HTTPS
            if (location.protocol === 'https:' || location.hostname === 'localhost') {
                addResult('✅ HTTPS: OK', 'success');
            } else {
                addResult('❌ HTTPS: Necessário para PWA (exceto localhost)', 'error');
            }

            // Test 2: Service Worker Support
            if ('serviceWorker' in navigator) {
                addResult('✅ Service Worker: Suportado', 'success');
                
                try {
                    const registration = await navigator.serviceWorker.getRegistration();
                    if (registration) {
                        addResult(`✅ Service Worker: Registrado (${registration.active ? 'Ativo' : 'Inativo'})`, 'success');
                    } else {
                        addResult('⚠️ Service Worker: Não registrado ainda', 'warning');
                        // Try to register
                        const reg = await navigator.serviceWorker.register('/sw.js');
                        addResult('✅ Service Worker: Registrado agora!', 'success');
                    }
                } catch (error) {
                    addResult(`❌ Service Worker: Erro - ${error.message}`, 'error');
                }
            } else {
                addResult('❌ Service Worker: Não suportado neste navegador', 'error');
            }

            // Test 3: Manifest
            try {
                const response = await fetch('/manifest.json');
                if (response.ok) {
                    const manifest = await response.json();
                    addResult('✅ Manifest: Encontrado', 'success');
                    addResult(`<pre>${JSON.stringify(manifest, null, 2)}</pre>`);
                } else {
                    addResult('❌ Manifest: Não encontrado (404)', 'error');
                }
            } catch (error) {
                addResult(`❌ Manifest: Erro ao carregar - ${error.message}`, 'error');
            }

            // Test 4: Icons
            const icons = ['/icon-192.png', '/icon-512.png'];
            for (const icon of icons) {
                try {
                    const response = await fetch(icon);
                    if (response.ok) {
                        addResult(`✅ Ícone ${icon}: OK`, 'success');
                    } else {
                        addResult(`❌ Ícone ${icon}: Não encontrado`, 'error');
                    }
                } catch (error) {
                    addResult(`❌ Ícone ${icon}: Erro - ${error.message}`, 'error');
                }
            }

            // Test 5: Install Prompt
            if (deferredPrompt) {
                addResult('✅ Prompt de Instalação: Disponível', 'success');
            } else {
                addResult('⚠️ Prompt de Instalação: Não disponível (pode já estar instalado ou navegador não suporta)', 'warning');
            }

            // Test 6: Display Mode
            if (window.matchMedia('(display-mode: standalone)').matches) {
                addResult('✅ Display Mode: Standalone (App já instalado!)', 'success');
            } else {
                addResult('ℹ️ Display Mode: Browser (App não instalado)', 'status');
            }

            // Test 7: Browser Info
            addResult(`<h3>📱 Informações do Navegador</h3>
                <pre>User Agent: ${navigator.userAgent}
Platform: ${navigator.platform}
Online: ${navigator.onLine}
Cookies Enabled: ${navigator.cookieEnabled}</pre>`);
        }

        async function installPWA() {
            if (!deferredPrompt) {
                addResult('⚠️ Prompt de instalação não disponível. Possíveis razões:<br>- App já está instalado<br>- Navegador não suporta<br>- Critérios PWA não atendidos', 'warning');
                return;
            }

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                addResult('✅ Usuário aceitou a instalação!', 'success');
            } else {
                addResult('❌ Usuário recusou a instalação', 'error');
            }
            
            deferredPrompt = null;
        }

        // Auto-run tests on load
        window.addEventListener('load', () => {
            setTimeout(runTests, 500);
        });
    </script>
</body>
</html>

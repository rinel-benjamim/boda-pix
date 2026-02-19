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
    
    <div style="background: #1E293B; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2>📥 Instalar BodaPix</h2>
        <p id="install-status">Verificando disponibilidade...</p>
        <button id="install-button" onclick="installPWA()" style="display: none;">📥 Instalar Agora</button>
        <div id="install-instructions" style="display: none; margin-top: 15px; padding: 15px; background: #0F172A; border-radius: 6px;">
            <!-- Instruções serão inseridas aqui -->
        </div>
    </div>
    
    <div id="results"></div>
    
    <button onclick="runTests()">Executar Testes</button>
    <button onclick="location.href='/'">Voltar ao App</button>

    <script>
        let deferredPrompt;

        // Detectar se já está instalado
        function isInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches ||
                   window.navigator.standalone === true;
        }

        // Detectar plataforma
        function getPlatform() {
            const ua = navigator.userAgent.toLowerCase();
            if (/iphone|ipad|ipod/.test(ua)) return 'ios';
            if (/android/.test(ua)) return 'android';
            return 'desktop';
        }

        // Mostrar instruções específicas da plataforma
        function showPlatformInstructions() {
            const platform = getPlatform();
            const instructionsDiv = document.getElementById('install-instructions');
            const statusP = document.getElementById('install-status');
            
            if (isInstalled()) {
                statusP.innerHTML = '✅ <strong>BodaPix já está instalado!</strong>';
                return;
            }

            let instructions = '';
            
            if (platform === 'ios') {
                instructions = `
                    <h3>📱 Instalar no iOS (Safari)</h3>
                    <ol style="text-align: left; line-height: 1.8;">
                        <li>Toque no botão <strong>Compartilhar</strong> (□↑) na barra inferior</li>
                        <li>Role para baixo e toque em <strong>"Adicionar à Tela de Início"</strong></li>
                        <li>Toque em <strong>"Adicionar"</strong></li>
                    </ol>
                    <p style="margin-top: 10px; color: #f59e0b;">⚠️ Nota: Use o Safari, não Chrome no iOS</p>
                `;
                statusP.innerHTML = '📱 Siga as instruções abaixo para instalar no iOS:';
            } else if (platform === 'android') {
                instructions = `
                    <h3>📱 Instalar no Android</h3>
                    <ol style="text-align: left; line-height: 1.8;">
                        <li>Toque no menu (⋮) no canto superior direito</li>
                        <li>Selecione <strong>"Adicionar à tela inicial"</strong> ou <strong>"Instalar app"</strong></li>
                        <li>Toque em <strong>"Instalar"</strong></li>
                    </ol>
                    <p style="margin-top: 10px; color: #10b981;">💡 Ou aguarde o banner de instalação aparecer automaticamente</p>
                `;
                statusP.innerHTML = '📱 Siga as instruções abaixo para instalar no Android:';
            } else {
                instructions = `
                    <h3>💻 Instalar no Desktop</h3>
                    <ol style="text-align: left; line-height: 1.8;">
                        <li>Clique no ícone <strong>➕</strong> ou <strong>🖥️</strong> na barra de endereço</li>
                        <li>Ou vá em Menu → <strong>"Instalar BodaPix"</strong></li>
                        <li>Clique em <strong>"Instalar"</strong></li>
                    </ol>
                    <p style="margin-top: 10px; color: #10b981;">💡 Também pode usar o botão abaixo se disponível</p>
                `;
                statusP.innerHTML = '💻 Siga as instruções abaixo para instalar no Desktop:';
            }
            
            instructionsDiv.innerHTML = instructions;
            instructionsDiv.style.display = 'block';
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            const installButton = document.getElementById('install-button');
            const statusP = document.getElementById('install-status');
            const instructionsDiv = document.getElementById('install-instructions');
            
            installButton.style.display = 'inline-block';
            statusP.innerHTML = '✅ <strong>BodaPix está pronto para instalar!</strong>';
            instructionsDiv.style.display = 'none';
        });

        window.addEventListener('appinstalled', () => {
            const statusP = document.getElementById('install-status');
            const installButton = document.getElementById('install-button');
            const instructionsDiv = document.getElementById('install-instructions');
            
            statusP.innerHTML = '✅ <strong>BodaPix instalado com sucesso!</strong>';
            installButton.style.display = 'none';
            instructionsDiv.style.display = 'none';
            
            setTimeout(() => {
                location.href = '/';
            }, 2000);
        });

        async function installPWA() {
            if (!deferredPrompt) {
                showPlatformInstructions();
                return;
            }

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            const statusP = document.getElementById('install-status');
            
            if (outcome === 'accepted') {
                statusP.innerHTML = '✅ <strong>Instalando BodaPix...</strong>';
            } else {
                statusP.innerHTML = '❌ Instalação cancelada. Tente novamente quando quiser!';
            }
            
            deferredPrompt = null;
            document.getElementById('install-button').style.display = 'none';
        }

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

        // Auto-run on load
        window.addEventListener('load', () => {
            if (isInstalled()) {
                document.getElementById('install-status').innerHTML = '✅ <strong>BodaPix já está instalado!</strong>';
            } else if (!deferredPrompt) {
                setTimeout(showPlatformInstructions, 1000);
            }
        });
    </script>
</body>
</html>

#!/bin/bash

# Script para iniciar BodaPix com HTTPS (via Ngrok)

echo "🚀 Iniciando BodaPix com HTTPS..."
echo ""

# Verificar se ngrok está instalado
if ! command -v ngrok &> /dev/null; then
    echo "❌ Ngrok não está instalado!"
    echo ""
    echo "Instale com:"
    echo "  npm install -g ngrok"
    echo "  ou"
    echo "  brew install ngrok (macOS)"
    echo ""
    exit 1
fi

# Iniciar servidor Laravel em background
echo "📦 Iniciando servidor Laravel..."
php artisan serve > /dev/null 2>&1 &
LARAVEL_PID=$!

# Aguardar servidor iniciar
sleep 3

# Iniciar Ngrok
echo "🌐 Criando túnel HTTPS..."
echo ""
ngrok http 8000

# Cleanup ao sair
trap "kill $LARAVEL_PID" EXIT

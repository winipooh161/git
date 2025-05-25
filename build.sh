#!/bin/bash
# Скрипт для сборки front-end ресурсов через Vite

# Установка зависимостей npm, если они еще не установлены
if [ ! -d "node_modules" ]; then
    echo "Installing npm dependencies..."
    npm install
fi

# Сборка ресурсов для продакшена
echo "Building front-end resources..."
npm run build

echo "Done! Front-end resources have been compiled."

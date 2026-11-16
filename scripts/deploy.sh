#!/usr/bin/env bash

set -euo pipefail

# Localizar a raiz do projeto
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

COMPOSE_FILE="$PROJECT_ROOT/deploy/compose.yaml"

echo "======================================"
echo " TaskFlow - Deploy com Docker Compose"
echo "======================================"

# Verificar se Docker está disponível
if ! docker info > /dev/null 2>&1; then
    echo "ERRO: Docker Engine não está disponível."
    exit 1
fi

# Verificar se Docker Compose está disponível
if ! docker compose version > /dev/null 2>&1; then
    echo "ERRO: Docker Compose não está disponível."
    exit 1
fi

echo ""
echo "[1/3] Baixando imagem do GHCR..."

docker compose -f "$COMPOSE_FILE" pull

echo ""
echo "[2/3] Iniciando aplicação..."

docker compose -f "$COMPOSE_FILE" up -d

echo ""
echo "[3/3] Verificando contêiner..."

docker compose -f "$COMPOSE_FILE" ps

echo ""
echo "Deploy solicitado com sucesso."
echo "Acesse: http://localhost:8081/tasks"
echo "Confira os logs para validar a inicialização."
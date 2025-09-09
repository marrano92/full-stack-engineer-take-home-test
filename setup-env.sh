#!/bin/bash

# Script to setup environment files
# Usage: ./setup-env.sh [local|production]

ENV_TYPE=${1:-local}

if [ "$ENV_TYPE" != "local" ] && [ "$ENV_TYPE" != "production" ]; then
    echo "Error: Invalid environment type. Use 'local' or 'production'"
    echo "Usage: $0 [local|production]"
    exit 1
fi

SOURCE_FILE="envs/.env.${ENV_TYPE}"
TARGET_FILE=".env"

if [ ! -f "$SOURCE_FILE" ]; then
    echo "Error: Source file $SOURCE_FILE does not exist"
    exit 1
fi

echo "Setting up environment for: $ENV_TYPE"
echo "Copying $SOURCE_FILE to $TARGET_FILE"

cp "$SOURCE_FILE" "$TARGET_FILE"

if [ $? -eq 0 ]; then
    echo "Environment file copied successfully"
    
    # Generate APP_KEY if it's empty or not present
    if ! grep -q "^APP_KEY=.\+$" "$TARGET_FILE"; then
        echo "Generating Laravel application key..."
        php artisan key:generate --no-interaction
        echo "Application key generated successfully"
    else
        echo "Application key already exists"
    fi
    
    echo "Environment setup completed successfully"
    echo "Current environment: $ENV_TYPE"
else
    echo "Error: Failed to copy environment file"
    exit 1
fi
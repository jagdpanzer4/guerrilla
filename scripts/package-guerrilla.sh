#!/usr/bin/env bash
# Builds a deployable zip of the guerrilla package for shared hosting.
# Usage: called by `npm run package:build` from packages/guerrilla/
# Output: guerrilla-v{version}.zip at project root

set -e
set -o pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PKG_DIR="$PROJECT_ROOT/packages/guerrilla"

# Read version string from controller.php  e.g. '1.0.0'
VERSION=$(grep "pkgVersion" "$PKG_DIR/controller.php" | grep -oE "[0-9]+\.[0-9]+\.[0-9]+")

if [ -z "$VERSION" ]; then
    echo "ERROR: Could not read version from controller.php" >&2
    exit 1
fi

ZIP_NAME="guerrilla-v${VERSION}.zip"
ZIP_PATH="$PROJECT_ROOT/$ZIP_NAME"

echo "Packaging guerrilla v${VERSION} → ${ZIP_NAME}"

rm -f "$ZIP_PATH"

cd "$PROJECT_ROOT/packages"
zip -r "$ZIP_PATH" guerrilla \
    --exclude "guerrilla/node_modules/*" \
    --exclude "guerrilla/src/*" \
    --exclude "guerrilla/vite.config.js" \
    --exclude "guerrilla/package.json" \
    --exclude "guerrilla/package-lock.json"

echo "Done: $ZIP_PATH"

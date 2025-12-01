#!/bin/bash

# Spectrum GRAP Extension Installation Script
# For AtoM 2.x+

echo "======================================"
echo "Spectrum GRAP Extension Installer"
echo "======================================"
echo ""

# Check if AtoM directory is provided
if [ -z "$1" ]; then
  echo "Usage: ./install.sh /path/to/atom"
  echo "Example: ./install.sh /usr/share/nginx/atom"
  exit 1
fi

ATOM_DIR="$1"
PLUGIN_NAME="arSpectrum51Plugin"

# Check if AtoM directory exists
if [ ! -d "$ATOM_DIR" ]; then
  echo "Error: AtoM directory not found: $ATOM_DIR"
  exit 1
fi

# Check if base Spectrum plugin exists
if [ ! -d "$ATOM_DIR/plugins/$PLUGIN_NAME" ]; then
  echo "Error: Base Spectrum plugin not found at $ATOM_DIR/plugins/$PLUGIN_NAME"
  echo "Please install arSpectrum51Plugin first"
  exit 1
fi

echo "Installing GRAP extension to: $ATOM_DIR"
echo ""

# Copy files
echo "Copying GRAP extension files..."

# Create directories if they don't exist
mkdir -p "$ATOM_DIR/plugins/$PLUGIN_NAME/config/doctrine"
mkdir -p "$ATOM_DIR/plugins/$PLUGIN_NAME/lib/model"
mkdir -p "$ATOM_DIR/plugins/$PLUGIN_NAME/lib/form"
mkdir -p "$ATOM_DIR/plugins/$PLUGIN_NAME/modules/grapReport/actions"
mkdir -p "$ATOM_DIR/plugins/$PLUGIN_NAME/modules/grapReport/templates"
mkdir -p "$ATOM_DIR/plugins/$PLUGIN_NAME/modules/api/actions"

# Copy schema
if [ -f "config/doctrine/schema.yml" ]; then
  # Append to existing schema or create new
  if [ -f "$ATOM_DIR/plugins/$PLUGIN_NAME/config/doctrine/schema.yml" ]; then
    echo "" >> "$ATOM_DIR/plugins/$PLUGIN_NAME/config/doctrine/schema.yml"
    cat config/doctrine/schema.yml >> "$ATOM_DIR/plugins/$PLUGIN_NAME/config/doctrine/schema.yml"
    echo "GRAP schema appended to existing schema.yml"
  else
    cp config/doctrine/schema.yml "$ATOM_DIR/plugins/$PLUGIN_NAME/config/doctrine/"
    echo "GRAP schema copied"
  fi
fi

# Copy model files
cp -r lib/model/* "$ATOM_DIR/plugins/$PLUGIN_NAME/lib/model/" 2>/dev/null || true
echo "Model files copied"

# Copy form files
cp -r lib/form/* "$ATOM_DIR/plugins/$PLUGIN_NAME/lib/form/" 2>/dev/null || true
echo "Form files copied"

# Copy module files
cp -r modules/* "$ATOM_DIR/plugins/$PLUGIN_NAME/modules/" 2>/dev/null || true
echo "Module files copied"

# Copy config files
if [ -f "config/routing.yml" ]; then
  cp config/routing.yml "$ATOM_DIR/plugins/$PLUGIN_NAME/config/grap_routing.yml"
  echo "Routing configuration copied"
fi

echo ""
echo "Files copied successfully!"
echo ""

# Build schema
echo "Building database schema..."
cd "$ATOM_DIR"

php symfony doctrine:build --all-classes
if [ $? -ne 0 ]; then
  echo "Error building classes"
  exit 1
fi

php symfony propel:build-model
if [ $? -ne 0 ]; then
  echo "Error building model"
  exit 1
fi

echo ""
echo "Creating database tables..."
php symfony doctrine:insert-sql --application=qubit

echo ""
echo "Clearing cache..."
php symfony cc

echo ""
echo "======================================"
echo "Installation Complete!"
echo "======================================"
echo ""
echo "Next steps:"
echo "1. Configure API key in apps/qubit/config/app.yml"
echo "2. Access GRAP reports at: http://yoursite/grap/report"
echo "3. API documentation at: http://yoursite/api/docs"
echo ""
echo "For more information, see README.md"
echo ""

#!/bin/bash

# Test script for isolated package installation via Composer
# This ensures the package can be installed correctly from Packagist

set -e

echo "================================================"
echo "Testing SortedLinkedList Package Installation"
echo "================================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Create temporary test directory
TEMP_DIR=$(mktemp -d)
echo -e "${YELLOW}Creating test environment in: $TEMP_DIR${NC}"

# Cleanup function
cleanup() {
    echo ""
    echo -e "${YELLOW}Cleaning up test environment...${NC}"
    rm -rf "$TEMP_DIR"
}

# Register cleanup on exit
trap cleanup EXIT

# Navigate to temp directory
cd "$TEMP_DIR"

# Test 1: Initialize new Composer project
echo ""
echo -e "${YELLOW}Test 1: Initializing Composer project...${NC}"
cat > composer.json <<EOF
{
    "name": "test/sortedlinkedlist-installation",
    "description": "Test installation of sortedlinkedlist package",
    "type": "project",
    "require": {
        "php": "^8.1"
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
EOF

# Test 2: Add local package repository (for pre-Packagist testing)
echo ""
echo -e "${YELLOW}Test 2: Configuring local package repository...${NC}"

# Get the absolute path to the package
PACKAGE_DIR="/Users/uniacid/Sites/SyncDev/SortedLinkedList"

# Add the local repository to composer.json
php -r '
$config = json_decode(file_get_contents("composer.json"), true);
$config["repositories"] = [
    [
        "type" => "path",
        "url" => "'$PACKAGE_DIR'",
        "options" => [
            "symlink" => false
        ]
    ]
];
file_put_contents("composer.json", json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
'

# Test 3: Install the package
echo ""
echo -e "${YELLOW}Test 3: Installing sortedlinkedlist package...${NC}"
composer require uniacid/sortedlinkedlist:@dev --no-interaction

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Package installed successfully${NC}"
else
    echo -e "${RED}✗ Package installation failed${NC}"
    exit 1
fi

# Test 4: Verify autoloading
echo ""
echo -e "${YELLOW}Test 4: Verifying autoloading...${NC}"
cat > test_autoload.php <<'EOF'
<?php
require_once 'vendor/autoload.php';

try {
    // Test that classes can be loaded
    $classes = [
        'SortedLinkedList\\Node',
        'SortedLinkedList\\SortedLinkedList',
        'SortedLinkedList\\IntegerSortedLinkedList',
        'SortedLinkedList\\StringSortedLinkedList',
        'SortedLinkedList\\FloatSortedLinkedList',
    ];

    foreach ($classes as $class) {
        if (!class_exists($class)) {
            throw new Exception("Class {$class} could not be loaded");
        }
    }

    echo "✓ All classes loaded successfully\n";
    exit(0);
} catch (Exception $e) {
    echo "✗ Autoloading failed: " . $e->getMessage() . "\n";
    exit(1);
}
EOF

php test_autoload.php
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Autoloading verified${NC}"
else
    echo -e "${RED}✗ Autoloading verification failed${NC}"
    exit 1
fi

# Test 5: Basic functionality test
echo ""
echo -e "${YELLOW}Test 5: Testing basic functionality...${NC}"
cat > test_functionality.php <<'EOF'
<?php
require_once 'vendor/autoload.php';

use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;

try {
    // Test integer sorted list
    $intList = new IntegerSortedLinkedList();
    $intList->add(5);
    $intList->add(2);
    $intList->add(8);
    $intList->add(1);

    if ($intList->toArray() !== [1, 2, 5, 8]) {
        throw new Exception("Integer sorting failed");
    }

    // Test string sorted list
    $stringList = new StringSortedLinkedList();
    $stringList->add("banana");
    $stringList->add("apple");
    $stringList->add("cherry");

    if ($stringList->toArray() !== ["apple", "banana", "cherry"]) {
        throw new Exception("String sorting failed");
    }

    echo "✓ Basic functionality works correctly\n";
    exit(0);
} catch (Exception $e) {
    echo "✗ Functionality test failed: " . $e->getMessage() . "\n";
    exit(1);
}
EOF

php test_functionality.php
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Functionality test passed${NC}"
else
    echo -e "${RED}✗ Functionality test failed${NC}"
    exit 1
fi

# Test 6: Check installed package metadata
echo ""
echo -e "${YELLOW}Test 6: Verifying package metadata...${NC}"
composer show uniacid/sortedlinkedlist

# Test 7: Validate no dev dependencies were installed
echo ""
echo -e "${YELLOW}Test 7: Verifying no dev dependencies...${NC}"
if [ -d "vendor/phpunit" ]; then
    echo -e "${RED}✗ Dev dependencies were incorrectly installed${NC}"
    exit 1
else
    echo -e "${GREEN}✓ No dev dependencies installed (correct)${NC}"
fi

# Test 8: Check package files exist
echo ""
echo -e "${YELLOW}Test 8: Checking package structure...${NC}"
REQUIRED_FILES=(
    "vendor/uniacid/sortedlinkedlist/composer.json"
    "vendor/uniacid/sortedlinkedlist/src/Node.php"
    "vendor/uniacid/sortedlinkedlist/src/SortedLinkedList.php"
    "vendor/uniacid/sortedlinkedlist/src/IntegerSortedLinkedList.php"
    "vendor/uniacid/sortedlinkedlist/src/StringSortedLinkedList.php"
    "vendor/uniacid/sortedlinkedlist/src/FloatSortedLinkedList.php"
    "vendor/uniacid/sortedlinkedlist/LICENSE"
    "vendor/uniacid/sortedlinkedlist/README.md"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file exists${NC}"
    else
        echo -e "${RED}✗ $file missing${NC}"
        exit 1
    fi
done

# Summary
echo ""
echo "================================================"
echo -e "${GREEN}All installation tests passed successfully!${NC}"
echo "================================================"
echo ""
echo "The package is ready for Packagist submission."
echo "Once published, users can install with:"
echo "  composer require uniacid/sortedlinkedlist"
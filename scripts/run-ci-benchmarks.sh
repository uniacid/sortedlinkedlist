#!/bin/bash

# CI Benchmark Runner Script with Progress Indicators
# This script runs benchmarks with periodic progress updates to prevent CI timeouts

set -e

echo "=========================================="
echo "CI Benchmark Runner"
echo "=========================================="
echo "Environment: ${CI:-local}"
echo "PHP Version: $(php -v | head -n 1)"
echo "Memory Limit: $(php -r 'echo ini_get("memory_limit");')"
echo "=========================================="

# Function to run benchmark with progress
run_benchmark() {
    local name=$1
    local filter=$2
    local tag=$3

    echo ""
    echo "📊 Running $name benchmarks..."
    echo "Start time: $(date '+%Y-%m-%d %H:%M:%S')"

    if [ -n "$filter" ]; then
        vendor/bin/phpbench run \
            --config=phpbench-ci.json \
            --filter="$filter" \
            --tag="$tag" \
            --store \
            --progress=dots \
            --report=ci \
            2>&1 | while IFS= read -r line; do
                echo "$line"
                # Output a heartbeat every 100 lines to keep CI alive
                if [ $((RANDOM % 100)) -eq 0 ]; then
                    echo "  ⏱️  Still running $name benchmarks... $(date '+%H:%M:%S')"
                fi
            done
    else
        vendor/bin/phpbench run \
            --config=phpbench-ci.json \
            --tag="$tag" \
            --store \
            --progress=dots \
            --report=ci \
            2>&1 | while IFS= read -r line; do
                echo "$line"
                # Output a heartbeat every 100 lines to keep CI alive
                if [ $((RANDOM % 100)) -eq 0 ]; then
                    echo "  ⏱️  Still running benchmarks... $(date '+%H:%M:%S')"
                fi
            done
    fi

    echo "✅ $name benchmarks completed at $(date '+%Y-%m-%d %H:%M:%S')"
}

# Create benchmark directory
mkdir -p .phpbench

# Run different benchmark groups
if [ "$1" == "all" ] || [ -z "$1" ]; then
    run_benchmark "All" "" "all"
elif [ "$1" == "memory" ]; then
    run_benchmark "Memory" "MemoryUsage" "memory"
elif [ "$1" == "add" ]; then
    run_benchmark "Add Operations" "AddOperations" "add"
elif [ "$1" == "search" ]; then
    run_benchmark "Search Operations" "SearchOperations" "search"
elif [ "$1" == "bulk" ]; then
    run_benchmark "Bulk Operations" "BulkOperations" "bulk"
elif [ "$1" == "iteration" ]; then
    run_benchmark "Iteration" "Iteration" "iteration"
elif [ "$1" == "immutable" ]; then
    run_benchmark "Immutable Operations" "ImmutableOperations" "immutable"
else
    echo "Unknown benchmark group: $1"
    echo "Usage: $0 [all|memory|add|search|bulk|iteration|immutable]"
    exit 1
fi

echo ""
echo "=========================================="
echo "Benchmark run complete!"
echo "=========================================="
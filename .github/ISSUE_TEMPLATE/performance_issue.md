---
name: Performance Issue
about: Report performance problems or suggest optimizations
title: '[PERF] '
labels: 'performance, needs-triage'
assignees: ''
---

## Performance Issue Description
Describe the performance issue you're experiencing with SortedLinkedList.

## Benchmark/Reproduction Code

```php
<?php
require 'vendor/autoload.php';

use SortedLinkedList\SortedLinkedList;

// Code that demonstrates the performance issue
$startTime = microtime(true);

// Your code here

$endTime = microtime(true);
echo "Execution time: " . ($endTime - $startTime) . " seconds\n";
echo "Memory usage: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";
```

## Performance Metrics

### Current Performance
- **Execution Time**: [e.g., 5.2 seconds]
- **Memory Usage**: [e.g., 128 MB]
- **Dataset Size**: [e.g., 10,000 items]
- **Operation**: [e.g., insertion, search, iteration]

### Expected Performance
What performance characteristics were you expecting?

## Comparison
If applicable, how does this compare to:
- Native PHP arrays
- Other data structure libraries
- Previous versions of SortedLinkedList

## Environment

- **PHP Version**: [e.g., 8.1.0]
- **SortedLinkedList Version**: [e.g., 1.0.0]
- **Operating System**: [e.g., Ubuntu 22.04]
- **Hardware**:
  - CPU: [e.g., Intel i7-9700K]
  - RAM: [e.g., 16GB]
  - Storage: [e.g., SSD]

## Profiling Data
If you have profiling data (xdebug, blackfire, etc.), please share relevant excerpts:

```
Paste profiling output here
```

## Suggested Optimization
If you have ideas for optimization, please share them:

```php
// Potential optimization code
```

## Additional Context

- [ ] This is a regression (worked better in a previous version)
- [ ] This affects production workloads
- [ ] I can provide additional benchmarks if needed
- [ ] I'm willing to help optimize this

## Related Issues
Are there any related performance issues or discussions?
# Contributing to SortedLinkedList

First off, thank you for considering contributing to SortedLinkedList! It's people like you that make SortedLinkedList such a great tool.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Process](#development-process)
- [Style Guidelines](#style-guidelines)
- [Testing Guidelines](#testing-guidelines)
- [Pull Request Process](#pull-request-process)
- [Community](#community)

## Code of Conduct

This project and everyone participating in it is governed by the [SortedLinkedList Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/your-username/sortedlinkedlist.git
   cd sortedlinkedlist
   ```
3. Install dependencies:
   ```bash
   composer install
   ```
4. Run tests to ensure everything is working:
   ```bash
   composer test
   ```

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** to demonstrate the steps
- **Describe the behavior you observed** and what behavior you expected
- **Include PHP version** and any relevant environment details
- **Include code samples** that demonstrate the issue

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the suggested enhancement
- **Provide specific examples** to demonstrate how it would be used
- **Describe the current behavior** and explain why the enhancement would be useful
- **List any alternative solutions** you've considered

### Contributing Code

#### Your First Code Contribution

Unsure where to begin? You can start by looking through these issues:

- Issues labeled `good first issue` - issues which should only require a few lines of code
- Issues labeled `help wanted` - issues which need extra attention
- Issues labeled `documentation` - improvements or additions to documentation

#### Pull Requests

1. Create a new branch for your feature or bugfix:
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/your-bugfix-name
   ```

2. Make your changes and commit them with descriptive commit messages

3. Add or update tests as needed

4. Ensure all tests pass and code standards are met:
   ```bash
   composer check
   ```

5. Push your branch and submit a pull request

## Development Process

### Branch Naming

- `feature/` - New features or enhancements
- `fix/` - Bug fixes
- `docs/` - Documentation improvements
- `refactor/` - Code refactoring
- `test/` - Test improvements

### Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line
- Consider starting the commit message with an applicable emoji:
  - 🎨 `:art:` - Improving structure/format of the code
  - ⚡ `:zap:` - Improving performance
  - 🐛 `:bug:` - Fixing a bug
  - 📝 `:memo:` - Writing docs
  - ✅ `:white_check_mark:` - Adding tests
  - 🔧 `:wrench:` - Changing configuration files

## Style Guidelines

### PHP Style

This project follows PSR-12 coding standards. Before submitting a pull request:

1. Run PHP CodeSniffer to check for style violations:
   ```bash
   composer cs-check
   ```

2. Fix any style violations automatically where possible:
   ```bash
   composer cs-fix
   ```

3. Run PHPStan for static analysis:
   ```bash
   composer analyse
   ```

### Code Principles

- **Write clear, self-documenting code** - Code should be readable without extensive comments
- **Follow SOLID principles** - Single responsibility, open-closed, etc.
- **Add PHPDoc blocks** for all public methods and complex logic
- **Use type declarations** for parameters and return types
- **Maintain backward compatibility** when possible

## Testing Guidelines

### Writing Tests

- Write tests for any new functionality
- Update tests when modifying existing functionality
- Follow the existing test structure and naming conventions
- Use descriptive test method names that explain what is being tested
- Aim for high code coverage but prioritize meaningful tests

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage

# Run specific test file
vendor/bin/phpunit tests/YourTestFile.php

# Run tests in a specific group
vendor/bin/phpunit --group your-group
```

### Performance Testing

If your changes might affect performance:

1. Run benchmarks before and after your changes:
   ```bash
   composer bench
   ```

2. Include benchmark results in your pull request description

3. Consider adding new benchmarks for new features

## Pull Request Process

1. **Ensure all tests pass** and code standards are met
2. **Update the README.md** with details of changes if applicable
3. **Update the CHANGELOG.md** following the existing format
4. **Increase version numbers** if applicable (we use [Semantic Versioning](http://semver.org/))
5. **Request review** from maintainers
6. **Address feedback** promptly and professionally
7. **Squash commits** if requested before merging

### Pull Request Checklist

- [ ] Tests pass locally (`composer test`)
- [ ] Code follows PSR-12 standards (`composer cs-check`)
- [ ] PHPStan analysis passes (`composer analyse`)
- [ ] Documentation is updated if needed
- [ ] Changelog entry is added
- [ ] Commit messages are clear and descriptive
- [ ] Branch is up to date with master

## Community

### Getting Help

- Open an issue for bugs or feature requests
- Join discussions in existing issues
- Check the documentation and examples

### Recognition

Contributors who submit accepted pull requests will be added to the project's contributor list. We value all contributions, whether they're code, documentation, bug reports, or feature suggestions.

## License

By contributing to SortedLinkedList, you agree that your contributions will be licensed under its MIT License.

## Questions?

Feel free to open an issue with your questions or reach out to the maintainers. We're here to help and make contributing as easy as possible!

Thank you for contributing to SortedLinkedList! 🚀
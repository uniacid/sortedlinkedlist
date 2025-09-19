# Packagist Submission Guide

This document outlines the steps to submit the SortedLinkedList package to Packagist.org.

## Prerequisites Checklist

✅ **Completed Items:**
- composer.json with complete metadata
- Semantic versioning structure
- GitHub repository public and accessible
- Comprehensive test suite passing
- Release workflow configured
- Package installation tested locally

## Submission Steps

### Step 1: Create Packagist Account

1. Go to https://packagist.org
2. Sign in with GitHub account (recommended) or create new account
3. Verify email if required

### Step 2: Submit Package

1. Click "Submit" button on Packagist homepage
2. Enter repository URL: `https://github.com/uniacid/sortedlinkedlist`
3. Click "Check" to validate the repository
4. Review the package information
5. Click "Submit" to complete submission

### Step 3: Configure Automatic Updates

#### Option A: GitHub Webhook (Recommended)

1. In Packagist, go to your package page
2. Click "Settings" tab
3. Copy the webhook URL
4. In GitHub repository:
   - Go to Settings → Webhooks
   - Click "Add webhook"
   - Paste Packagist webhook URL
   - Content type: application/json
   - Secret: Leave empty (unless Packagist provides one)
   - Events: Select "Just the push event"
   - Click "Add webhook"

#### Option B: API Token (Alternative)

1. In Packagist, go to Profile → Show API Token
2. Copy your API token
3. Add to GitHub repository secrets:
   - Go to Settings → Secrets and variables → Actions
   - Click "New repository secret"
   - Name: `PACKAGIST_API_TOKEN`
   - Value: [Your API token]
4. The release workflow will automatically notify Packagist

### Step 4: Verify Submission

1. Check package appears at: https://packagist.org/packages/uniacid/sortedlinkedlist
2. Verify all metadata displays correctly:
   - Description
   - Keywords
   - License
   - Authors
   - Support links
3. Check badges render properly
4. Verify installation instructions work

### Step 5: Test Installation from Packagist

```bash
# Create test directory
mkdir test-packagist && cd test-packagist

# Initialize composer project
composer init --name=test/package --type=project

# Install package from Packagist
composer require uniacid/sortedlinkedlist

# Test basic usage
php -r '
require "vendor/autoload.php";
use SortedLinkedList\IntegerSortedLinkedList;
$list = new IntegerSortedLinkedList();
$list->add(3);
$list->add(1);
$list->add(2);
print_r($list->toArray()); // Should output: [1, 2, 3]
'
```

### Step 6: Monitor Package Statistics

1. View download statistics on package page
2. Monitor GitHub stars and issues
3. Check Packagist quality score
4. Review dependency insights

## Post-Submission Tasks

### Update README
- [ ] Add Packagist installation instructions
- [ ] Add Packagist version badge
- [ ] Add downloads badge
- [ ] Update installation section

### Badges to Add
```markdown
[![Latest Stable Version](https://poser.pugx.org/uniacid/sortedlinkedlist/v)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![Total Downloads](https://poser.pugx.org/uniacid/sortedlinkedlist/downloads)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![License](https://poser.pugx.org/uniacid/sortedlinkedlist/license)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![PHP Version Require](https://poser.pugx.org/uniacid/sortedlinkedlist/require/php)](https://packagist.org/packages/uniacid/sortedlinkedlist)
```

### Create First Release
1. Ensure all tests pass
2. Update CHANGELOG.md
3. Create git tag: `git tag -a v1.0.0 -m "Initial release"`
4. Push tag: `git push origin v1.0.0`
5. GitHub Actions will create release automatically

## Troubleshooting

### Package Not Updating
- Check webhook is configured correctly
- Verify push events are being sent
- Manual update: Click "Update" on Packagist package page
- Check composer.json is valid: `composer validate --strict`

### Package Not Found
- Ensure repository is public
- Check composer.json exists in repository root
- Verify package name follows convention: vendor/package
- Wait 1-2 minutes for Packagist cache update

### Quality Issues
- Add comprehensive README
- Include LICENSE file
- Provide clear documentation
- Add support URLs in composer.json
- Use semantic versioning

## Maintenance

### Regular Tasks
- Monitor issues and pull requests
- Update dependencies regularly
- Tag releases following SemVer
- Respond to community feedback
- Update documentation

### Version Management
- MAJOR: Breaking API changes
- MINOR: New features (backward compatible)
- PATCH: Bug fixes (backward compatible)

## Support

For Packagist-specific issues:
- Documentation: https://packagist.org/about
- Support: https://github.com/composer/packagist/issues

For package-specific issues:
- GitHub Issues: https://github.com/uniacid/sortedlinkedlist/issues
- Discussions: https://github.com/uniacid/sortedlinkedlist/discussions
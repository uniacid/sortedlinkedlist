# Packagist Release Setup Guide

This document provides instructions for releasing the SortedLinkedList package on Packagist.

## Prerequisites

- [x] Valid composer.json with all required fields
- [x] LICENSE file present in repository
- [x] Public GitHub repository
- [x] All tests passing
- [ ] Git tag for initial release

## Step 1: Submit Package to Packagist

1. Go to [https://packagist.org/](https://packagist.org/)
2. Sign in with your GitHub account
3. Click "Submit" in the navigation menu
4. Enter the repository URL: `https://github.com/uniacid/sortedlinkedlist`
5. Click "Check" to validate the repository
6. Review the package information
7. Click "Submit" to add the package

## Step 2: Setup GitHub Webhook for Auto-Updates

After submitting the package, Packagist will provide instructions to set up automatic updates:

1. Go to your GitHub repository settings: https://github.com/uniacid/sortedlinkedlist/settings/hooks
2. Click "Add webhook"
3. Configure the webhook:
   - **Payload URL**: Will be provided by Packagist (format: `https://packagist.org/api/github?username=YOUR_USERNAME`)
   - **Content type**: `application/json`
   - **Secret**: Leave empty (unless Packagist provides one)
   - **Which events?**: Select "Just the push event"
   - **Active**: Check the box
4. Click "Add webhook"

### Verifying Webhook Setup

1. The webhook will trigger on every push to the repository
2. Check the webhook's "Recent Deliveries" tab for successful pings
3. Packagist will automatically update package information when:
   - New commits are pushed
   - New tags are created
   - composer.json is modified

## Step 3: Creating Releases

### Initial Release (v0.1.0)

```bash
# Ensure all changes are committed
git add .
git commit -m "Prepare for initial Packagist release"

# Create and push the tag
git tag -a v0.1.0 -m "Initial release"
git push origin v0.1.0
```

### Future Releases

Follow semantic versioning (https://semver.org/):
- **MAJOR** (v1.0.0): Incompatible API changes
- **MINOR** (v0.2.0): Add functionality (backwards-compatible)
- **PATCH** (v0.1.1): Bug fixes (backwards-compatible)

```bash
# Example for patch release
git tag -a v0.1.1 -m "Fix bug in binary search"
git push origin v0.1.1

# Example for minor release
git tag -a v0.2.0 -m "Add bulk operations support"
git push origin v0.2.0
```

## Step 4: Package Verification

After submission and webhook setup:

1. Visit your package page: `https://packagist.org/packages/uniacid/sortedlinkedlist`
2. Verify all information is correct:
   - Description
   - Keywords
   - License
   - Authors
   - Dependencies
3. Check the version/tag is listed
4. Test installation:
   ```bash
   composer require uniacid/sortedlinkedlist
   ```

## Step 5: Add Packagist Badges to README

Add these badges to your README.md:

```markdown
[![Latest Stable Version](https://poser.pugx.org/uniacid/sortedlinkedlist/v)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![Total Downloads](https://poser.pugx.org/uniacid/sortedlinkedlist/downloads)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![License](https://poser.pugx.org/uniacid/sortedlinkedlist/license)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![PHP Version Require](https://poser.pugx.org/uniacid/sortedlinkedlist/require/php)](https://packagist.org/packages/uniacid/sortedlinkedlist)
```

## Maintenance

### Updating Package Information

Changes to composer.json are automatically reflected on Packagist when pushed to GitHub (if webhook is configured).

### Handling Issues

- **Package not updating**: Check webhook deliveries in GitHub settings
- **Invalid composer.json**: Run `composer validate --strict` before pushing
- **Version conflicts**: Ensure tags follow semantic versioning

### Security Updates

For security vulnerabilities:
1. Fix the issue
2. Create a new patch release immediately
3. Consider adding a security advisory on GitHub

## Support

For issues with:
- **This package**: https://github.com/uniacid/sortedlinkedlist/issues
- **Packagist**: https://github.com/composer/packagist/issues
- **Composer**: https://github.com/composer/composer/issues
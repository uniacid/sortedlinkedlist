# Branch Protection Rules Configuration

This document outlines the recommended branch protection rules for the SortedLinkedList repository.

## Main Branch Protection

The `main` branch should have the following protection rules enabled:

### Required Settings

1. **Require a pull request before merging**
   - Require approvals: 1
   - Dismiss stale pull request approvals when new commits are pushed: ✓
   - Require review from CODEOWNERS: ✓ (if CODEOWNERS file exists)

2. **Require status checks to pass before merging**
   - Require branches to be up to date before merging: ✓
   - Required status checks:
     - `test (8.1, prefer-lowest)`
     - `test (8.1, prefer-stable)`
     - `test (8.2, prefer-lowest)`
     - `test (8.2, prefer-stable)`
     - `test (8.3, prefer-lowest)`
     - `test (8.3, prefer-stable)`
     - `coverage`
     - `documentation`

3. **Require conversation resolution before merging**
   - All conversations must be resolved: ✓

4. **Require signed commits** (optional but recommended)
   - Require all commits to be signed with GPG: ✓

5. **Include administrators**
   - Apply rules to administrators: ✓

6. **Restrict who can push to matching branches**
   - Restrict pushes that create matching branches: ✓
   - Allow specified users/teams/apps only

### Additional Settings

- **Allow force pushes**: ✗ (Never allow)
- **Allow deletions**: ✗ (Never allow)
- **Lock branch**: ✗ (Keep unlocked for normal operations)

## Develop Branch Protection (if applicable)

If using a develop branch, apply similar rules with these modifications:

1. **Require a pull request before merging**
   - Require approvals: 0 (or 1 for stricter control)
   - Other settings same as main

2. **Require status checks to pass before merging**
   - Same required checks as main branch

## Setting Up Branch Protection

### Via GitHub Web UI

1. Navigate to Settings → Branches
2. Click "Add branch protection rule"
3. Enter branch name pattern: `main`
4. Configure settings as listed above
5. Click "Create" or "Save changes"

### Via GitHub CLI

```bash
# Install GitHub CLI if not already installed
# brew install gh (macOS)
# or download from https://cli.github.com/

# Authenticate
gh auth login

# Set branch protection rules for main branch
gh api repos/:owner/:repo/branches/main/protection \
  --method PUT \
  --field required_status_checks='{"strict":true,"contexts":["test (8.1, prefer-lowest)","test (8.1, prefer-stable)","test (8.2, prefer-lowest)","test (8.2, prefer-stable)","test (8.3, prefer-lowest)","test (8.3, prefer-stable)","coverage","documentation"]}' \
  --field enforce_admins=true \
  --field required_pull_request_reviews='{"dismissal_restrictions":{},"dismiss_stale_reviews":true,"require_code_owner_reviews":false,"required_approving_review_count":1}' \
  --field restrictions=null \
  --field allow_force_pushes=false \
  --field allow_deletions=false \
  --field required_conversation_resolution=true
```

### Via GitHub Actions (automated setup)

A workflow can be created to verify branch protection rules are in place:

```yaml
name: Verify Branch Protection

on:
  schedule:
    - cron: '0 0 * * 1' # Weekly check
  workflow_dispatch:

jobs:
  verify-protection:
    runs-on: ubuntu-latest
    steps:
      - name: Check branch protection
        uses: actions/github-script@v7
        with:
          script: |
            const { data: branch } = await github.rest.repos.getBranch({
              owner: context.repo.owner,
              repo: context.repo.repo,
              branch: 'main'
            });

            if (!branch.protected) {
              core.setFailed('Main branch is not protected!');
            }
```

## Enforcement

Branch protection rules should be:
- Reviewed quarterly for effectiveness
- Updated when new CI checks are added
- Documented in this file when changed
- Communicated to all contributors

## Exceptions

Emergency fixes may require temporary bypass:
1. Document the reason in PR description
2. Get approval from repository admin
3. Re-enable protection immediately after merge

## Contributing

All contributors should:
1. Create feature branches from `main`
2. Open pull requests against `main`
3. Ensure all CI checks pass
4. Get required approvals before merge
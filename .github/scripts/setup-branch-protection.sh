#!/bin/bash

# Setup Branch Protection Rules for SortedLinkedList Repository
# This script configures branch protection rules using GitHub CLI

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if gh CLI is installed
if ! command -v gh &> /dev/null; then
    echo -e "${RED}Error: GitHub CLI (gh) is not installed.${NC}"
    echo "Please install it from: https://cli.github.com/"
    exit 1
fi

# Check if authenticated
if ! gh auth status &> /dev/null; then
    echo -e "${YELLOW}Not authenticated with GitHub CLI.${NC}"
    echo "Running: gh auth login"
    gh auth login
fi

# Get repository information
REPO=$(gh repo view --json nameWithOwner -q .nameWithOwner)
if [ -z "$REPO" ]; then
    echo -e "${RED}Error: Could not determine repository.${NC}"
    echo "Make sure you're in a git repository with a GitHub remote."
    exit 1
fi

echo -e "${GREEN}Setting up branch protection for: ${REPO}${NC}"

# Function to setup branch protection
setup_branch_protection() {
    local BRANCH=$1
    echo -e "${YELLOW}Configuring protection for branch: ${BRANCH}${NC}"

    # Create the protection rules
    gh api \
        --method PUT \
        -H "Accept: application/vnd.github+json" \
        -H "X-GitHub-Api-Version: 2022-11-28" \
        "/repos/${REPO}/branches/${BRANCH}/protection" \
        -F required_status_checks[strict]=true \
        -f required_status_checks[contexts][]='PHP 8.1 - prefer-lowest' \
        -f required_status_checks[contexts][]='PHP 8.1 - prefer-stable' \
        -f required_status_checks[contexts][]='PHP 8.2 - prefer-lowest' \
        -f required_status_checks[contexts][]='PHP 8.2 - prefer-stable' \
        -f required_status_checks[contexts][]='PHP 8.3 - prefer-lowest' \
        -f required_status_checks[contexts][]='PHP 8.3 - prefer-stable' \
        -f required_status_checks[contexts][]='Code Coverage' \
        -F enforce_admins=true \
        -F required_pull_request_reviews[dismiss_stale_reviews]=true \
        -F required_pull_request_reviews[require_code_owner_reviews]=false \
        -F required_pull_request_reviews[required_approving_review_count]=1 \
        -F required_pull_request_reviews[require_last_push_approval]=false \
        -F required_conversation_resolution=true \
        -F lock_branch=false \
        -F allow_fork_syncing=true \
        -F required_linear_history=false \
        -F allow_force_pushes=false \
        -F allow_deletions=false \
        -F block_creations=false \
        --field restrictions=null

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Branch protection configured for: ${BRANCH}${NC}"
    else
        echo -e "${RED}✗ Failed to configure protection for: ${BRANCH}${NC}"
        return 1
    fi
}

# Setup protection for main branch
setup_branch_protection "main"

# Check if develop branch exists and setup protection
if gh api "/repos/${REPO}/branches/develop" &> /dev/null; then
    echo -e "${YELLOW}Develop branch found, configuring protection...${NC}"
    setup_branch_protection "develop"
fi

echo -e "${GREEN}Branch protection setup complete!${NC}"
echo ""
echo "To verify protection settings, visit:"
echo "https://github.com/${REPO}/settings/branches"
echo ""
echo "You can also verify with:"
echo "gh api /repos/${REPO}/branches/main/protection"
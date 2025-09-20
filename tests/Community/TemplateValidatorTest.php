<?php

declare(strict_types=1);

namespace Tests\Community;

use PHPUnit\Framework\TestCase;

class TemplateValidatorTest extends TestCase
{
    private string $projectRoot;
    private string $githubDir;

    protected function setUp(): void
    {
        $this->projectRoot = dirname(__DIR__, 2);
        $this->githubDir = $this->projectRoot . '/.github';
    }

    public function testGitHubDirectoryExists(): void
    {
        $this->assertDirectoryExists(
            $this->githubDir,
            '.github directory should exist'
        );
    }

    public function testIssueTemplateDirectoryExists(): void
    {
        $templateDir = $this->githubDir . '/ISSUE_TEMPLATE';
        $this->assertDirectoryExists(
            $templateDir,
            'Issue template directory should exist'
        );
    }

    public function testBugReportTemplateExists(): void
    {
        $bugTemplate = $this->githubDir . '/ISSUE_TEMPLATE/bug_report.md';
        $this->assertFileExists(
            $bugTemplate,
            'Bug report template should exist'
        );

        $content = file_get_contents($bugTemplate);
        $this->assertStringContainsString('name:', $content);
        $this->assertStringContainsString('about:', $content);
        $this->assertStringContainsString('title:', $content);
        $this->assertStringContainsString('labels:', $content);
    }

    public function testFeatureRequestTemplateExists(): void
    {
        $featureTemplate = $this->githubDir . '/ISSUE_TEMPLATE/feature_request.md';
        $this->assertFileExists(
            $featureTemplate,
            'Feature request template should exist'
        );

        $content = file_get_contents($featureTemplate);
        $this->assertStringContainsString('name:', $content);
        $this->assertStringContainsString('about:', $content);
        $this->assertStringContainsString('title:', $content);
        $this->assertStringContainsString('labels:', $content);
    }

    public function testPerformanceIssueTemplateExists(): void
    {
        $perfTemplate = $this->githubDir . '/ISSUE_TEMPLATE/performance_issue.md';
        $this->assertFileExists(
            $perfTemplate,
            'Performance issue template should exist'
        );

        $content = file_get_contents($perfTemplate);
        $this->assertStringContainsString('name:', $content);
        $this->assertStringContainsString('about:', $content);
        $this->assertStringContainsString('Performance', $content);
    }

    public function testPullRequestTemplateExists(): void
    {
        $prTemplate = $this->githubDir . '/pull_request_template.md';
        $this->assertFileExists(
            $prTemplate,
            'Pull request template should exist'
        );

        $content = file_get_contents($prTemplate);

        // Check for required sections
        $this->assertStringContainsString('## Description', $content);
        $this->assertStringContainsString('## Type of Change', $content);
        $this->assertStringContainsString('## Checklist', $content);

        // Check for checklist items
        $this->assertMatchesRegularExpression('/\[ \].*tests/i', $content);
        $this->assertMatchesRegularExpression('/\[ \].*documentation/i', $content);
        $this->assertMatchesRegularExpression('/\[ \].*breaking change/i', $content);
    }

    public function testContributingFileExists(): void
    {
        $contributingFile = $this->projectRoot . '/CONTRIBUTING.md';
        $this->assertFileExists(
            $contributingFile,
            'CONTRIBUTING.md should exist'
        );

        $content = file_get_contents($contributingFile);

        // Check for essential sections
        $this->assertStringContainsString('## Getting Started', $content);
        $this->assertStringContainsString('## Development Setup', $content);
        $this->assertStringContainsString('## Pull Request Process', $content);
        $this->assertStringContainsString('## Code Standards', $content);
        $this->assertStringContainsString('## Testing', $content);
    }

    public function testCodeOfConductFileExists(): void
    {
        $codeOfConductFile = $this->projectRoot . '/CODE_OF_CONDUCT.md';
        $this->assertFileExists(
            $codeOfConductFile,
            'CODE_OF_CONDUCT.md should exist'
        );

        $content = file_get_contents($codeOfConductFile);

        // Check for Contributor Covenant elements
        $this->assertStringContainsString('## Our Pledge', $content);
        $this->assertStringContainsString('## Our Standards', $content);
        $this->assertStringContainsString('## Enforcement', $content);
    }

    public function testLabelsConfigurationExists(): void
    {
        $labelsFile = $this->githubDir . '/labels.yml';
        $this->assertFileExists(
            $labelsFile,
            'GitHub labels configuration should exist'
        );

        $content = file_get_contents($labelsFile);

        // Check for label categories
        $this->assertStringContainsString('bug', $content);
        $this->assertStringContainsString('feature', $content);
        $this->assertStringContainsString('enhancement', $content);
        $this->assertStringContainsString('documentation', $content);
        $this->assertStringContainsString('priority', $content);
    }

    public function testIssueTemplateConfigExists(): void
    {
        $configFile = $this->githubDir . '/ISSUE_TEMPLATE/config.yml';
        $this->assertFileExists(
            $configFile,
            'Issue template config should exist'
        );

        $content = file_get_contents($configFile);
        $this->assertStringContainsString('blank_issues_enabled:', $content);
        $this->assertStringContainsString('contact_links:', $content);
    }

    public function testTemplateMarkdownValidity(): void
    {
        // Issue templates should have YAML frontmatter
        $issueTemplates = [
            $this->githubDir . '/ISSUE_TEMPLATE/bug_report.md',
            $this->githubDir . '/ISSUE_TEMPLATE/feature_request.md',
            $this->githubDir . '/ISSUE_TEMPLATE/performance_issue.md',
        ];

        foreach ($issueTemplates as $template) {
            $this->assertFileExists($template, "Template {$template} should exist");
            $content = file_get_contents($template);

            // Check for valid YAML front matter in issue templates
            $this->assertMatchesRegularExpression(
                '/^---\n.*\n---/s',
                $content,
                "Issue template {$template} should have valid YAML front matter"
            );

            // Check for no broken markdown syntax
            $this->assertDoesNotMatchRegularExpression(
                '/\[.*\]\(\s*\)/',
                $content,
                "Template {$template} should not have empty links"
            );
        }

        // PR template doesn't need frontmatter
        $prTemplate = $this->githubDir . '/pull_request_template.md';
        $this->assertFileExists($prTemplate, "PR template should exist");
        $content = file_get_contents($prTemplate);

        // Check for no broken markdown syntax
        $this->assertDoesNotMatchRegularExpression(
            '/\[.*\]\(\s*\)/',
            $content,
            "PR template should not have empty links"
        );
    }

    public function testDiscussionsInstructionsExist(): void
    {
        $discussionsFile = $this->githubDir . '/DISCUSSIONS_SETUP.md';
        $this->assertFileExists(
            $discussionsFile,
            'GitHub Discussions setup instructions should exist'
        );

        $content = file_get_contents($discussionsFile);

        // Check for required categories
        $this->assertStringContainsString('Q&A', $content);
        $this->assertStringContainsString('Ideas', $content);
        $this->assertStringContainsString('Show and Tell', $content);
        $this->assertStringContainsString('General', $content);
    }
}

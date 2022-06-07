<?php

namespace p1\view\security;

class HtmlSanitizer {

  public function sanitizeConfigDefault(?string $html): ?string {
    return $this->sanitize($html, self::configDefault());
  }

  public function sanitize(?string $html, HtmlSanitizerConfig $config): ?string {
    if (empty($html)) {
      return $html;
    }
    $trimmed = trim($html);
    $strippedSlashes = $config->stripSlashes() ? stripslashes($trimmed) : $trimmed;
    // TODO: use some external tool to strip CSS / JS as well
    return strip_tags($strippedSlashes, $config->allowedTags());
  }

  public static function config(): HtmlSanitizerConfigBuilder {
    return new HtmlSanitizerConfigBuilder();
  }

  public static function configDefault(): HtmlSanitizerConfig {
    return self::config()->build();
  }
}

class HtmlSanitizerConfig {

  private string $allowedTags;
  private bool $stripSlashes;

  public function __construct(string $allowedTags,
                              bool   $stripSlashed) {
    $this->allowedTags = $allowedTags;
    $this->stripSlashes = $stripSlashed;
  }

  public function allowedTags(): string {
    return $this->allowedTags;
  }

  public function stripSlashes(): bool {
    return $this->stripSlashes;
  }
}

class HtmlSanitizerConfigBuilder {
  private const DEFAULT_ALLOWED_TAGS = '<p><strong><em><u><h1><h2><h3><h4><h5><h6>'
  . '<img><li><ol><ul><span><div><br><ins><del>';
  private string $allowedTags;
  private bool $stripSlashes;

  public function __construct() {
    $this->allowedTags = self::DEFAULT_ALLOWED_TAGS;
    $this->stripSlashes = true;
  }

  public function allowedTags(string $allowedTags): HtmlSanitizerConfigBuilder {
    $this->allowedTags = $allowedTags;
    return $this;
  }

  public function stripSlashes(): HtmlSanitizerConfigBuilder {
    $this->stripSlashes = true;
    return $this;
  }

  public function skipStripSlashes(): HtmlSanitizerConfigBuilder {
    $this->stripSlashes = false;
    return $this;
  }

  public function build(): HtmlSanitizerConfig {
    return new HtmlSanitizerConfig($this->allowedTags, $this->stripSlashes);
  }
}
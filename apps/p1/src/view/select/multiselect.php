<?php

namespace p1\view\select;

class Multiselect {
  private string $name;
  private string $labelValue;
  private string $displayValuesInputLabel;
  private array $options;
  private array $selectedOptions;
  private bool $optionSelectAll;

  public function __construct(string $name,
                              string $labelValue,
                              string $displayValuesInputLabel,
                              array  $options,
                              array  $selectedOptions = [],
                              bool   $optionSelectAll = true) {
    $this->name = $name;
    $this->labelValue = $labelValue;
    $this->displayValuesInputLabel = $displayValuesInputLabel;
    $this->options = $options;
    $this->selectedOptions = $selectedOptions;
    $this->optionSelectAll = $optionSelectAll;
  }

  public function name(): string {
    return $this->name;
  }

  public function labelValue(): string {
    return $this->labelValue;
  }

  public function displayValuesInputLabel(): string {
    return $this->displayValuesInputLabel;
  }

  public function options(): array {
    return $this->options;
  }

  public function selectedOptions(): array {
    return $this->selectedOptions;
  }

  public function optionSelectAll(): bool {
    return $this->optionSelectAll;
  }
}

class SelectOption {
  private string $id;
  private ?string $value;
  private ?string $displayName;

  public function __construct(string  $id,
                              ?string $value,
                              ?string $displayName) {
    $this->id = $id;
    $this->value = $value;
    $this->displayName = $displayName;
  }

  public function id(): string {
    return $this->id;
  }

  public function value(): ?string {
    return $this->value;
  }

  public function displayName(): ?string {
    return $this->displayName;
  }

  public function __toString(): string {
    return 'SelectOption(id=' . $this->id
      . ', value=' . $this->value
      . ', displayname=' . $this->displayName . ')';
  }


}
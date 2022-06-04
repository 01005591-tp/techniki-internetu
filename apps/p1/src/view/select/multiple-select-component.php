<?php

require_once "core/function/function.php";
require_once "view/select/multiselect.php";

use p1\core\function\Runnable;
use p1\view\select\SelectOption;

$selectedOptions = [];
foreach ($multiselect->selectedOptions() as $selectedOption) {
  $options = array_filter($multiselect->options(), function ($option, $key) use ($selectedOption) {
    return $selectedOption === $option->value();
  }, ARRAY_FILTER_USE_BOTH);
  $option = array_pop($options);
  $selectedOptions[] = $option;
}
$selectedOptionIds = array_map(function ($opt) {
  return 'opt_' . $opt->id();
}, $selectedOptions);

$selectedOptionValues = array_filter(array_map(function ($opt) {
  return $opt->value();
}, $selectedOptions), function ($optValue, $key) {
  return !empty($optValue);
}, ARRAY_FILTER_USE_BOTH);

$selectedOptionDisplayNames = array_filter(array_map(function ($opt) {
  return $opt->displayName();
}, $selectedOptions), function ($displayName, $key) {
  return !empty($displayName);
}, ARRAY_FILTER_USE_BOTH);

$selectedOptionIdsString = join(',', $selectedOptionIds);
$selectedOptionValuesString = join(',', $selectedOptionValues);
$selectedOptionDisplayNamesString = join(',', $selectedOptionDisplayNames);
?>

<div id="<?php echo $multiselect->name() . '_parentElement'; ?>" class="dropdown">
    <div class="form-floating d-flex">
        <input type="text"
               id="<?php echo $multiselect->name() . '_displayInput'; ?>"
               class="form-control"
               name="<?php echo $multiselect->name() . '_displayInput'; ?>"
               readonly
               placeholder="<?php echo $multiselect->displayValuesInputLabel(); ?>"
          <?php if (!empty($selectedOptionDisplayNamesString)) {
            echo 'value="' . $selectedOptionDisplayNamesString . '"';
          } ?>
        />
        <label for="<?php echo $multiselect->name() . '_displayInput'; ?>">
          <?php echo $multiselect->displayValuesInputLabel(); ?>
        </label>
        <a type="button" href="#"
           role="button"
           class="btn btn-secondary dropdown-toggle align-content-center"
           data-bs-toggle="dropdown"
           data-bs-auto-close="outside"
           aria-expanded="false"
           id="<?php echo $multiselect->name() . '_btn'; ?>">
        </a>
        <div class="dropdown-menu" aria-labelledby="<?php echo $multiselect->name() . '_btn'; ?>">
            <!-- TODO: implement multiselect search JS side -->
            <div class="d-none form-floating mb-2 mx-2">
                <form name="<?php echo $multiselect->name() . '_form'; ?>">
                    <input type="text"
                           id="<?php echo $multiselect->name() . '_search_input'; ?>"
                           class="form-control"
                           placeholder="
            <?php echo L::main_multiple_select_component_search_input_label; ?>">
                    <label for="<?php echo $multiselect->name() . '_search_input'; ?>">
                      <?php echo L::main_multiple_select_component_search_input_label; ?>
                    </label>
                </form>
            </div>
          <?php
          // TODO: implement select all support
          if (false && $multiselect->optionSelectAll()) {
            $selectAllRunnable = new class implements Runnable {
              function run(): void {
                $option = new SelectOption(
                  'select-all-option-unique-id-i-hope-so',
                  null,
                  L::main_multiple_select_component_select_all_option_label
                );
                require "view/select/multiple-select-option-component.php";
                echo '<hr/>';
              }
            };
            $selectAllRunnable->run();
          }
          foreach ($multiselect->options() as $option) {
            $optionRunnable = new class($option) implements Runnable {
              private SelectOption $option;

              public function __construct(SelectOption $option) {
                $this->option = $option;
              }

              function run(): void {
                $option = $this->option;
                require "view/select/multiple-select-option-component.php";
              }
            };
            $optionRunnable->run();
          }
          ?>
        </div>
    </div>
    <input id="<?php echo $multiselect->name() . '_valueHolderInput'; ?>"
           name="<?php echo $multiselect->name() . '_valueHolderInput'; ?>"
           aria-label="hiddenValueHolderInput"
           aria-hidden="true"
      <?php if (!empty($selectedOptionValuesString)) {
        echo 'value="' . $selectedOptionValuesString . '"';
      } ?>
           hidden>
    <input id="<?php echo $multiselect->name() . '_optionsHolderInput'; ?>"
           aria-label="hiddenOptionsHolderInput"
           aria-hidden="true"
      <?php if (!empty($selectedOptionIdsString)) {
        echo 'value="' . $selectedOptionIdsString . '"';
      } ?>
           hidden>

    <script>
        let multipleSelectComponent = new MultipleSelectComponent(
            "<?php echo $multiselect->name() . '_displayInput'; ?>",
            "<?php echo $multiselect->name() . '_parentElement'; ?>",
            "<?php echo $multiselect->name() . '_valueHolderInput'; ?>",
            "<?php echo $multiselect->name() . '_optionsHolderInput'; ?>"
        );
    </script>

</div>
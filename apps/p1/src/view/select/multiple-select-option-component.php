<?php

?>


<div class="form-check mx-2">
    <input class="form-check-input"
           type="checkbox"
           value="<?php echo $option->value(); ?>"
           id="opt_<?php echo $option->id(); ?>">
    <label class="form-check-label" for="opt_<?php echo $option->id(); ?>">
      <?php echo $option->displayName(); ?>
    </label>
</div>
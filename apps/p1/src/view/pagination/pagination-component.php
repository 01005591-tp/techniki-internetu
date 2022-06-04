<?php

$uri = $paginationData->uri() . '?' . $paginationData->queryParamName() . '=';
?>

<nav aria-label="Book list navigation top">
    <ul class="pagination justify-content-center">
        <li class="page-item">
            <a class="page-link"
               href="<?php echo $uri . $paginationData->previousPage(); ?>"
               aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
      <?php
      $pages = $paginationData->pages();
      foreach ($pages as $page) {
        echo '<li class="page-item ' . $page->style() . '">
                        <a class="page-link" href="' . $uri . $page->index() . '">' . $page->indexDisplay() . '</a>
                      </li>';
      }
      ?>
        <li class="page-item">
            <a class="page-link"
               href="<?php echo $uri . $paginationData->nextPage(); ?>"
               aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>

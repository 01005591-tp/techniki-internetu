<?php

$firstName = empty($author->firstName()) ? '' : $author->firstName();
$lastName = empty($author->lastName()) ? '' : $author->lastName();
$authorName = $firstName . ' ' . $lastName;

$birthDay = empty($author->birthDay()) ? '' : '-' . str_pad($author->birthDay(), 2, '0', STR_PAD_LEFT);
$birthMonth = empty($author->birthMonth()) ? '' : '-' . str_pad($author->birthMonth(), 2, '0', STR_PAD_LEFT);
$birthYear = empty($author->birthYear()) ? '' : str_pad($author->birthYear(), 2, '0', STR_PAD_LEFT);

$birthDate = $birthYear . $birthMonth . $birthDay;
$birthDateDisplay = empty($birthDate) ? '' : ' (' . $birthDate . ')';
?>
<hr/>

<div class="authors">
    <div class="h5 author author-<?php echo $author->priority(); ?>"><?php echo $authorName . $birthDateDisplay; ?></div>
</div>
<div>
  <?php echo $author->biographyNote(); ?>
</div>
<div class="shadow-lg p-2 mb-5 bg-body rounded">
    <div class="d-flex justify-content-center">
        <span class="p2 h1"><?php echo L::main_home_book_list_header ?></span>
    </div>
    <div class="d-flex flex-wrap justify-content-center">
        <?php
        for ($i = 0; $i < 22; $i++) {
            echo '
        <div id="book-list-cards-container" class="p-2">
            <div class="card">
                <img src="/assets/book-icon.svg" class="card-img-top" alt="Book icon">
                <div class="card-body">
                    <h5 class="card-title">Book title</h5>
                    <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean scelerisque eu magna et fermentum. Sed sollicitudin ut urna in lacinia. Nulla mattis dui nisi, at hendrerit augue mattis et. In tincidunt et nisl eu accumsan. Pellentesque vitae purus ex. Ut finibus venenatis felis. Mauris vitae sodales sem. Nunc eleifend sodales lorem. Fusce tincidunt mauris non dui vehicula, at posuere sapien mollis. Morbi sagittis ex dapibus, tristique massa sit amet, blandit ligula. Quisque eget mi ex. Ut luctus enim libero, vitae. </p>
                    <a href="/" class="btn btn-primary">See details</a>
                </div>
            </div>
        </div>
        ';
        } ?>

    </div>
</div>
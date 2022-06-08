<hr/>
<div>
    <span class="p2 h3 mb-4">Employee features</span>
</div>
<div class="accordion mt-4" id="aboutFeaturesAccordion">
    <!-- Employee: Book details page -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="bookDetailsEmployeeHeader">
            <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#bookDetailsEmployeeDescriptionPanel"
                    aria-expanded="true"
                    aria-controls="bookDetailsEmployeeDescriptionPanel">
                (Employee) Book details page
            </button>
        </h2>
        <div id="bookDetailsEmployeeDescriptionPanel" class="accordion-collapse collapse"
             aria-labelledby="mainPageHeader">
            <div class="accordion-body">
                <div>
                    When you enter page details, you can see the same things as any other user.
                </div>
                <div>
                    <strong>Employee</strong> users can also see an Edit button, allowing them to make some
                    modifications to existing books.
                </div>
                <div>
                    One can also enter edition page via books permalink, e.g.
                    <a href="/books/the-complete-works-of-william-shakespeare-leather-bound-classics-2014-10-01/edition">
                        /books/the-complete-works-of-william-shakespeare-leather-bound-classics-2014-10-01/edition
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Employee: Book edit page -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="bookEditEmployeeHeader">
            <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#bookEditEmployeeDescriptionPanel"
                    aria-expanded="true"
                    aria-controls="bookEditEmployeeDescriptionPanel">
                (Employee) Edit page
            </button>
        </h2>
        <div id="bookEditEmployeeDescriptionPanel" class="accordion-collapse collapse"
             aria-labelledby="mainPageHeader">
            <div class="accordion-body">
                <div>
                    Edition is very limited for now. Employee can edit the following data:
                    <ul>
                        <li><strong>ISBN</strong> - verified if contain digits and '-' only (cannot end or start with
                            '-' character)
                        </li>
                        <li><strong>State</strong> - selected from the selection menu</li>
                        <li>Language - selected from the selection menu</li>
                        <li>Published at - selected using a simple date picker component</li>
                        <li>Page count - only non-negative integers allowed</li>
                        <li>Tags - comma-separated values representing book tags. Can contain any letters or digits and
                            be separated with '-' char.
                            Cannot end or start with a '-' or ',' char.
                        </li>
                        <li><strong>Title</strong> - this field has nearly no restrictions.</li>
                        <li><strong>Link name</strong> - this is the unique name of the book used in the permalink.
                            Can contain letters, numbers and '-' chars. Cannot start or end with a '-' character.
                        </li>
                        <li class="w-100">Image URL - any URL for the image. Application has a few images built-in, but
                            you can use
                            any URL. Try pasting the following link into the Image URL and clicking the Preview button.
                            <a href="#" readonly="true">
                                https://upload.wikimedia.org/wikipedia/commons/4/4d/Gmach_G%C5%82%C3%B3wny_Politechniki_Warszawskiej_2018.jpg
                            </a>
                        </li>
                        <li>
                            About the book - this is a section where you can describe a book. It supports very basic
                            HTML tags.
                        </li>
                    </ul>
                    * data in <strong>bold</strong> are required
                </div>
            </div>
        </div>
    </div>
</div>
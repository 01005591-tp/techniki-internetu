<div class="shadow-lg p-2 mb-5 bg-body rounded">
    <div class="d-flex justify-content-center">
        <span class="p2 h1 mb-4">Library</span>
    </div>
    <hr/>
    <div class="container">
        <p>
            This is a very simple and feature limited library implementation.
            Actually, it has nearly no features at all.
        </p>
        <span class="p2 h3 mb-4">Available features</span>

        <div class="accordion mt-4" id="aboutFeaturesAccordion">
            <!-- Home page -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="mainPageHeader">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#mainPageDescriptionPanel"
                            aria-expanded="true"
                            aria-controls="mainPageDescriptionPanel">
                        Home page
                    </button>
                </h2>
                <div id="mainPageDescriptionPanel" class="accordion-collapse collapse"
                     aria-labelledby="mainPageHeader">
                    <div class="accordion-body">
                        <div>
                            <a href="/" aria-label="home-page">Home page</a> allows you to display available books, as
                            well as search them by a
                            few criterias:
                            <ul>
                                <li>Title - case insensitive, searches by a word fragment</li>
                                <li>Description - case insensitive, searches by a word fragment</li>
                                <li>Author - case insensitive, searches by a word fragment in both first and last
                                    names
                                </li>
                                <li>Tags - select one or many tags to pick books by tags</li>
                                <li>ISBN - searches by the entire ISBN - must be exactly the same as specified for the
                                    book
                                </li>
                            </ul>
                            Clear button allows you to quickly reset all the criteria.
                            When you provide your criteria, simply press the Search button.
                        </div>
                        <div>
                            Results are presented using a very simple pagination mechanism. For now, one cannot select
                            the amount of entries present on a single page.
                        </div>
                        <div>
                            To enter book details, simply click its cover image.
                        </div>
                    </div>
                </div>
            </div>
            <!-- Book details page -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="bookDetailsHeader">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#bookDetailsDescriptionPanel"
                            aria-expanded="true"
                            aria-controls="bookDetailsDescriptionPanel">
                        Book details page
                    </button>
                </h2>
                <div id="bookDetailsDescriptionPanel" class="accordion-collapse collapse"
                     aria-labelledby="mainPageHeader">
                    <div class="accordion-body">
                        <div>
                            When you enter page details, you can see:
                            <ul>
                                <li>Book title</li>
                                <li>Book authors</li>
                                <li>Book cover image</li>
                                <li>ISBN</li>
                                <li>State</li>
                                <li>Published date</li>
                                <li>Language</li>
                                <li>Pages</li>
                                <li>Publisher name</li>
                                <li>Tags associated with the book</li>
                                <li>Book pieces availability per state</li>
                                <li>Book description</li>
                                <li>Authors description</li>
                            </ul>
                        </div>
                        <div>
                            All books are reachable by their permalinks - e.g.
                            <a href="/books/the-complete-works-of-william-shakespeare-leather-bound-classics-2014-10-01">
                                The Complete Works of William Shakespeare (Leather-bound Classics) by William
                                Shakespeare
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sign in page -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="signInHeader">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#signInPanel"
                            aria-expanded="true"
                            aria-controls="signInPanel">
                        Sign in page
                    </button>
                </h2>
                <div id="signInPanel" class="accordion-collapse collapse"
                     aria-labelledby="mainPageHeader">
                    <div class="accordion-body">
                        <div>
                            This is a very simple page where you can log in using your e-mail address and password.
                        </div>
                        <div>
                            If you don't have an account, you can click the Sign Up button to enter the registration
                            form.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sign up page -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="signUpHeader">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#signUpPanel"
                            aria-expanded="true"
                            aria-controls="signUpPanel">
                        Sign up page
                    </button>
                </h2>
                <div id="signUpPanel" class="accordion-collapse collapse"
                     aria-labelledby="mainPageHeader">
                    <div class="accordion-body">
                        <div>
                            This is a very simple page where you can register providing your e-mail address and
                            password.
                        </div>
                        <div>
                            E-mail address was supposed to be used to verify if the user is actually the owner of the
                            e-mail
                            address to filter out at least some simplest bots creating accounts all over the
                            Internet.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee features -->
      <?php
      if (\p1\session\SessionManager::instance()->userContext()->hasAnyRole(\p1\core\domain\user\auth\Roles::EMPLOYEE->name)) {
        require_once "view/about/about-employee-features.php";
      }
      ?>
    </div>
</div>
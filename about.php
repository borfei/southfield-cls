<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="css/fonts.css">
    <script src="lib/jquery-3.7.1-dist/jquery.min.js"></script>
    <script src="lib/bootstrap-3.4.1-dist/js/bootstrap.min.js"></script>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>About &mdash; SCLS</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbarCollapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <img alt="PSS Icon" src="assets/icon.png" height="64px">
                    </a>
                    <p class="navbar-text text-uppercase"><strong>Computerized Library System</strong></p>
                </div>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="index.php"> <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Return to Previous Page</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="jumbotron about">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>The Purpose of Computerized Library System</h2>
                    <p class="text-justify">
                        The Philippine <strong>S</strong>outhfield School <strong>C</strong>omputerized <strong>L</strong>ibrary <strong>S</strong>ystem (or <strong>SCLS</strong>) is designed to modernize the book borrowing process within the school.
                        By shifiting from manual record-keeping to a computerized system, it ensures a more efficient and organized approach to managing library transactions.
                        This system primarily focses on tracking book borrowing activities, providing structured way for students to request books while allowing librarian to monitor transactions seamlessly.
                    </p>
                </div>
                <div class="col-md-5">
                    <img class="about purpose" src="assets/logo-alt.png" alt="PSS Logo" />
                </div>
            </div>
        </div>
    </div>
    <div class="container content about">
        <div class="row">
            <div class="col-md-6">
                <h3>How does it work?</h3>
                <p class="text-justify">
                    The system simplifies book borrowing by replacing manual records with a digital process.
                    Students can check book availability and request to borrow through the system, which then notifies the librarian.
                    Once approved, the system records the students name, book title, and borrowing date, ensuring organized tracking of borrowed materials.
                    When a book is returned, the librarian updates the system to mark it as available again.
                    This prevents issues like double lending or misplaced records, keeping everything in order.
                    Overall, SCLS makes borrowing books faster, more efficient, and well-organized, benefiting both students and librarians.
                </p>
            </div>
            <div class="col-md-6">
                <h3>How does it speed up the book borrowing process?</h3>
                <p class="text-justify">
                    It speeds up the borrowing process by making book requests and record-keeping more efficient.
                    Instead of searching through physical records, students can quickly check book availability and request to borrow through the system.
                    This eliminates the need for verbal inquiries and speeds up the librarian's workflow by providing instant notifications of requests.
                    Although students still need to pick up the book and manually log the transaction, the system reduces the time spent on searching and requesting.
                    Since all book details are already recorded digitally, the librarian can process requests faster, ensuring a smoother and more organized borrowing experience.
                </p>
            </div>
            <div class="col-md-6">
                <h3>How does it provide a user-friendly book search experience for students?</h3>
                <p class="text-justify">
                    It enhances the book search process by allowing students to check availability digitally, eliminating the need for manual shelf searches or direct inquiries with the librarian.
                    By providing a structured and accessible database, students can efficiently locate the books they need, reducing time spent searching and improving overall convenience within the library.
                </p>
            </div>
            <div class="col-md-6">
                <h3>How does it ensure efficient book returns and real-time availability updates?</h3>
                <p class="text-justify">
                    When a student returns a book, the librarian can quickly update its status in the system, marking it as available for the next borrower.
                    This ensures that book availability is always up to date, preventing confusion about whether a book is still checked out or ready for borrowing.
                    By automating this part of the process, the system helps maintain an accurate and organized inventory, making it easier for both students and librarians to manage book returns efficiently.
                </p>
            </div>
            <div class="col-md-12">
                <hr>
            </div>
            <div class="col-md-12">
                <h3>In summary, the SCLS:</h3>
                <ul>
                    <li>Modernizes the book borrowing process by replacing manual records with a digital system.</li>
                    <li>Speeds up book requests and record-keeping, making it more efficient for both students and librarians.</li>
                    <li>Provides a user-friendly book search experience, allowing students to check availability digitally.</li>
                    <li>Ensures efficient book returns and real-time availability updates, maintaining an organized inventory.</li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>
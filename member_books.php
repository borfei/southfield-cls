<?php
require("components/bootstrap/alerts.php");
require("components/scls/document.php");
require("components/scls/mysqli.php");
require("components/scls/session.php");
require("components/sync.php");

// Forbid access if the user is not a member
if (scls_session_user()["role"] != "member") {
    scls_document_navigate("error.php?status=403", true);
}

/**
 * Create a transaction for a book if the user does not already have 10 transactions.
 *
 * @param int $id The ID of the book to create a transaction for.
 */
function create_transaction(int $id): void
{
    if ($id == -1) {
        bs_alerts_create(BSAlertClass::Danger, "Invalid book ID.");
        return;
    }

    // CHECK: Book

    $statement = "SELECT 
                        title,
                        author,
                        year,
                        quantity,
                        (SELECT COUNT(*) FROM transactions
                            WHERE book = ? AND status IN ('Accepted', 'Borrowed')
                        ) AS quantity_taken
                    FROM books
                    WHERE id = ?";
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("ii", $id, $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "The specified book does not exist.");
        return;
    }

    $book = $result->fetch_assoc(); // store the book data for later use
    $result->close(); // needed for later queries

    // CHECK: Book & User's Quantity

    if ($book["quantity_taken"] > $book["quantity"]) {
        bs_alerts_create(BSAlertClass::Danger, "The specified book is no longer available to borrow.");
        return;
    }

    $statement = "SELECT COUNT(*) as quantity
                    FROM transactions
                    WHERE user = ?
                    AND status IN ('Pending', 'Accepted', 'Borrowed')";
    $user = scls_session_user(); // current user's data
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("s", $user["name"]);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "Failed to validate user credentials, please relogin.");
        return;
    }

    $user_quantity = $result->fetch_assoc()["quantity"]; // check user quantity
    $result->close(); // needed for later queries

    if ($user_quantity >= 10) {
        bs_alerts_create(BSAlertClass::Danger, "Failed to request a borrow for the specified book, you already have 10 transactions.");
        bs_alerts_create(BSAlertClass::Info, "To fix this issue, you must return at least one of your current borrowed books.");
        return;
    }

    // TRANSACTION: Create Transaction

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("INSERT INTO transactions(book, user) VALUES (?, ?)");
    $query->bind_param("is", $id, $user["name"]);
    $result = $query->execute();
    scls_mysqli()->commit();

    if ($result) {
        bs_alerts_create(BSAlertClass::Success, "Transaction created for book: <strong>$book[title]</strong> ($book[year]) by <strong>$book[author]</strong>");
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Failed to create a transaction for the specified book, please try again later.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require("components/document/charset.php"); ?>
    <?php require("components/document/viewport.php"); ?>
    <?php require("components/document/styles.php"); ?>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>Books &mdash; SCLS</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = strtolower($_POST["action"]);

        switch ($action) {
            case "create":
                create_transaction(intval($_POST["id"] ?? -1));
                break;
            default:
                bs_alerts_create(BSAlertClass::Warning, "Action invalid or not recognized.");
        }
    }

    $search = htmlspecialchars($_GET["search"] ?? "");
    ?>
    <header><?php require("components/navbar/member.php"); ?></header>
    <div class="jumbotron">
        <div class="container">
            <h1>Books</h1>
        </div>
    </div>
    <div class="container content">
        <!-- Alert -->
        <?php bs_alerts_display(); ?>
        <!-- List -->
        <?php
        $statement = "SELECT
                            *,
                            COALESCE(t.quantity_taken, 0) as quantity_taken,
                            CASE 
                                WHEN b.quantity > COALESCE(t.quantity_taken, 0)
                                THEN 'Available'
                                ELSE 'Unavailable'
                            END AS status
                        FROM books b
                        LEFT JOIN (
                            SELECT book, COUNT(*) as quantity_taken
                            FROM transactions
                            WHERE status IN ('Pending', 'Accepted', 'Borrowed')
                            GROUP BY book
                        ) t ON b.id = t.book
                        WHERE
                            b.title LIKE '%{$search}%' OR
                            b.author LIKE '%{$search}%' OR
                            b.category LIKE '%{$search}%'
                        ORDER BY b.id DESC";
        $query = scls_mysqli()->prepare($statement);
        $query->execute();
        $result = $query->get_result();
        ?>
        <?php if ($result->num_rows > 0): ?>
            <!-- Search Input -->
            <form method="get" role="search">
                <div class="form-group input-group">
                    <input type="search" class="form-control" placeholder="Search" name="search" value="<?php echo $search; ?>">
                    <span class="input-group-btn">
                        <?php if (!empty($search)): ?>
                            <a class="btn btn-default" href="member_books.php"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </span>
                </div>
            </form>
            <?php if (!empty($search)): ?>
                <?php $returned_plural = ($result->num_rows > 1) ? "results" : "result"; ?>
                <p class="text-muted">The search query returned matching <?php echo "$result->num_rows $returned_plural"; ?>:</p>
            <?php endif; ?>
            <ul class="list-group">
                <?php while ($book = $result->fetch_assoc()): ?>
                    <?php
                    if (is_readable("public/books/$book[id].png")) {
                        $image = "public/books/$book[id].png";
                    } else {
                        $image = "assets/book-placeholder.png";
                    }
                    ?>
                    <li class="list-group-item <?php echo ($book["status"] != "Available") ? "disabled" : ""; ?>">
                        <div class="media">
                            <div class="media-left">
                                <img class="media-object img-thumbnail" width="128px" height="128px" src="<?php echo $image; ?>" alt="<?php echo $book["title"]; ?>">
                            </div>
                            <div class="media-body media-middle">
                                <h4 class="media-heading">
                                    <strong><?php echo ucwords($book["title"]); ?></strong>
                                    <small><?php echo ucwords($book["author"]); ?></small>
                                </h4>
                                <h5>
                                    Category: <?php echo ucfirst($book["category"]); ?>
                                    <br>
                                    Year: <?php echo $book["year"]; ?>
                                    <br>
                                    Quantity: <?php echo $book["quantity"] - $book["quantity_taken"]; ?>
                                </h5>
                                <?php if ($book["status"] == "Available"): ?>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#scls-transaction-modal-create-<?php echo $book["id"]; ?>">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                        Request Borrow
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php include("components/modals/transaction/member_create.php"); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php elseif (!empty($search)): ?>
            <p class="text-muted">Couldn't find any results from the search query.</p>
        <?php else: ?>
            <p class="text-muted">No books found, contact the administrator if this problem still persists.</p>
        <?php endif; ?>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>
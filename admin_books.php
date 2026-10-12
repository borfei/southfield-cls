<?php
require("components/bootstrap/alerts.php");
require("components/scls/document.php");
require("components/scls/mysqli.php");
require("components/scls/session.php");
require("components/sync.php");

// Forbid access if the user is not an admin
if (scls_session_user()["role"] != "admin") {
    scls_document_navigate("error.php?status=403", true);
}

/**
 * Adds a new book to the database, given the specified fields and the book preview image.
 *
 * @param array $fields The fields to be inserted into the database.
 * @param array $upload The book preview image to be uploaded.
 */
function add_book(array $fields, array $upload): void
{
    $valid = true;

    foreach ($fields as $value) {
        if (empty($value)) {
            $valid = false;
            break;
        }
    }
    if ($valid) {
        scls_mysqli()->begin_transaction();
        $insert_params = [
            ucwords($fields["title"]), // Title (captialized first letter of every word)
            $fields["author"], // Author(s)
            $fields["publisher"], // Publisher
            $fields["year"], // Year (or Publication Year)
            $fields["category"], // Category
            $fields["quantity"] // Quantity
        ];
        $insert_query = "INSERT INTO books(
                                            title, author,
                                            publisher, year,
                                            category, quantity
                                        )
                            VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = scls_mysqli()->prepare($insert_query);
        $insert_result = $insert_stmt->execute($insert_params);
        scls_mysqli()->commit();

        if ($insert_result) {
            if ($upload["error"] == UPLOAD_ERR_OK) {
                $upload_destination = "public/books/$insert_stmt->insert_id.png";

                if (file_exists($upload_destination)) {
                    unlink($upload_destination);
                }
                if (!move_uploaded_file($upload["tmp_name"], $upload_destination)) {
                    bs_alerts_create(BSAlertClass::Warning, "Book image could not be uploaded.");
                }
            } else {
                bs_alerts_create(BSAlertClass::Warning, "Book image is invalid, please select a valid image.");
            }

            bs_alerts_create(BSAlertClass::Success, "Book added: <strong>$insert_params[0]</strong> ($insert_params[3]) by <strong>$insert_params[1]</strong>");
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Failed to add book, please try again later.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Please fill out the required fields.");
    }
}
/**
 * Edits an existing book in the database, given the specified fields and the book preview image.
 *
 * @param array $fields The fields to be updated in the database.
 * @param array $upload The book preview image to be uploaded.
 */
function edit_book(array $fields, array $upload): void
{
    $valid = true;

    foreach ($fields as $value) {
        if (empty($value)) {
            $valid = false;
            break;
        }
    }
    if ($valid) {
        $get_query = "SELECT title, author, year FROM books WHERE id = ?";
        $get_stmt = scls_mysqli()->prepare($get_query);
        $get_stmt->bind_param("i", $fields["id"]);
        $get_stmt->execute();
        $get_result = $get_stmt->get_result();

        if ($get_result->num_rows > 0) {
            $book = $get_result->fetch_assoc();

            scls_mysqli()->begin_transaction();
            $update_params = [
                ucwords($fields["title"]), // New Title (capitalized first letter of every word)
                $fields["author"], // New Author(s)
                $fields["publisher"], // New Publisher
                $fields["year"], // New Year (or Publication Year)
                $fields["category"], // New Category
                $fields["quantity"], // New Quantity
                $fields["id"] //  Specified Existing Book ID
            ];
            $update_query = "UPDATE books
                                    SET
                                        title = ?,
                                        author = ?,
                                        publisher = ?,
                                        year = ?,
                                        category = ?,
                                        quantity = ?
                                    WHERE id = ?";
            $update_stmt = scls_mysqli()->prepare($update_query);
            $update_result = $update_stmt->execute($update_params);
            scls_mysqli()->commit();

            if ($update_result) {
                if ($upload["error"] == UPLOAD_ERR_OK) {
                    $upload_destination = "public/books/$fields[id].png";

                    if (file_exists($upload_destination)) {
                        unlink($upload_destination);
                    }
                    if (!move_uploaded_file($upload["tmp_name"], $upload_destination)) {
                        bs_alerts_create(BSAlertClass::Warning, "Finalization failed for book image.");
                    }
                }

                bs_alerts_create(BSAlertClass::Success, "Book updated: <strong>$book[title]</strong> ($book[year]) by <strong>$book[author]</strong> -> <strong>$update_params[0]</strong> ($update_params[3]) by <strong>$update_params[1]</strong>");
            } else {
                bs_alerts_create(BSAlertClass::Danger, "Failed to update the specified book, try again later.");
            }
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Specified book is no longer available.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Please fill out the required fields.");
    }
}
/**
 * Removes the book with the specified ID from the database.
 *
 * @param int $id The ID of the book to be removed.
 */
function remove_book(int $id): void
{
    if ($id != -1) {
        $id = $_POST["id"];
        $get_query = "SELECT title, author, year FROM books WHERE id = ?";
        $get_stmt = scls_mysqli()->prepare($get_query);
        $get_stmt->bind_param("i", $id);
        $get_stmt->execute();
        $get_result = $get_stmt->get_result();

        if ($get_result->num_rows > 0) {
            $book = $get_result->fetch_assoc();

            scls_mysqli()->begin_transaction();
            $delete_query = "DELETE FROM books WHERE id = ?";
            $delete_stmt = scls_mysqli()->prepare($delete_query);
            $delete_stmt->bind_param("i", $id);
            $delete_result = $delete_stmt->execute();
            scls_mysqli()->commit();

            if (file_exists("public/books/$id.png")) {
                unlink("public/books/$id.png");
            }
            if ($delete_result) {
                scls_mysqli()->begin_transaction();
                $alter_query = "ALTER TABLE books AUTO_INCREMENT = 1";
                $alter_stmt = scls_mysqli()->prepare($alter_query);
                $alter_result = $alter_stmt->execute();

                if (!$alter_result) {
                    bs_alerts_create(BSAlertClass::Warning, "Couldn't reset auto-increment value.");
                }

                bs_alerts_create(BSAlertClass::Success, "Book removed: <strong>$book[title]</strong> ($book[year]) by <strong>$book[author]</strong>");
            } else {
                bs_alerts_create(BSAlertClass::Danger, "Failed to remove the specified book, try again later.");
            }
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Specified book is no longer available.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Invalid book ID.");
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
    <title>Books &mdash; SCLS (Admin)</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = strtolower($_POST["action"]);

        switch ($action) {
            case "add":
                add_book([
                    "title" => htmlspecialchars($_POST["title"] ?? ""),
                    "author" => htmlspecialchars($_POST["author"] ?? ""),
                    "publisher" => htmlspecialchars($_POST["publisher"] ?? ""),
                    "year" => htmlspecialchars($_POST["year"] ?? date("Y")),
                    "category" => htmlspecialchars($_POST["category"] ?? "Unsorted"),
                    "quantity" => htmlspecialchars($_POST["quantity"] ?? "1")
                ], $_FILES["image"]);
                break;
            case "edit":
                edit_book([
                    "id" => $_POST["id"] ?? "",
                    "title" => htmlspecialchars($_POST["title"] ?? ""),
                    "author" => htmlspecialchars($_POST["author"] ?? ""),
                    "publisher" => htmlspecialchars($_POST["publisher"] ?? ""),
                    "year" => htmlspecialchars($_POST["year"] ?? date("Y")),
                    "category" => htmlspecialchars($_POST["category"] ?? "Unsorted"),
                    "quantity" => htmlspecialchars($_POST["quantity"] ?? "1")
                ], $_FILES["image"]);
                break;
            case "remove":
                remove_book(intval($_POST["id"] ?? -1));
                break;
            default:
                bs_alerts_create(BSAlertClass::Warning, "Action invalid or not recognized.");
        }
    }

    $search = htmlspecialchars($_GET["search"] ?? "");
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
                    WHERE status IN ('Accepted', 'Borrowed')
                    GROUP BY book
                ) t ON b.id = t.book
                WHERE
                    b.id LIKE '%{$search}%' OR
                    b.title LIKE '%{$search}%' OR
                    b.author LIKE '%{$search}%' OR
                    b.category LIKE '%{$search}%'
                ORDER BY b.id DESC";
    $query = scls_mysqli()->prepare($statement);
    $query->execute();
    $result = $query->get_result();
    ?>
    <div class="container content">
        <header><?php require("components/navbar/admin.php"); ?></header>
        <div class="page-header">
            <h1>
                Books
                <?php if ($result->num_rows > 0): ?>
                    <small>(<?php echo $result->num_rows; ?>)</small>
                <?php endif; ?>
            </h1>
        </div>
        <div class="row">
            <!-- Add Book -->
            <div class="col-md-9">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-book-modal-add">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New
                </button>
                <?php require("components/modals/book/add.php"); ?>
            </div>
            <!-- Search Input -->
            <div class="col-mwd-3">
                <form method="get" role="search">
                    <div class="form-group input-group">
                        <input type="search" class="form-control" placeholder="e.g. Ham On Rye" name="search" value="<?php echo $search; ?>">
                        <span class="input-group-btn">
                            <?php if (!empty($search)): ?>
                                <a class="btn btn-default" href="admin_books.php"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <br class="visible-xs-block">
        <!-- Alert -->
        <?php bs_alerts_display(); ?>
        <!-- Table -->
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>Year</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($book = $result->fetch_assoc()): ?>
                        <?php
                        if (is_readable("public/books/$book[id].png")) {
                            $image = "public/books/$book[id].png";
                        } else {
                            $image = "assets/book-placeholder.png";
                        }
                        ?>
                        <tr>
                            <td>
                                <img class="img-thumbnail" width="128px" height="128px" src="<?php echo $image; ?>" alt="<?php echo $book["title"]; ?>">
                            </td>
                            <td><?php echo $book["title"]; ?></td>
                            <td><?php echo $book["author"]; ?></td>
                            <td><?php echo $book["publisher"]; ?></td>
                            <td><?php echo $book["year"]; ?></td>
                            <td><?php echo $book["category"]; ?></td>
                            <td class="<?php echo ($book["status"] == "Available") ? "success" : "danger"; ?>">
                                <?php echo $book["quantity"] - $book["quantity_taken"]; ?> / <?php echo $book["quantity"]; ?>
                            </td>
                            <td style="width: 10%;">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-book-modal-edit-<?php echo $book["id"]; ?>">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#scls-book-modal-remove-<?php echo $book["id"]; ?>">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <?php require("components/modals/book/edit.php"); ?>
                                <?php require("components/modals/book/remove.php"); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php elseif (!empty($search)): ?>
            <p class="text-muted">The system couldn't find any results from the search query.</p>
        <?php else: ?>
            <p class="text-muted">Hmm, there aren't any books.</p>
        <?php endif; ?>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>
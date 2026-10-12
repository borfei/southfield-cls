<div class="modal centered fade" id="scls-book-modal-remove-<?php echo $book["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-book-modal-remove-<?php echo $book["id"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-book-modal-remove-<?php echo $book["id"]; ?>-label"><strong>Remove Book</strong></h4>
            </div>
            <div class="modal-body">
                <form id="scls-form-book-remove-<?php echo $book["id"]; ?>" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="id" value="<?php echo $book["id"]; ?>">
                </form>
                <p>Do you want to remove this book? <span class="text-danger">This action cannot be undone.</span></p>
                <div class="media">
                    <div class="media-left">
                        <?php
                        // Get book image, if available, otherwise use placeholder image
                        if (is_readable("public/books/$book[id].png")) {
                            $image = "public/books/$book[id].png";
                        } else {
                            $image = "assets/book-placeholder.png";
                        }
                        ?>
                        <img class="media-object img-thumbnail" width="48px" height="48px" src="<?php echo $image; ?>" alt="<?php echo $book["title"]; ?>">
                    </div>
                    <div class="media-body media-middle">
                        <h4 class="media-heading">
                            <?php echo $book["title"]; ?>
                            <br>
                            <small><?php echo $book["author"]; ?></small>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-book-remove-<?php echo $book["id"]; ?>">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
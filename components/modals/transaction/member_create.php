<div class="modal centered fade" id="scls-transaction-modal-create-<?php echo $book["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-transaction-modal-create-<?php echo $book["id"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-transaction-modal-create-<?php echo $book["id"]; ?>-label"><strong>Request Borrow</strong></h4>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>" id="scls-form-transaction-create-<?php echo $book["id"]; ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="id" value="<?php echo $book["id"]; ?>">
                </form>
                <p>Do you want to borrow this book? If you confirm, a new transaction will be created.</p>
                <div class="media">
                    <div class="media-left">
                        <img class="media-object img-thumbnail" width="48px" height="48px" src="<?php echo $image; ?>" alt="<?php echo $book["title"]; ?>">
                    </div>
                    <div class="media-body media-middle">
                        <h4 class="media-heading">
                            <?php echo $book["title"]; ?>
                            <small><?php echo $book["year"]; ?></small>
                            <br>
                            <small><?php echo $book["author"]; ?></small>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-transaction-create-<?php echo $book["id"]; ?>">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
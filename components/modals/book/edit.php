<div class="modal fade" id="scls-book-modal-edit-<?php echo $book["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-book-modal-edit-<?php echo $book["id"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-book-modal-edit-<?php echo $book["id"]; ?>-label"><strong>Edit Book</strong></h4>
            </div>
            <div class="modal-body">
                <div class="well well-lg">
                    <form id="scls-form-book-edit-<?php echo $book["id"]; ?>" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $book["id"]; ?>">
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" class="form-control" name="image" accept="image/png">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" class="form-control" name="title" value="<?php echo $book["title"]; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Author</label>
                                    <input type="text" class="form-control" name="author" value="<?php echo $book["author"]; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Publisher</label>
                                    <input type="text" class="form-control" name="publisher" value="<?php echo $book["publisher"]; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Year</label>
                                    <input type="number" class="form-control" name="year" min="1900" max="2099" value="<?php echo $book["year"]; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-control" name="category" required>
                                        <option value="Unsorted" selected>Unsorted</option>
                                        <option value="CIR SHS">CIR SHS</option>
                                        <option value="FIL SHS">FIL SHS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control" name="quantity" min="1" max="30" value="<?php echo $book["quantity"]; ?>" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-book-edit-<?php echo $book["id"]; ?>">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
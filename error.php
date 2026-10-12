<!DOCTYPE html>
<html lang="en">

<?php
// If "status" query parameter is set, use it to determine code
// Otherwise, use 200 OK as the default status error code
$status = (isset($_GET["status"])) ? $_GET["status"] : 200;

// Use the friendly-descriptive text for the current status
//
// The page supports various HTTP status codes, published under
// RFC 9110 to document these codes, and to help and guide project
// maintainers / administrators about page navigations & network status
switch ($status) {
    case 101:
        $status_description = "Protocol is being switched to another protocol";
        break;
    case 102:
        $status_description = "Request is being processed";
        break;
    case 103:
        $status_description = "Resources are being pre-loaded";
        break;
    case 200:
        $status_description = "Request is OK";
        break;
    case 201:
        $status_description = "Result OK / Request OK";
        break;
    case 401:
        $status_description = "Unauthorized access to content";
        break;
    case 403:
        $status_description = "Content forbidden";
        break;
    case 404:
        $status_description = "Content not found";
        break;
    case 500:
        $status_description = "Internal server error";
        break;
    default:
        $status_description = "Could not state error code. $status";
}
?>

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
    <title>Philippine Southfield CLS</title>
</head>

<body class="error">
    <div class="container content">
        <!-- Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 text-left">
                    <img class="img-responsive" src="assets/logo.png" width="250px" alt="PSS Logo">
                </div>
                <div class="col-md-6 text-right">
                    <small>Code <?php echo $status; ?></small>
                    <br>
                    <small><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></small>
                    <br>
                    <small><?php echo "$_SERVER[SERVER_PROTOCOL] &mdash; $_SERVER[GATEWAY_INTERFACE]"; ?>
                    </small>
                </div>
            </div>
        </div>
        <!-- Error Text -->
        <samp><?php echo $status_description; ?></samp>
    </div>
</body>

</html>
<?php
/**
 * Navigates to the specified document via HTTP redirect.
 * 
 * This function constructs a URL from the provided document name and
 * issues an HTTP redirect to the constructed URL. If the optional
 * parameter $die is set to true, the script will terminate execution
 * after issuing the redirect.
 * 
 * @param string $document The document to navigate to
 * @param bool $die [optional] Whether to die after navigation
 */
function scls_document_navigate(string $document, bool $die = false): void
{
    header("location: $document");

    if ($die) {
        die("The document requested to stop processing after navigation");
    }
}

/**
 * Returns the referring document, if set.
 * 
 * This function retrieves the referring document from the query string
 * parameter "referrer" and returns it. If the parameter is not set, the
 * function returns the provided fallback value instead.
 * 
 * @param string $fallback [optional] The value to return if there is no referring document
 * @return string The referring document, or the fallback value if not set
 */
function scls_document_referrer(string $fallback = ""): string
{
    return (!empty($_GET["referrer"])) ? $_GET["referrer"] : $fallback;
}
?>
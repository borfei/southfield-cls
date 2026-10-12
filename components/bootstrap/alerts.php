<?php
$__bs_alerts = [];

/**
 * Enum representing the class of Bootstrap alerts that can be created.
 *
 * This enum provides a set of predefined alert classes that are used
 * to specify the category of an alert, such as success, info, warning,
 * or danger.
 */
enum BSAlertClass: string
{
    case Success = "success"; // Represents a success alert
    case Info = "info";       // Represents an informational alert
    case Warning = "warning"; // Represents a warning alert
    case Danger = "danger";   // Represents a danger alert
}

/**
 * Clears all created Bootstrap alerts.
 */
function bs_alerts_clear(): void
{
    global $__bs_alerts;
    $__bs_alerts = array();
}

/**
 * Creates a new Bootstrap alert.
 * 
 * To display the newly created alert, use the scls_alerts_display() function.
 *
 * @param BSAlertClass $class The context class of the alert to be added.
 * @param string $message The message of the alert to be added.
 * @param bool $dismissable Whether the alert is dismissable or not.
 */
function bs_alerts_create(BSAlertClass $class, string $message, bool $dismissable = true): void
{
    global $__bs_alerts;
    array_push($__bs_alerts, [
        "class" => $class,
        "dismissable" => $dismissable,
        "message" => $message
    ]);
}

/**
 * Removes an existing Bootstrap alert at the specified index.
 *
 * @param int $index The index of the alert to be deleted.
 */
function bs_alerts_remove(int $index): void
{
    global $__bs_alerts;
    unset($__bs_alerts[$index]);
}

/**
 * Displays the Bootstrap alerts that have been created.
 *
 * The HTML elements are divs with the Bootstrap CSS class "alert" and the
 * appropriate class for the alert type (e.g. "alert-danger" for a danger
 * alert). They also have the class "alert-dismissible" if the alert is set as dismissable.
 */
function bs_alerts_display(): void
{
    global $__bs_alerts;

    if (!is_array($__bs_alerts)) {
        return;
    }
    foreach ($__bs_alerts as $alert) {
?>
        <div class="alert alert-<?php echo $alert["class"]->value ?? "info"; ?> <?php echo $alert["dismissable"] ? "alert-dismissible" : ""; ?>" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $alert["message"] ?? "No message provided."; ?>
        </div>
<?php
    }
}
?>
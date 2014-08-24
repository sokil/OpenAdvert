<h1>Error</h1>
<?php
if (isset($message)) {
    echo $message;
} else {
    echo _('Error occured while handling your request. Administration noticed about this and already trying to search a resolution');
}

<?php
if(file_exists('.htaccess')) {
    echo ".htaccess file exists. Renaming it...";
    rename('.htaccess', '.htaccess.backup');
    echo "Renamed to .htaccess.backup";
} else {
    echo "No .htaccess file found.";
}
?>
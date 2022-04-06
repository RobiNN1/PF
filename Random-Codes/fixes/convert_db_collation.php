<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: convert_db_collation.php
| Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once __DIR__.'/maincore.php';
require_once THEMES.'templates/header.php';

echo '<h1 class="text-center">Convert DB Collation</h1>';

$result = dbquery("SELECT @@character_set_database as charset, @@collation_database as collation;");
while ($db = dbarray($result)) {
    if ($db['charset'] == 'utf8') {
        dbquery("SET NAMES utf8mb4");
    }

    if ($db['collation'] == 'utf8_general_ci') {
        dbquery("ALTER DATABASE ".$db_name." CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;");
    }
}

$result = dbquery("SHOW TABLES");
while ($table = dbarraynum($result)) {
    if (preg_match("/^".DB_PREFIX."/i", $table[0])) {
        dbquery("ALTER TABLE ".$table[0]." CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    }
}

require_once THEMES.'templates/footer.php';

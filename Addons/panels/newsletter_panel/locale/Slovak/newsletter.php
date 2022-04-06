<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: newsletter.php
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
$locale['nsl_title'] = 'Newsletter';
$locale['nsl_desc'] = 'Newsletter systém.';
$locale['nsl_001'] = 'Šablóny';
$locale['nsl_002'] = 'Odberatelia';
$locale['nsl_003'] = 'Nastavenia';
$locale['nsl_004'] = 'Vybrať šablónu';
$locale['nsl_005'] = 'Poslať newsletter';
$locale['nsl_006'] = 'Odosielanie';
$locale['nsl_007'] = 'Ďalšie hlavičky';
$locale['nsl_008'] = 'Meno odosielateľa';
$locale['nsl_009'] = 'Email odosielateľa';
$locale['nsl_010'] = 'Zobraziť email odosielateľa v odoslaných emailoch';
$locale['nsl_011'] = 'Znaková sada';
$locale['nsl_012'] = 'Typ obsahu';
$locale['nsl_013'] = 'Obyčajný text';
$locale['nsl_014'] = 'Email na testovanie šablón';
$locale['nsl_015'] = 'Viditeľnosť newsletter formulára';
$locale['nsl_016'] = 'Ako poslať';
$locale['nsl_017'] = 'Cesta k sendmail';
$locale['nsl_018'] = 'Pridať DKIM signature do hlavičky emailu';
$locale['nsl_019'] = 'Názov';
$locale['nsl_020'] = 'Hodnota';
$locale['nsl_021'] = 'Žiadne vlastné hlavičky';
$locale['nsl_022'] = 'Host';
$locale['nsl_023'] = 'Port';
$locale['nsl_024'] = 'Užívateľské meno';
$locale['nsl_025'] = 'Heslo';
$locale['nsl_026'] = 'Timeout';
$locale['nsl_027'] = 'Povoliť server';
$locale['nsl_028'] = 'Zabezpečené pripojenie';
$locale['nsl_029'] = 'Povolené';
$locale['nsl_030'] = 'Zakázané';
$locale['nsl_031'] = 'Žiadne SMTP servery.';
$locale['nsl_032'] = 'Email';
$locale['nsl_033'] = 'Hostia';
$locale['nsl_034'] = 'Členovia';
$locale['nsl_035'] = 'Užívateľ';
$locale['nsl_036'] = 'Žiadny odberatelia.';
$locale['nsl_037'] = 'Aktivovať';
$locale['nsl_038'] = 'Deaktivovať';
$locale['nsl_039'] = 'Aktívny';
$locale['nsl_040'] = 'Pripojil/a sa';
$locale['nsl_041'] = 'Prázdne';
$locale['nsl_042'] = 'Súbor';
$locale['nsl_043'] = 'Priorita';
$locale['nsl_044'] = 'normálna';
$locale['nsl_045'] = 'nízka';
$locale['nsl_046'] = 'vysoká';
$locale['nsl_047'] = 'Testovať šablónu';
$locale['nsl_048'] = 'Prázdne';
$locale['nsl_049'] = '1 stĺpec';
$locale['nsl_050'] = '2 stĺpce';
$locale['nsl_051'] = 'Vytvorené dňa';
$locale['nsl_052'] = 'Žiadne šablóny.';
$locale['nsl_053'] = 'Tento email ste dostali, pretože ste sa prihlásili k odberu nášho newslettereru.';
$locale['nsl_054'] = '[LINK]Odhlásiť sa[/LINK] z našich emailov.';
$locale['nsl_055'] = 'Odhlásiť sa: Prihláste sa do [LINK] a upravte svoj profil.';
$locale['nsl_056'] = 'Odhlásiť sa';
$locale['nsl_057'] = 'Odoberať novinky';
$locale['nsl_058'] = 'Ahoj!<br/>Od nášho newsletteru Vás ďelí už len jeden klik. Kliknutím na odkaz nižšie potvrdíte odber noviniek.';
$locale['nsl_059'] = 'Potvrdiť';
$locale['nsl_060'] = 'Alebo použite tento odkaz:';
$locale['nsl_061'] = 'Potvrdenie odberu';
$locale['nsl_062'] = 'Prihláste sa na odber newsletteru od [SITENAME] a buďte informovaní ako prví.';
$locale['nsl_063'] = 'Nainštalujte newsletter užívateľské pole.';

$locale['nsl_notice_01'] = 'Nastavenia boli aktualizované.';
$locale['nsl_notice_02'] = 'Hlavička bola odstránená.';
$locale['nsl_notice_03'] = 'Hlavička bola aktualizovaná.';
$locale['nsl_notice_04'] = 'Hlavička bola pridaná.';
$locale['nsl_notice_05'] = 'SMTP bol odstránený.';
$locale['nsl_notice_06'] = 'SMTP bol aktualizovaný.';
$locale['nsl_notice_07'] = 'SMTP bol pridaný.';
$locale['nsl_notice_08'] = 'Odberateľ bol odstránený.';
$locale['nsl_notice_09'] = 'Odberateľ bol aktualizovaný.';
$locale['nsl_notice_10'] = 'Odberateľ bol pridaný.';
$locale['nsl_notice_11'] = 'Odberatelia boli aktivovaní.';
$locale['nsl_notice_12'] = 'Odberatelia boli deaktivovaní.';
$locale['nsl_notice_13'] = 'Odberatelia boli odstránení.';
$locale['nsl_notice_14'] = 'Musíte si vybrať aspoň jedného odberateľa.';
$locale['nsl_notice_15'] = 'Šablóna bola odstránená.';
$locale['nsl_notice_16'] = 'Email bol odoslaný na';
$locale['nsl_notice_17'] = 'Nedá sa odoslať email. Dôvod:';
$locale['nsl_notice_18'] = 'Šablóna bola aktualizovaná.';
$locale['nsl_notice_19'] = 'Šablóna bola pridaná.';
$locale['nsl_notice_20'] = 'Šablóny boli odstránené.';
$locale['nsl_notice_21'] = 'Musíte vybrať aspoň jednu šablónu.';
$locale['nsl_notice_22'] = 'Odber newsletteru bol úspešne potvrdený.';
$locale['nsl_notice_23'] = 'Boli ste odhlásení z tohto zoznamu adries a nedostanete od nás žiadne emaily.';
$locale['nsl_notice_24'] = 'Odber bol aktualizovaný.';
$locale['nsl_notice_25'] = 'Skontrolujte svoj email a potvrďte odber.';

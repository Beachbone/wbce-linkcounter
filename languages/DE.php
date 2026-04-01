<?php
/**
 * Link Counter - German Language
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.1.0
 */

if (!defined('WB_PATH')) {
    exit('Direct access is not allowed');
}

// Module Info
$MOD_LINKCOUNTER = array(
    'MODULE_NAME'           => 'Link Counter',
    'MODULE_DESCRIPTION'    => 'Verwalten Sie Links mit Klick-Statistiken',

    // Navigation
    'MENU_OVERVIEW'         => 'Übersicht',
    'MENU_ADD'              => 'Neu hinzufügen',
    'MENU_STATS'            => 'Statistiken',

    // Overview/List
    'HEADING_OVERVIEW'      => 'Link-Übersicht',
    'TEXT_NO_DOWNLOADS'     => 'Noch keine Links vorhanden.',
    'TEXT_ADD_FIRST'        => 'Erstellen Sie Ihren ersten Link.',

    // Table Headers
    'TH_ID'                 => 'ID',
    'TH_TITLE'              => 'Titel',
    'TH_URL'                => 'URL',
    'TH_DESCRIPTION'        => 'Beschreibung',
    'TH_COUNTER'            => 'Klicks',
    'TH_STATUS'             => 'Status',
    'TH_CREATED'            => 'Erstellt',
    'TH_ACTIONS'            => 'Aktionen',

    // Status
    'STATUS_ACTIVE'         => 'Aktiv',
    'STATUS_INACTIVE'       => 'Inaktiv',

    // Add/Edit Form
    'HEADING_ADD'           => 'Neuen Link hinzufügen',
    'HEADING_EDIT'          => 'Link bearbeiten',
    'LABEL_TITLE'           => 'Titel',
    'LABEL_LINK_TYPE'       => 'Link-Typ',
    'LABEL_URL'             => 'Ziel-URL',
    'LABEL_PAGE'            => 'Interne Seite',
    'LABEL_DESCRIPTION'     => 'Beschreibung',
    'LABEL_ACTIVE'          => 'Aktiv',
    'LABEL_OPEN_TARGET'     => 'In neuem Tab öffnen',
    'LINK_TYPE_URL'         => 'Externe URL',
    'LINK_TYPE_PAGE'        => 'Interne Seite',
    'SELECT_PAGE'           => '-- Seite auswählen --',
    'PLACEHOLDER_TITLE'     => 'z.B. Produktkatalog 2026',
    'PLACEHOLDER_URL'       => 'https://example.com/download.pdf',
    'PLACEHOLDER_DESC'      => 'Optional: Beschreibung für interne Zwecke',
    'HELP_TITLE'            => 'Anzeigename für den Link',
    'HELP_LINK_TYPE'        => 'Wählen Sie zwischen externer URL oder interner WBCE-Seite',
    'HELP_URL'              => 'Vollständige URL (z.B. https://example.com/file.pdf)',
    'HELP_PAGE'             => 'Wählen Sie eine Seite aus Ihrer WBCE-Installation',
    'HELP_DESCRIPTION'      => 'Interne Notiz oder Beschreibung',
    'HELP_ACTIVE'           => 'Nur aktive Links werden im Frontend angezeigt',
    'HELP_OPEN_TARGET'      => 'Link wird in einem neuen Browser-Tab geöffnet',

    // Buttons
    'BTN_SAVE'              => 'Speichern',
    'BTN_CANCEL'            => 'Abbrechen',
    'BTN_BACK'              => 'Zurück',
    'BTN_ADD'               => 'Hinzufügen',
    'BTN_EDIT'              => 'Bearbeiten',
    'BTN_DELETE'            => 'Löschen',
    'BTN_RESET_COUNTER'     => 'Zähler zurücksetzen',
    'BTN_EXPORT'            => 'Als CSV exportieren',
    'BTN_VIEW_STATS'        => 'Statistiken',
    'BTN_SETTINGS'          => 'Einstellungen',

    // Messages - Success
    'SUCCESS_SAVED'         => 'Link erfolgreich gespeichert.',
    'SUCCESS_DELETED'       => 'Link erfolgreich gelöscht.',
    'SUCCESS_COUNTER_RESET' => 'Zähler erfolgreich zurückgesetzt.',

    // Messages - Errors
    'ERROR_TITLE_EMPTY'     => 'Bitte geben Sie einen Titel ein.',
    'ERROR_URL_EMPTY'       => 'Bitte geben Sie eine URL ein.',
    'ERROR_URL_INVALID'     => 'Die eingegebene URL ist ungültig.',
    'ERROR_NOT_FOUND'       => 'Link nicht gefunden.',
    'ERROR_DELETE_FAILED'   => 'Fehler beim Löschen des Links.',
    'ERROR_SAVE_FAILED'     => 'Fehler beim Speichern des Links.',
    'ERROR_SECURITY'        => 'Sicherheitsfehler: Ungültiges Token.',

    // Confirm Dialogs
    'CONFIRM_DELETE'        => 'Möchten Sie diesen Link wirklich löschen?',
    'CONFIRM_RESET'         => 'Möchten Sie den Zähler wirklich zurücksetzen?',

    // Statistics
    'HEADING_STATS'         => 'Link-Statistiken',
    'TEXT_TOTAL_CLICKS'     => 'Gesamt-Klicks',
    'TEXT_AVG_CLICKS'       => 'Durchschnitt',
    'TEXT_TOP_DOWNLOADS'    => 'Top Links',

    // Export
    'EXPORT_FILENAME'       => 'linkcounter-export',

    // Droplet Help
    'DROPLET_USAGE_HEADING'     => 'Droplet-Verwendung',
    'DROPLET_CODE_HEADING'      => 'Droplet-Code für diesen Link',
    'DROPLET_CODE_INFO'         => 'Der Linktext wird automatisch aus dem Titelfeld übernommen.',
    'DROPLET_LINKCOUNTER_DESC'  => 'Erstellt einen verfolgten Link mit dem Titel aus der Datenbank als Linktext.',
    'DROPLET_LINKSTATS_DESC'    => 'Zeigt eine Tabelle mit Link-Statistiken der am häufigsten geklickten Links.',

    // Filter
    'FILTER_ALL'            => 'Alle',
    'FILTER_ACTIVE'         => 'Aktiv',
    'FILTER_INACTIVE'       => 'Inaktiv',

    // Sort
    'SORT_BY'               => 'Sortieren nach',
    'SORT_ID_ASC'           => 'ID (aufsteigend)',
    'SORT_ID_DESC'          => 'ID (absteigend)',
    'SORT_TITLE_ASC'        => 'Titel (A-Z)',
    'SORT_TITLE_DESC'       => 'Titel (Z-A)',
    'SORT_CLICKS_ASC'       => 'Klicks (aufsteigend)',
    'SORT_CLICKS_DESC'      => 'Klicks (absteigend)',
    'SORT_DATE_ASC'         => 'Datum (älteste zuerst)',
    'SORT_DATE_DESC'        => 'Datum (neueste zuerst)',

    // Settings
    'HEADING_SETTINGS'              => 'Einstellungen - Crawler-Schutz',
    'SETTINGS_CRAWLER_PROTECTION'   => 'Crawler-Schutz Konfiguration',
    'SETTINGS_CRAWLER_DESCRIPTION'  => 'Der Crawler-Schutz filtert automatisierte Link-Aufrufe anhand der Zeit zwischen Seitenaufbau und Klick. Dies reduziert Bot-Traffic in Ihren Statistiken.',
    'SETTINGS_ENABLE_PROTECTION'    => 'Crawler-Schutz aktivieren',
    'SETTINGS_ENABLE_PROTECTION_HELP' => 'Wenn aktiviert, werden Klicks mit zu kurzer Verzögerung als Crawler erkannt.',
    'SETTINGS_MIN_DELAY'            => 'Minimale Verzögerung (in Millisekunden)',
    'SETTINGS_MIN_DELAY_HELP'       => 'Klicks die schneller erfolgen werden als Crawler erkannt. Empfohlen: 500ms (0,5 Sekunden). Bereich: 100-10000ms.',
    'SETTINGS_CRAWLER_ACTION'       => 'Aktion bei erkanntem Crawler',
    'SETTINGS_CRAWLER_ACTION_HELP'  => 'Wählen Sie, was passieren soll wenn ein Crawler erkannt wird.',
    'SETTINGS_ACTION_SKIP_COUNT'    => 'Weiterleiten ohne zu zählen (empfohlen)',
    'SETTINGS_ACTION_BLOCK'         => 'Nicht weiterleiten',
    'SETTINGS_HOW_IT_WORKS'         => 'Wie funktioniert der Crawler-Schutz?',
    'SETTINGS_HOW_POINT_1'          => 'JavaScript erfasst die Zeit beim Seitenaufbau und beim Klick auf einen Link',
    'SETTINGS_HOW_POINT_2'          => 'Die Zeitstempel werden verschleiert als URL-Parameter übertragen',
    'SETTINGS_HOW_POINT_3'          => 'Der Server prüft die Zeitdifferenz gegen die eingestellte Mindestzeit',
    'SETTINGS_HOW_POINT_4'          => 'Crawler ohne JavaScript oder mit zu kurzer Wartezeit werden automatisch gefiltert',
    'SUCCESS_SETTINGS_SAVED'        => 'Einstellungen erfolgreich gespeichert.',
);

?>

<?php
define("IN_MYBB", 1);
require("global.php");

global $db, $cache, $mybb, $lang, $templates, $theme, $header, $headerinclude, $footer, $page;

    // GÄSTE => ERROR
    if($mybb->user['uid'] == 0) {
        error_no_permission();
    } 

    // KEIN ADMIN  => ERROR
    if($mybb->usergroup['cancp'] != 1) {
        error_no_permission();
    } 

    // Tabelle existiert => kein Update
    if ($db->table_exists("designs_users")) {
        echo "Das Plugin ist schon geupdatet worden auf die aktuellste Version. Du kannst diese Datei löschen.";
    } else {
        // DATENBANK ERSTELLEN
        $db->query("CREATE TABLE ".TABLE_PREFIX."designs_users(	
            `duid` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `uid` int(10) unsigned NOT NULL,
            `style` int(10) unsigned NOT NULL,
            `designname` varchar(500) COLLATE utf8_general_ci NOT NULL,
            `designdimm` int(1) NOT NULL DEFAULT 0,
            `individual_colors` varchar(500) COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY(`duid`),
            KEY `duid` (`duid`)
            )
            ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1
        ");

        // bestehende Werte übertragen
        $query_alluser = $db->query("SELECT uid,style FROM ".TABLE_PREFIX."users
        WHERE designname != ''
        OR designdimm != ''
        OR individual_colors != ''
        ");

        $users_array = [];
        while($alluser = $db->fetch_array($query_alluser)) {

            // Leer laufen lassen
            $uid = "";
            $style = "";

            // Mit Infos füllen
            $uid = $alluser['uid'];
            $style = $alluser['style'];

            $users_array[$uid] = $style;
        }

        foreach ($users_array as $uid => $style) {
            // Benutzer Standard abfangen
            if($style != 0) {
                $style_id = $style;
            } else {
                // Default Design
                $style_id = $db->fetch_field($db->simple_select("themes", "tid", "def = '1'"), "tid");
            }

            // Designwerte
            $designname_user = $db->fetch_field($db->simple_select("users", "designname", "uid = '".$uid."'"), "designname");
            $designdimm_user =$db->fetch_field($db->simple_select("users", "designdimm", "uid = '".$uid."'"), "designdimm");
            $individual_colors_user = $db->fetch_field($db->simple_select("users", "individual_colors", "uid = '".$uid."'"), "individual_colors");

            // Rausfinden, was der Style für ne Option hat
            $designoption_header = $db->fetch_field($db->simple_select("designs", "headerimage", "tid = '".$style_id."'"), "headerimage");
            $designoption_individualcolors = $db->fetch_field($db->simple_select("designs", "individual_colors", "tid = '".$style_id."'"), "individual_colors");

            // Headerimage = Farb-/Headerwechsel
            if (!empty($designoption_header) AND empty($designoption_individualcolors)) {

                // eigener Header ausgesucht
                if (!empty($designname_user)) {
                    $designname = $designname_user;
                }
                // kein Header festgelegt => Standard 
                else {
                    // Standard Design
                    $count_default = $db->num_rows($db->query("SELECT did FROM ".TABLE_PREFIX."designs
                    WHERE tid = '".$style_id."'
                    AND standard = '1'
                    "));
    
                    // Team hat vergessen eine Standardvariante zu setzen
                    if ($count_default < 1) {
                        // Erste Variante => did am kleinsten
                        $default_did = $db->fetch_field($db->simple_select("designs", "did", "tid = '".$style_id."'", ["order_dir" => "ASC", "order_by" => "did", "limit" => "1"]), "did");
                        $default_design = $db->fetch_field($db->simple_select("designs", "name", "did = '".$default_did."'"), "name");
                    } else {
                        // Standard-Modus
                        $default_design = $db->fetch_field($db->simple_select("designs", "name", "tid = '".$style_id."' AND standard = '1'"), "name");
                    }

                    $designname = $default_design;
                }

                $individual_colors = "";
            }
            // Light/Dark Design
            else if (empty($designoption_header) AND empty($designoption_individualcolors)) {
                $designname = "";
                $individual_colors = "";
            }
            // Aktzentfarbe
            else if (empty($designoption_header) AND !empty($designoption_individualcolors)) {
                $designname = "";
                $individual_colors = $individual_colors_user;
            }

            // Daten speichern
            $new_userstyle = array(
                "uid" => (int)$uid,
                "style" => (int)$style_id,
                "designname" => $db->escape_string($designname),
                "designdimm" => $db->escape_string($designdimm_user),
                "individual_colors" => $db->escape_string($individual_colors)
            );                    
            
            $db->insert_query("designs_users", $new_userstyle);
        }

        // DATENBANK SPALTEN LÖSCHEN
        if ($db->field_exists("designname", "users")) {
            $db->drop_column("users", "designname");
        }

        if ($db->field_exists("designdimm", "users")) {
            $db->drop_column("users", "designdimm");
        }

        if ($db->field_exists("individual_colors", "users")) {
            $db->drop_column("users", "individual_colors");
        }

        redirect("index.php", "Das Update wurde erfolgreich ausgeführt. Du wirst nun auf den Index zurückgeleitet.");
    }

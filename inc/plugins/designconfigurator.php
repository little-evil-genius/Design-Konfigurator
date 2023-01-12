<?php
// Direktzugriff auf die Datei aus Sicherheitsgründen sperren
if (!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// HOOKS
$plugins->add_hook("admin_style_action_handler", "designconfigurator_admin_style_action_handler");
$plugins->add_hook("admin_style_permissions", "designconfigurator_admin_style_permissions");
$plugins->add_hook("admin_style_menu", "designconfigurator_admin_style_menu");
$plugins->add_hook("admin_load", "designconfigurator_manage_designconfigurator");
$plugins->add_hook('global_intermediate', 'designconfigurator_headerinclude', 0);
$plugins->add_hook('usercp_start', 'designconfigurator_usercp');
$plugins->add_hook('usercp_menu', 'designconfigurator_usercpmenu', 40);
$plugins->add_hook('datahandler_user_update', 'designconfigurator_userupdate');
$plugins->add_hook("fetch_wol_activity_end", "designconfigurator_online_activity");
$plugins->add_hook("build_friendly_wol_location_end", "designconfigurator_online_location");
// MyAlerts
if (class_exists('MybbStuff_MyAlerts_AlertTypeManager')) {
	$plugins->add_hook("global_start", "designconfigurator_alerts");
}

// Die Informationen, die im Pluginmanager angezeigt werden
function designconfigurator_info() {
	return array(
		"name" => "Design Konfigurator",
		"description" => "Mit diesem Plugin lassen sich über das AdminCP verschiedene Designoptionen per Root Verzeichnis festlegen. Unter anderem Light-/Darkmodes, Designs mit Aktzentfarben, welche die User individuell anpassen können und verschiedene Farb-/Headervarianten. Die User können dann auf einer extra Seite sich ihre Wunschvariante selbstaussuchen.",
		"website" => "https://github.com/little-evil-genius/Design-Konfigurator",
		"author" => "little.evil.genius",
		"authorsite" => "https://storming-gates.de/member.php?action=profile&uid=1712",
		"version" => "1.1",
		"compatibility" => "18*"
	);
}

// Diese Funktion wird aufgerufen, wenn das Plugin installiert wird (optional).
function designconfigurator_install() {
	global $db, $cache, $mybb;

	// DATENBANKSPALTE USERS
	$db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `designname` VARCHAR(500) COLLATE utf8_general_ci NOT NULL;");
	$db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `designdimm` VARCHAR(1) COLLATE utf8_general_ci  NOT NULL;");
	$db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `individual_colors` VARCHAR(500) COLLATE utf8_general_ci  NOT NULL;");

	// DATENBANK HINZUFÜGEN
	$db->query("CREATE TABLE ".TABLE_PREFIX."designs(
        `did` int(10) NOT NULL AUTO_INCREMENT,
        `tid` int(10) NOT NULL,
        `name` varchar(500) COLLATE utf8_general_ci NOT NULL,
        `standard` int(1) NOT NULL DEFAULT 0,
        `root` varchar(10) COLLATE utf8_general_ci NOT NULL,
        `headerimage` varchar(500) COLLATE utf8_general_ci NOT NULL,
        `accentcolor1` varchar(10) COLLATE utf8_general_ci NOT NULL,
        `accentcolor2` varchar(10) COLLATE utf8_general_ci NOT NULL,
        `light_root` longtext COLLATE utf8_general_ci NOT NULL,
        `dark_root` longtext COLLATE utf8_general_ci NOT NULL,
        `path` varchar(500) COLLATE utf8_general_ci NOT NULL,
        `individual_colors` varchar(500) COLLATE utf8_general_ci NOT NULL,
		`allowed_usergroups` varchar(500) COLLATE utf8_general_ci NOT NULL,
		PRIMARY KEY(`did`),
        KEY `did` (`did`)
        )
        ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1
    ");

	// TEMPLATES ERSTELLEN
	// Template Gruppe für jedes Design erstellen
	$templategroup = array(
		"prefix" => "designconfigurator",
		"title" => $db->escape_string("Design Konfigurator"),
	);

	$db->insert_query("templategroups", $templategroup);

	$insert_array = array(
		'title' => 'designconfigurator',
		'template' => $db->escape_string('<html>
        <head>
           <title>{$lang->user_cp} - {$lang->designconfigurator_usercp}</title>
           {$headerinclude}
        </head>
        <body>
           {$header}
           <table width="100%" border="0" align="center">
              <tr>
                 {$usercpnav}
                 <td valign="top" class="tborder">
                    <div id="designconfigurator">
                       <div class="designconfi-headline">{$lang->designconfigurator_usercp}</div>
                       <div class="designconfi-desc">{$lang->designconfigurator_usercp_desc}</div>
                       <div class="designconfi-headline">{$lang->designconfigurator_usercp_mode}</div>
                       <div class="designconfi-mode">
                          {$lightdarkmode}
                       </div>
                       <div class="designconfi-headline">{$lang->designconfigurator_usercp_design}</div>
                       <div class="designconfi-design">
                          {$designswitch}
                       </div>
                       <div class="designconfi-headline">{$lang->designconfigurator_usercp_accentcolors}</div>
                       {$accentcolors}
                    </div>
                 </td>
              </tr>
           </table>
           {$footer}
        </body>
     </html>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_accentcolor',
		'template' => $db->escape_string('<div class="designconfi-accentcolor">
        <table>
           <tbody>
              <form id="designconfigurator_accentcolor" method="post" action="usercp.php?action=designconfigurator_accentcolor">
                 {$accentcolors_add}
                 <tr>
                    <td align="center" colspan="2">
                       <input type="hidden" name="action" value="designconfigurator_accentcolor">
                       <input type="submit" value="{$lang->designconfigurator_accentcolor_button}" name="designconfigurator_accentcolor" class="button">
                    </td>
                 </tr>
              </form>
           </tbody>
        </table>
     </div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_accentcolor_add',
		'template' => $db->escape_string('<tr>
        <td class="trow1" width="40%" align="justify">
      <strong>{$lang->designconfigurator_accentcolor} {$number}</strong><br>
      <span class="smalltext">
         {$lang->designconfigurator_accentcolor_desc}<br>
         <div style="display: flex;align-items: center;">
            <div style="height: 11px;width: 10px;background:{$accentcolor};margin-right: 5px;"></div>
            <div style="color:{$accentcolor};">{$lang->designconfigurator_accentcolor_def} {$number}</div>
         </div>
         {$accentcolors_own}
      </span>
   </td>
   <td class="trow1" width="60%">
      <input type="text" class="textbox" name="accentcolor{$number}" id="accentcolor{$number}" value="{$color_own}" size="40" maxlength="1155">
   </td>

   </tr>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_accentcolor_none',
		'template' => $db->escape_string('<div style="text-align:center;margin:10px auto;">{$lang->designconfigurator_accentcolor_none}</div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_accentcolor_own',
		'template' => $db->escape_string('<div style="display: flex;align-items: center;">
    <div style="height: 11px;width: 10px;background:{$color_own};margin-right: 5px;"></div>
    <div style="color:{$color_own};">{$lang->designconfigurator_accentcolor_own_number} {$number}</div>
    </div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_designswitch',
		'template' => $db->escape_string('<div class="designconfi-header" style="background: linear-gradient(to right, {$designs[\'accentcolor1\']} 50%, {$designs[\'accentcolor2\']} 50%);{$avtive_design}">{$designswitch_link}</div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_designswitch_active',
		'template' => $db->escape_string('<img src="{$designs[\'headerimage\']}" class="designconfi-headerimg">
      <div class="designconfi-headermode" style="background:{$designs[\'accentcolor1\']};color:{$designs[\'accentcolor2\']};">
         {$mode_option}
      </div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_designswitch_link',
		'template' => $db->escape_string('<a href="usercp.php?action=designconfigurator&designswitch={$designs[\'name\']}">
       <img src="{$designs[\'headerimage\']}" class="designconfi-headerimg">
       <div class="designconfi-headermode" style="background:{$designs[\'accentcolor1\']};color:{$designs[\'accentcolor2\']};">
           {$mode_option}
       </div>
   </a>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_designswitch_none',
		'template' => $db->escape_string('<div style="text-align:center;margin:10px auto;">{$lang->designconfigurator_designswitch_none}</div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_lightdarkmode',
		'template' => $db->escape_string('<div class="designconfi-mode_option" {$lightdarkmode_active_light}>{$lightdarkmode_link_light}</div>
    <div class="designconfi-mode_option" {$lightdarkmode_active_dark}>{$lightdarkmode_link_dark}</div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_lightdarkmode_none',
		'template' => $db->escape_string('<div style="text-align:center;margin:10px auto;">{$lang->designconfigurator_lightdarkmode_none}</div>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_switcher_button_guest',
		'template' => $db->escape_string('<button onclick="dark_mode_{$design_option}()">{$lang->switcher_lightdarkbutton}</button>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_switcher_button_member',
		'template' => $db->escape_string('<form method="post" action="usercp.php?action=designconfigurator">     
		<input type="hidden" name="saveurl" value="{$saveurl}" /> 
		<input type="hidden" value="{$activedimm}" name="indexdimm" class="button">
		<input type="submit" value="{$lang->switcher_lightdarkbutton}" class="button" name="send_indexdimm">
	</form>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_switcher_guest_js_accentcolor',
		'template' => $db->escape_string('<script>
    function getCookie(k){var v=document.cookie.match(\'(^|;) ?\'+k+\'=([^;]*)(;|$)\');return v?v[2]:null}
    function dark_mode_accentcolor() {
        if (getCookie(\'theme_accentcolor\') != "dark_accentcolor") {
            document.cookie="theme_accentcolor=dark_accentcolor";
        } else {
            document.cookie="theme_accentcolor=dark_accentcolor; max-age=0";
        }
        location.reload();
    }
    </script>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_switcher_guest_js_design',
		'template' => $db->escape_string('<script>
    function getCookie(k){var v=document.cookie.match(\'(^|;) ?\'+k+\'=([^;]*)(;|$)\');return v?v[2]:null}
    function dark_mode_design() {
        if (getCookie(\'theme_design\') != "dark_design") {
            document.cookie="theme_design=dark_design";
        } else {
            document.cookie="theme_design=dark_design; max-age=0";
        }
        location.reload();
    }
    </script>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_switcher_guest_js_mode',
		'template' => $db->escape_string('<script>
    function getCookie(k){var v=document.cookie.match(\'(^|;) ?\'+k+\'=([^;]*)(;|$)\');return v?v[2]:null}
    function dark_mode_mode() {
        if (getCookie(\'theme_mode\') != "dark_mode") {
            document.cookie="theme_mode=dark_mode";
        } else {
            document.cookie="theme_mode=dark_mode; max-age=0";
        }
        location.reload();
    }
    </script>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title' => 'designconfigurator_usercp_nav',
		'template' => $db->escape_string('<tr>
	<td class="trow1 smalltext">
		<a href="usercp.php?action=designconfigurator" class="usercp_nav_item usercp_nav_options">{$lang->designconfigurator_usercpmenu_nav}</a>
	</td>
    </tr>'),
		'sid' => '-2',
		'version' => '',
		'dateline' => TIME_NOW
	);
	$db->insert_query("templates", $insert_array);


    // CSS HINZUFÜGEN
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";

    // STYLESHEET HINZUFÜGEN
    $css = array(
		'name' => 'designconfigurator.css',
		'tid' => 1,
		'attachedto' => '',
		"stylesheet" =>	'#designconfigurator {
            width: 100%;
            box-sizing: border-box;
            background: #f5f5f5;
            border: 1px solid;
            border-color: #fff #ddd #ddd #fff;
        }
        
        #designconfigurator .designconfi-headline {
            background: #0066a2 url(../../../images/thead.png) top left repeat-x;
            color: #ffffff;
            border-bottom: 1px solid #263c30;
            padding: 8px;
            -moz-border-radius-topleft: 6px;
            -moz-border-radius-topright: 6px;
            -webkit-border-top-left-radius: 6px;
            -webkit-border-top-right-radius: 6px;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }
        
        #designconfigurator .designconfi-desc {
            text-align: justify;
            line-height: 180%;
            padding: 20px 40px;
        }
        
        #designconfigurator .designconfi-mode {
            display: flex;
            gap: 50px;
            flex-wrap: nowrap;
            justify-content: center;
            margin: 10px 0;
        }
        
        #designconfigurator .designconfi-mode .designconfi-mode_option {
            background: #0f0f0f url(../../../images/tcat.png) repeat-x;
            color: #fff;
            border-top: 1px solid #444;
            border-bottom: 1px solid #000;
            padding: 6px;
            font-size: 12px;
            width: 20%;
            text-align: center;
        }
        
        #designconfigurator .designconfi-design {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px;
        }
        
        #designconfigurator .designconfi-design .designconfi-header {
            padding: 8px;
            width: 31%;
            height: 151px;
        }
        
        #designconfigurator .designconfi-design .designconfi-header .designconfi-headerimg {
            width: 316px;
            height: 150px;
        }
        
        #designconfigurator .designconfi-design .designconfi-header .designconfi-headermode {
            position: relative;
            top: -90px;
            text-align: center;
            padding: 8px;
            font-weight: bold;
        }',
		'cachefile' => $db->escape_string(str_replace('/', '', 'designconfigurator.css')),
		'lastmodified' => time()
	);
    
    $sid = $db->insert_query("themestylesheets", $css);
	$db->update_query("themestylesheets", array("cachefile" => "designconfigurator.css"), "sid = '".$sid."'", 1);

	$tids = $db->simple_select("themes", "tid");
	while($theme = $db->fetch_array($tids)) {
		update_theme_stylesheet_list($theme['tid']);
	}
}

// Funktion zur Überprüfung des Installationsstatus; liefert true zurürck, wenn Plugin installiert, sonst false (optional).
function designconfigurator_is_installed() {
	global $db, $mybb;

	if ($db->field_exists("designname", "users")) {
		return true;
	}
	return false;
}

// Diese Funktion wird aufgerufen, wenn das Plugin deinstalliert wird (optional).
function designconfigurator_uninstall() {
	global $db;

	//DATENBANK LÖSCHEN
	if ($db->table_exists("designs")) {
		$db->drop_table("designs");
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

    // TEMPLATGRUPPE LÖSCHEN
    $db->delete_query("templategroups", "prefix = 'designconfigurator'");

    // TEMPLATES LÖSCHEN
    $db->delete_query("templates", "title LIKE '%designconfigurator%'");

    // CSS LÖSCHEN
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";

    $db->delete_query("themestylesheets", "name = 'designconfigurator.css'");
	$query = $db->simple_select("themes", "tid");
	while($theme = $db->fetch_array($query)) {
		update_theme_stylesheet_list($theme['tid']);
	}
}

// Diese Funktion wird aufgerufen, wenn das Plugin aktiviert wird.
function designconfigurator_activate() {
	global $db, $cache;

	require MYBB_ROOT."/inc/adminfunctions_templates.php";

	// VARIABLEN EINFÜGEN
	find_replace_templatesets('headerinclude', '#'.preg_quote('{$stylesheets}').'#', '{$stylesheets}{$rootsystem}{$switcher_guest_js}');
	find_replace_templatesets('footer', '#'.preg_quote('{$theme_select}').'#', '{$theme_select}{$lightdark_button}');

	// MyALERTS STUFF
	if (class_exists('MybbStuff_MyAlerts_AlertTypeManager')) {
		$alertTypeManager = MybbStuff_MyAlerts_AlertTypeManager::getInstance();

		if (!$alertTypeManager) {
			$alertTypeManager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);
		}

		// Alert bei einer neuen Szene
		$alertType = new MybbStuff_MyAlerts_Entity_AlertType();
		$alertType->setCode('designconfigurator_newDesign'); // The codename for your alert type. Can be any unique string.
		$alertType->setEnabled(true);
		$alertType->setCanBeUserDisabled(true);

		$alertTypeManager->add($alertType);

	}

}

// Diese Funktion wird aufgerufen, wenn das Plugin deaktiviert wird.
function designconfigurator_deactivate() {
	global $db, $cache;

	require MYBB_ROOT."/inc/adminfunctions_templates.php";

	// VARIABLEN ENTFERNEN
	find_replace_templatesets("headerinclude", "#".preg_quote('{$rootsystem}{$switcher_guest_js}')."#i", '', 0);
	find_replace_templatesets("footer", "#".preg_quote('{$lightdark_button}')."#i", '', 0);

	// MyALERTS STUFF
	if (class_exists('MybbStuff_MyAlerts_AlertTypeManager')) {
		$alertTypeManager = MybbStuff_MyAlerts_AlertTypeManager::getInstance();

		if (!$alertTypeManager) {
			$alertTypeManager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);
		}

		$alertTypeManager->deleteByCode('designconfigurator_newDesign');
	}
}

##############################
### FUNKTIONEN - THE MAGIC ###
##############################

// ADMIN-CP
// action handler fürs acp konfigurieren
function designconfigurator_admin_style_action_handler(&$actions) {
	$actions['designconfigurator'] = array('active' => 'designconfigurator', 'file' => 'designconfigurator');
}

// Berechtigungen im ACP - Adminrechte
function designconfigurator_admin_style_permissions(&$admin_permissions) {
	global $lang;
	
    $lang->load('designconfigurator');

	$admin_permissions['designconfigurator'] = $lang->designconfigurator_permission;

	return $admin_permissions;
}

// Menü einfügen
function designconfigurator_admin_style_menu(&$sub_menu) {
	global $mybb, $lang;
	
    $lang->load('designconfigurator');

	$sub_menu[] = [
		"id" => "designconfigurator",
		"title" => $lang->designconfigurator_manage,
		"link" => "index.php?module=style-designconfigurator"
	];
}

// Designs verwalten in ACP
function designconfigurator_manage_designconfigurator() {

	global $mybb, $db, $lang, $page, $run_module, $action_file;

	$lang->load('designconfigurator');

	if ($page->active_action != 'designconfigurator') {
		return false;
	}

	// Add to page navigation
	$page->add_breadcrumb_item($lang->designconfigurator_manage);

	if ($run_module == 'style' && $action_file == 'designconfigurator') {

		#################################
		### HEADER- UND FARBVARIANTEN ###
		#################################

		// Designs Übersicht
		if ($mybb->input['action'] == "" || !isset($mybb->input['action'])) {

			// Optionen im Header bilden
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_overview_designs);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc

			];
			// Design Hinzufüge Button
			$sub_tabs['designconfigurator_design_add'] = [
				"title" => $lang->designconfigurator_manage_add_design,
				"link" => "index.php?module=style-designconfigurator&amp;action=add_design",
				"description" => $lang->designconfigurator_manage_add_design_desc
			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Übersichtsseite
			$form = new Form("index.php?module=style-designconfigurator", "post");

			$form_container = new FormContainer($lang->designconfigurator_manage_overview_designs);
			// Designame
			$form_container->output_row_header($lang->designconfigurator_manage_designname, array('style' => 'text-align: center; width: 15%;'));
			// Style-ID
			$form_container->output_row_header($lang->designconfigurator_manage_theme, array('style' => 'text-align: center; width: 7%;'));
			// Header-Vorschau
			$form_container->output_row_header($lang->designconfigurator_manage_headerimage, array('style' => 'text-align: center;'));
			// Light/Dark Optionen
			$form_container->output_row_header($lang->designconfigurator_manage_modeoption, array('style' => 'text-align: center; width: 10%;'));
			// Benutzer
			$form_container->output_row_header($lang->designconfigurator_manage_user, array('style' => 'text-align: center; width: 7%;'));
			// Gruppen
			$form_container->output_row_header($lang->designconfigurator_manage_groups, array('style' => 'text-align: center; width: 15%;'));
			// Optionen
			$form_container->output_row_header($lang->designconfigurator_manage_options, array('style' => 'text-align: center; width: 5%;'));

			$designs_count = $db->fetch_field($db->query("SELECT COUNT(*) as designs FROM ".TABLE_PREFIX."designs
            WHERE headerimage != ''
            AND accentcolor1 != ''
            AND accentcolor2 != ''
			"), 'designs');
			
        
			$mybb->input['perpage'] = $mybb->get_input('perpage', MyBB::INPUT_INT);
            if ($mybb->input['perpage'] > 0 && $mybb->input['perpage'] <= 50) {
                $perpage = $mybb->input['perpage'];
            } else {
                $perpage = $mybb->input['perpage'] = 10;
            }

			// Page
            $pageview = $mybb->get_input('page', MyBB::INPUT_INT);
            if ($pageview && $pageview > 0) {
                $start = ($pageview - 1) * $perpage;
            } else {
                $start = 0;
                $pageview = 1;
            }
			
            $end = $start + $perpage;
            $lower = $start+1;
            $upper = $end;
            if($upper > $designs_count) {
                $upper = $designs_count;
            }

			// Alle Einträge - nach Style sortieren
			$query_designs = $db->query("SELECT * FROM ".TABLE_PREFIX."designs
            WHERE headerimage != ''
            AND accentcolor1 != ''
            AND accentcolor2 != ''
            ORDER BY tid ASC, name ASC
			LIMIT $start, $perpage
            ");

			while ($designconfigurator_designs = $db->fetch_array($query_designs)) {

				// Default Button
				if ($designconfigurator_designs['standard'] == "1") {
					$default_button = "<div class=\"float_right\"><img src=\"styles/default/images/icons/default.png\" alt=\"Standard-Variante\" style=\"vertical-align: middle;\" title=\"Standard-Variante\"></div>";
				} else {
					$default_button = "<div class=\"float_right\"><a href=\"index.php?module=style-designconfigurator&amp;action=setdefault_design&amp;did={$designconfigurator_designs['did']}\"><img src=\"styles/default/images/icons/make_default.png\" alt=\"Als Standard setzen\" style=\"vertical-align: middle;\" title=\"Als Standard setzen\"></a></div>";
				}

				// Designname & Style-ID
				$form_container->output_cell('<center><strong>'.htmlspecialchars_uni($designconfigurator_designs['name']).'</strong>'.$default_button.'</center>');
				$form_container->output_cell('<center><strong>'.htmlspecialchars_uni($designconfigurator_designs['tid']).'</strong></center>');

				// Header-Vorschau
				$form_container->output_cell('<center><div style="box-sizing: border-box;padding: 5px;background: linear-gradient(to right, '.htmlspecialchars_uni($designconfigurator_designs['accentcolor1']).' 50%, '.htmlspecialchars_uni($designconfigurator_designs['accentcolor2']).' 50%);width:160px;"><img src="'.$mybb->settings['bburl'].'/'.htmlspecialchars_uni($designconfigurator_designs['headerimage']).'" width="150px"></div></center>');

				// Light/Dark Optionen
				if (!empty($designconfigurator_designs['light_root'])) {
					$sonne = "Light";
				} else {
					$sonne = "";
				}
				if (!empty($designconfigurator_designs['dark_root'])) {
					$mond = "Dark";
				} else {
					$mond = "";
				}
				if (!empty($designconfigurator_designs['light_root']) && !empty($designconfigurator_designs['dark_root'])) {

					if ($designconfigurator_designs['root'] == "dark") {
						$mond = "<u>".$mond."</u>";
						$sonne = $sonne;
					} else {
						$mond = $mond;
						$sonne = "<u>".$sonne."</u>";
					}

					$and = " & ";
				} else {
					$and = "";
				}
				$mode = $sonne . $and . $mond;
				$form_container->output_cell('<center><strong>'.$mode.'</strong></center>');

				// Anzahl Nutzer die den Style direkt ausgewählt haben
				$count_userdircet = $db->fetch_field($db->query("SELECT COUNT(*) as count_user FROM ".TABLE_PREFIX."users
                WHERE style = '".$designconfigurator_designs['tid']."'
                AND designname = '".$designconfigurator_designs['name']."'
                "), 'count_user');

				// Anzahl Nutzer die den Standardstyle nutzen
				$count_userdef = $db->fetch_field($db->query("SELECT COUNT(*) as count_user FROM ".TABLE_PREFIX."users
                WHERE style = '".$designconfigurator_designs['tid']."'
                AND designname = ''
                "), 'count_user');

				// Wenn das Team vergessen hat ein Standard Design einzustellen
				$count_default = $db->fetch_field($db->query("SELECT COUNT(*) as default_design FROM ".TABLE_PREFIX."designs
                WHERE tid = '".$designconfigurator_designs['tid']."'
                AND standard = '1'
                "), 'default_design');

				if ($count_default < 1) {
					// Erste Variante => did am kleinsten
					$defstyle = $db->fetch_field($db->simple_select("designs", "did", "tid = '".$designconfigurator_designs['tid']."'", ["order_dir" => "ASC", "order_by" => "did", "limit" => "1"]), "did");
				} else {
					$defstyle = $db->fetch_field($db->simple_select("designs", "did", "tid = '".$designconfigurator_designs['tid']."' AND standard = '1'"), "did");
				}

				if ($designconfigurator_designs['did'] == $defstyle) {
					$count_user = $count_userdircet + $count_userdef;
				} else {
					$count_user = $count_userdircet;
				}

				$form_container->output_cell('<center><strong>'.$count_user.'</strong></center>');

                // Benutzergruppen
				if ($designconfigurator_designs['allowed_usergroups'] != "all") {

					$allowed_usergroups = explode(",", $designconfigurator_designs['allowed_usergroups']);
					$allowedgroups = [];
					foreach($allowed_usergroups as $allowedgroup) {
	
						$groupname = $db->fetch_field($db->simple_select("usergroups", "title", "gid = '".$allowedgroup."'"), "title");
	
						$allowedgroups[] = $groupname;
					}
					$allowedgroups = implode(" &#x26; ", $allowedgroups);
				} else {
					$allowedgroups = $lang->designconfigurator_manage_design_all_usergroups;
				}

                $form_container->output_cell('<center><strong>'.$allowedgroups.'</strong></center>');

				// OPTIONEN
				$popup = new PopupMenu("designconfigurator_{$designconfigurator_designs['did']}", $lang->designconfigurator_manage_options);
				$popup->add_item(
					$lang->designconfigurator_manage_edit,
					"index.php?module=style-designconfigurator&amp;action=edit_design&amp;did={$designconfigurator_designs['did']}"
				);
				$popup->add_item(
					$lang->designconfigurator_manage_delete,
					"index.php?module=style-designconfigurator&amp;action=delete_design&amp;did={$designconfigurator_designs['did']}"
					."&amp;my_post_key={$mybb->post_code}"
				);
				$form_container->output_cell($popup->fetch(), array("class" => "align_center"));
				$form_container->construct_row();
			}

			$form_container->end();
			$form->end();
            // Multipage
            $search_url = htmlspecialchars_uni(
                "index.php?module=style-designconfigurator&{$mybb->input['perpage']}"
            );
            $multipage = multipage($designs_count, $perpage, $pageview, $search_url);
            echo $multipage;
			$page->output_footer();

			exit;
		}

		// DESIGN HINZUFÜGEN
		if ($mybb->input['action'] == "add_design") {

			if ($mybb->request_method == "post") {

				// Check if required fields are not empty
				if (empty($mybb->input['name'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_name;
				}
				if (empty($mybb->input['tid'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_designID;
				}
				if (empty($mybb->input['headerimage'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_headerimage;
				}
				if (empty($mybb->input['accentcolor1'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_accentcolor1;
				}
				if (empty($mybb->input['accentcolor2'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_accentcolor2;
				}
				if (empty($mybb->input['light_root']) AND empty($mybb->input['dark_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_root;
				}
				if (empty($mybb->input['root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_rootdef;
				}
				if (empty($mybb->input['allowedgroups'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_allowedgroups;
				}
				if (empty($mybb->input['alertsend'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_alertsend;
				}

				// No errors - insert
				if (empty($errors)) {

                    $allowedgroups = array();
                    if(is_array($mybb->input['allowedgroups'])){
                        foreach($mybb->input['allowedgroups'] as $gid){
                            if($gid == "all"){
                                $allowedgroups = "all";
                                break;
                            }
                            $gid = (int)$gid;
                            $allowedgroups[$gid] = $gid;
                        }
                    }
                    if(is_array($allowedgroups)){
                        $allowedgroups = implode(",", $allowedgroups);
                    }

					$new_design = array(
						"name" => $db->escape_string($mybb->input['name']),
						"tid" => $db->escape_string($mybb->input['tid']),
						"root" => $db->escape_string($mybb->input['root']),
						"headerimage" => $db->escape_string($mybb->input['headerimage']),
						"accentcolor1" => $db->escape_string($mybb->input['accentcolor1']),
						"accentcolor2" => $db->escape_string($mybb->input['accentcolor2']),
						"light_root" => $db->escape_string($mybb->input['light_root']),
						"dark_root" => $db->escape_string($mybb->input['dark_root']),
						"path" => $db->escape_string($mybb->input['path']),
						"allowed_usergroups" => $allowedgroups
					);

					$db->insert_query("designs", $new_design);

					// MyALERTS STUFF
					if (class_exists('MybbStuff_MyAlerts_AlertTypeManager') && $mybb->input['alertsend'] == "ja") {

						// Themename
						$themename = $db->fetch_field($db->simple_select("themes", "name", "tid = '".$mybb->input['tid']."'"), "name");

						// Admin-Infos
						$adminUid = "1";
						$adminname = $db->fetch_field($db->simple_select("users", "username", "uid = '".$adminUid."'"), "username");

						$user_query = $db->simple_select("users", "uid", "as_uid = '0'");

						$alluids = "";
						while ($user = $db->fetch_array($user_query)) {

							$alluids .= $user['uid'].",";
						}

						// letztes Komma vom UID-String entfernen
						$alluids_string = substr($alluids, 0, -1);

						// UIDs in Array für Foreach
						$alluids_array = explode(",", $alluids_string);

						// Foreach um die einzelnen Partners durchzugehen
						foreach ($alluids_array as $user_id) {

							$alertType = MybbStuff_MyAlerts_AlertTypeManager::getInstance()->getByCode('designconfigurator_newDesign');
							if ($alertType != NULL && $alertType->getEnabled()) {
								$alert = new MybbStuff_MyAlerts_Entity_Alert((int)$user_id, $alertType);
								$alert->setExtraDetails([
									'username' => $adminname,
									'from' => $adminUid,
									'themename' => $themename,
								]);
								MybbStuff_MyAlerts_AlertManager::getInstance()->addAlert($alert);
							}

						}
					}

					$mybb->input['module'] = "designconfigurator";
					$mybb->input['action'] = $lang->designconfigurator_manage_design_added;
					log_admin_action(htmlspecialchars_uni($mybb->input['name']));

					flash_message($lang->designconfigurator_manage_design_added, 'success');
					admin_redirect("index.php?module=style-designconfigurator");
				}
			}

			$page->add_breadcrumb_item($lang->designconfigurator_manage_add_design);

			// Editor scripts
			$page->extra_header .= '
            <link rel="stylesheet" href="../jscripts/sceditor/themes/mybb.css" type="text/css" media="all" />
            <script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1822"></script>
            <script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1827"></script>
            <script type="text/javascript" src="../jscripts/sceditor/plugins/undo.js?ver=1805"></script>
            <link href="./jscripts/codemirror/lib/codemirror.css?ver=1813" rel="stylesheet">
            <link href="./jscripts/codemirror/theme/mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/lib/codemirror.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/xml/xml.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/javascript/javascript.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/css/css.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/htmlmixed/htmlmixed.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/dialog/dialog-mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/addon/dialog/dialog.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/searchcursor.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/search.js?ver=1821"></script>
            <script src="./jscripts/codemirror/addon/fold/foldcode.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/xml-fold.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/foldgutter.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/fold/foldgutter.css?ver=1813" rel="stylesheet">
            ';

			// Build options header
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_add_design);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc

			];
			// Design Hinzufüge Button
			$sub_tabs['designconfigurator_design_add'] = [
				"title" => $lang->designconfigurator_manage_add_design,
				"link" => "index.php?module=style-designconfigurator&amp;action=add_design",
				"description" => $lang->designconfigurator_manage_add_design_desc
			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_design_add');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Stylesheets Dropbox
			$sort = [];
			$themes_query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes t
            WHERE tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage = '' AND light_root != '' AND dark_root != '')
            AND tid != 1
            ORDER BY name ASC
            ");
			while ($themes = $db->fetch_array($themes_query)) {
				$tid = $themes['tid'];
				$sort[$tid] = $themes['name'];
			}

			// Build the form
			$form = new Form("index.php?module=style-designconfigurator&amp;action=add_design", "post", "", 1);
			$form_container = new FormContainer($lang->designconfigurator_manage_add_design);

			// Designname
			$form_container->output_row(
				$lang->designconfigurator_manage_design_name_title."<em>*</em>",
				$lang->designconfigurator_manage_design_name_desc,
				$form->generate_text_box('name', $mybb->get_input('name'))
			);

			// Themen ID
			$form_container->output_row(
				$lang->designconfigurator_manage_design_designID_title."<em>*</em>",
				$lang->designconfigurator_manage_design_designID_desc,
				$form->generate_select_box('tid', $sort, $mybb->get_input('tid'), array('id' => 'tid')),
				'tid'
			);

			// Headerbild
			$form_container->output_row(
				$lang->designconfigurator_manage_design_headerimage_title."<em>*</em>",
				$lang->designconfigurator_manage_design_headerimage_desc,
				$form->generate_text_box('headerimage', $mybb->get_input('headerimage'))
			);

			// Aktzentfarbe1
			$form_container->output_row(
				$lang->designconfigurator_manage_design_accentcolor1_title."<em>*</em>",
				$lang->designconfigurator_manage_design_accentcolor1_desc,
				$form->generate_text_box('accentcolor1', $mybb->get_input('accentcolor1'))
			);

			// Aktzentfarbe2
			$form_container->output_row(
				$lang->designconfigurator_manage_design_accentcolor2_title."<em>*</em>",
				$lang->designconfigurator_manage_design_accentcolor2_desc,
				$form->generate_text_box('accentcolor2', $mybb->get_input('accentcolor2'))
			);

			// Pfad Bilder
			$form_container->output_row(
				$lang->designconfigurator_manage_design_path_title."<em>*</em>",
				$lang->designconfigurator_manage_design_path_desc,
				$form->generate_text_box('path', $mybb->get_input('path'))
			);

			// Standard Root
			$rootsystem = array(
				"" => $lang->designconfigurator_manage_design_root_select_def,
				"light" => $lang->designconfigurator_manage_design_root_select_light,
				"dark" => $lang->designconfigurator_manage_design_root_select_dark,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_root_title."<em>*</em>",
				$lang->designconfigurator_manage_design_root_desc,
				$form->generate_select_box('root', $rootsystem, $mybb->get_input('root'), array('id' => 'root')),
				'root'
			);

			// Zugelassene Benutzergruppen
			$options = array();
            $query = $db->simple_select("usergroups", "gid, title", "gid != '1'", array('order_by' => 'title'));
            $options['all'] = $lang->designconfigurator_manage_design_all_usergroups;
            while($usergroup = $db->fetch_array($query)){
                $options[(int)$usergroup['gid']] = $usergroup['title'];
            }
            $form_container->output_row(
                $lang->designconfigurator_manage_design_allowed_usergroups_title."<em>*</em>",
                $lang->designconfigurator_manage_design_allowed_usergroups_desc, 
                $form->generate_select_box('allowedgroups[]', $options, '', array('id' => 'allowedgroups', 'multiple' => true, 'size' => 5)), 
                'allowedgroups'
            );

			// Light 
			$light_root_editor = $form->generate_text_area('light_root', $mybb->get_input('light_root'), array(
				'id' => 'light_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_lightmode_title,
				$lang->designconfigurator_manage_design_lightmode_desc,
				$light_root_editor,
				'light_root'
			);

			// Dark
			$dark_root_editor = $form->generate_text_area('dark_root', $mybb->get_input('dark_root'), array(
				'id' => 'dark_root',
				'style' => 'width: 99%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_darkmode_title,
				$lang->designconfigurator_manage_design_darkmode_desc,
				$dark_root_editor,
				'dark_root'
			);

			// Alerts schicken
			$alertsend = array(
				"" => $lang->designconfigurator_manage_design_alertsend_def,
				"ja" => $lang->designconfigurator_manage_design_alertsend_yes,
				"nein" => $lang->designconfigurator_manage_design_alertsend_no,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_alertsend_title."<em>*</em>",
				$lang->designconfigurator_manage_design_alertsend_desc,
				$form->generate_select_box('alertsend', $alertsend, $mybb->get_input('alertsend'), array('id' => 'alertsend')),
				'alertsend'
			);

			$form_container->end();
			$buttons[] = $form->generate_submit_button($lang->designconfigurator_manage_submit_add);
			$form->output_submit_wrapper($buttons);

			$form->end();

			$admin_options['codepress'] = 1;

			if ($admin_options['codepress'] != 0) {
				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("dark_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';

				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("light_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';
			}

			$page->output_footer();

			exit;
		}

		// DESIGN BEARBEITEN
		if ($mybb->input['action'] == "edit_design") {

			if ($mybb->request_method == "post") {

				// Check if required fields are not empty
				if (empty($mybb->input['name'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_name;
				}
				if (empty($mybb->input['tid'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_designID;
				}
				if (empty($mybb->input['headerimage'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_headerimage;
				}
				if (empty($mybb->input['accentcolor1'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_accentcolor1;
				}
				if (empty($mybb->input['accentcolor2'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_accentcolor2;
				}
				if (empty($mybb->input['light_root']) AND empty($mybb->input['dark_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_root;
				}
				if (empty($mybb->input['root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_rootdef;
				}
				if (empty($mybb->input['allowedgroups'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_allowedgroups;
				}
				if (empty($mybb->input['alertsend'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_alertsend;
				}

				// No errors - insert the terms of use
				if (empty($errors)) {

					$did = $mybb->get_input('did', MyBB::INPUT_INT);

                    $allowedgroups = array();
                    if(is_array($mybb->input['allowedgroups'])){
                        foreach($mybb->input['allowedgroups'] as $gid){
                            if($gid == "all"){
                                $allowedgroups = "all";
                                break;
                            }
                            $gid = (int)$gid;
                            $allowedgroups[$gid] = $gid;
                        }
                    }
                    if(is_array($allowedgroups)){
                        $allowedgroups = implode(",", $allowedgroups);
                    }

					$edited_design = [
						"tid" => $db->escape_string($mybb->input['tid']),
						"name" => $db->escape_string($mybb->input['name']),
						"headerimage" => $db->escape_string($mybb->input['headerimage']),
						"accentcolor1" => $db->escape_string($mybb->input['accentcolor1']),
						"accentcolor2" => $db->escape_string($mybb->input['accentcolor2']),
						"light_root" => $db->escape_string($mybb->input['light_root']),
						"dark_root" => $db->escape_string($mybb->input['dark_root']),
						"path" => $db->escape_string($mybb->input['path']),
						"allowed_usergroups" => $allowedgroups
					];

					$db->update_query("designs", $edited_design, "did='{$did}'");

					// MyALERTS STUFF
					if (class_exists('MybbStuff_MyAlerts_AlertTypeManager') && $mybb->input['alertsend'] == "ja") {

						// Themename
						$themename = $db->fetch_field($db->simple_select("themes", "name", "tid = '".$mybb->input['tid']."'"), "name");

						// Admin-Infos
						$adminUid = "1";
						$adminname = $db->fetch_field($db->simple_select("users", "username", "uid = '".$adminUid."'"), "username");

						$user_query = $db->simple_select("users", "uid", "as_uid = '0'");

						$alluids = "";
						while ($user = $db->fetch_array($user_query)) {

							$alluids .= $user['uid'].",";
						}

						// letztes Komma vom UID-String entfernen
						$alluids_string = substr($alluids, 0, -1);

						// UIDs in Array für Foreach
						$alluids_array = explode(",", $alluids_string);

						// Foreach um die einzelnen Partners durchzugehen
						foreach ($alluids_array as $user_id) {

							$alertType = MybbStuff_MyAlerts_AlertTypeManager::getInstance()->getByCode('designconfigurator_newDesign');
							if ($alertType != NULL && $alertType->getEnabled()) {
								$alert = new MybbStuff_MyAlerts_Entity_Alert((int)$user_id, $alertType);
								$alert->setExtraDetails([
									'username' => $adminname,
									'from' => $adminUid,
									'themename' => $themename,
								]);
								MybbStuff_MyAlerts_AlertManager::getInstance()->addAlert($alert);
							}

						}
					}

					$mybb->input['module'] = "designconfigurator";
					$mybb->input['action'] = $lang->designconfigurator_manage_design_edited;
					log_admin_action(htmlspecialchars_uni($mybb->input['name']));

					flash_message($lang->designconfigurator_manage_design_edited, 'success');
					admin_redirect("index.php?module=style-designconfigurator");
				}

			}

			$page->add_breadcrumb_item($lang->designconfigurator_manage_edit_design);

			// Editor scripts
			$page->extra_header .= '
            <link rel="stylesheet" href="../jscripts/sceditor/themes/mybb.css" type="text/css" media="all" />
            <script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1822"></script>
            <script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1827"></script>
            <script type="text/javascript" src="../jscripts/sceditor/plugins/undo.js?ver=1805"></script>
            <link href="./jscripts/codemirror/lib/codemirror.css?ver=1813" rel="stylesheet">
            <link href="./jscripts/codemirror/theme/mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/lib/codemirror.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/xml/xml.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/javascript/javascript.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/css/css.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/htmlmixed/htmlmixed.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/dialog/dialog-mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/addon/dialog/dialog.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/searchcursor.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/search.js?ver=1821"></script>
            <script src="./jscripts/codemirror/addon/fold/foldcode.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/xml-fold.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/foldgutter.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/fold/foldgutter.css?ver=1813" rel="stylesheet">
            ';

			// Build options header
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_edit_design);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc

			];
			// Design Bearbeiten Button
			$sub_tabs['designconfigurator_design_edit'] = [
				"title" => $lang->designconfigurator_manage_edit_design,
				"link" => "index.php?module=style-designconfigurator&amp;action=edit_design",
				"description" => $lang->designconfigurator_manage_edit_design_desc
			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_design_edit');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Stylesheets Dropbox
			$theme = [];
			$sorted = [];
			$themes_query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes t
            WHERE tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage = '' AND light_root != '' AND dark_root != '')
            AND tid != 1
            ORDER BY name ASC
            ");
			while ($themes = $db->fetch_array($themes_query)) {
				$tid = $themes['tid'];
				$theme[$tid] = $themes['name'];
				$sorted[$tid] = $themes['name'];
			}

			// Get the data
			$did = $mybb->get_input('did', MyBB::INPUT_INT);
			$edit_query = $db->simple_select("designs", "*", "did={$did}");
			$edit_design = $db->fetch_array($edit_query);

			// Build the form
			$form = new Form("index.php?module=style-designconfigurator&amp;action=edit_design", "post", "", 1);
			echo $form->generate_hidden_field('did', $did);

			$form_container = new FormContainer($lang->designconfigurator_manage_edit_design);

			// Designname
			$form_container->output_row(
				$lang->designconfigurator_manage_design_name_title."<em>*</em>",
				$lang->designconfigurator_manage_design_name_desc,
				$form->generate_text_box('name', htmlspecialchars_uni($edit_design['name']))
			);

			// Themen ID
			$form_container->output_row(
				$lang->designconfigurator_manage_design_designID_title."<em>*</em>",
				$lang->designconfigurator_manage_design_designID_desc,
				$form->generate_select_box('tid', $sorted, $edit_design['tid'], array('id' => 'tid')),
				'tid'
			);

			// Headerbild
			$form_container->output_row(
				$lang->designconfigurator_manage_design_headerimage_title."<em>*</em>",
				$lang->designconfigurator_manage_design_headerimage_desc,
				$form->generate_text_box('headerimage', htmlspecialchars_uni($edit_design['headerimage']))
			);

			// erste Aktzentfarbe
			$form_container->output_row(
				$lang->designconfigurator_manage_design_accentcolor1_title."<em>*</em>",
				$lang->designconfigurator_manage_design_accentcolor1_desc,
				$form->generate_text_box('accentcolor1', htmlspecialchars_uni($edit_design['accentcolor1']))
			);

			// zweite Aktzentfarbe
			$form_container->output_row(
				$lang->designconfigurator_manage_design_accentcolor2_title."<em>*</em>",
				$lang->designconfigurator_manage_design_accentcolor2_desc,
				$form->generate_text_box('accentcolor2', htmlspecialchars_uni($edit_design['accentcolor2']))
			);

			// Pfad Bilder
			$form_container->output_row(
				$lang->designconfigurator_manage_design_path_title."<em>*</em>",
				$lang->designconfigurator_manage_design_path_desc,
				$form->generate_text_box('path', htmlspecialchars_uni($edit_design['path']))
			);

			// Standard Root
			$rootsystem = array(
				"" => $lang->designconfigurator_manage_design_root_select_def,
				"light" => $lang->designconfigurator_manage_design_root_select_light,
				"dark" => $lang->designconfigurator_manage_design_root_select_dark,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_root_title."<em>*</em>",
				$lang->designconfigurator_manage_design_root_desc,
				$form->generate_select_box('root', $rootsystem, $edit_design['root'], array('id' => 'root')),
				'root'
			);

			// Zugelassene Benutzergruppen
            $options = array();
            $query = $db->simple_select("usergroups", "gid, title", "gid != '1'", array('order_by' => 'title'));
            $options['all'] = $lang->designconfigurator_manage_design_all_usergroups;
            while($usergroup = $db->fetch_array($query)){
                $options[(int)$usergroup['gid']] = $usergroup['title'];
            }
            $form_container->output_row(
                $lang->designconfigurator_manage_design_allowed_usergroups_title."<em>*</em>",
                $lang->designconfigurator_manage_design_allowed_usergroups_desc, 
                $form->generate_select_box('allowedgroups[]', $options, explode(",", $edit_design['allowed_usergroups']), array('id' => 'allowedgroups', 'multiple' => true, 'size' => 5)), 
                'allowedgroups'
            );

			$light_root_editor = $form->generate_text_area('light_root', $edit_design['light_root'], array(
				'id' => 'light_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_lightmode_title,
				$lang->designconfigurator_manage_design_lightmode_desc,
				$light_root_editor,
				'light_root'
			);

			$dark_root_editor = $form->generate_text_area('dark_root', $edit_design['dark_root'], array(
				'id' => 'dark_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_darkmode_title,
				$lang->designconfigurator_manage_design_darkmode_desc,
				$dark_root_editor,
				'dark_root'
			);

			// Alerts schicken
			$alertsend = array(
				"" => $lang->designconfigurator_manage_design_alertsend_def,
				"ja" => $lang->designconfigurator_manage_design_alertsend_yes,
				"nein" => $lang->designconfigurator_manage_design_alertsend_no,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_alertsend_title."<em>*</em>",
				$lang->designconfigurator_manage_design_alertsend_desc,
				$form->generate_select_box('alertsend', $alertsend, $mybb->input['alertsend'], array('id' => 'alertsend')),
				'alertsend'
			);

			$form_container->end();
			$buttons[] = $form->generate_submit_button($lang->designconfigurator_manage_submit_edit);
			$form->output_submit_wrapper($buttons);
			$form->end();

			$admin_options['codepress'] = 1;

			if ($admin_options['codepress'] != 0) {
				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("dark_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';

				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("light_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';
			}

			$page->output_footer();

			exit;
		}

		// Delete Design
		if ($mybb->input['action'] == "delete_design") {

			// Get data
			$did = $mybb->get_input('did', MyBB::INPUT_INT);
			$query = $db->simple_select("designs", "*", "did={$did}");
			$del_design = $db->fetch_array($query);

			// Error Handling
			if (empty($did)) {
				flash_message($lang->designconfigurator_manage_error_invalid, 'error');
				admin_redirect("index.php?module=style-designconfigurator");
			}

			// Cancel button pressed?
			if (isset($mybb->input['no']) && $mybb->input['no']) {
				admin_redirect("index.php?module=style-designconfigurator");
			}

			if (!verify_post_check($mybb->input['my_post_key'])) {
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=style-designconfigurator");
			} // all fine
			else {
				if ($mybb->request_method == "post") {

					$db->delete_query("designs", "did='{$did}'");

					$mybb->input['module'] = "designconfigurator";
					$mybb->input['action'] = $lang->designconfigurator_manage_design_deleted;
					log_admin_action(htmlspecialchars_uni($del_design['name']));

					flash_message($lang->designconfigurator_manage_design_deleted, 'success');
					admin_redirect("index.php?module=style-designconfigurator");
				} else {
					$page->output_confirm_action(
						"index.php?module=style-designconfigurator&amp;action=delete_design&amp;did={$did}",
						$lang->designconfigurator_manage_delete_page
					);
				}
			}
			exit;
		}

		// Standard setzen
		if ($mybb->input['action'] == "setdefault_design") {

			// Get data
			$query = $db->simple_select("designs", "*", "did='".$mybb->get_input('did', MyBB::INPUT_INT)."'");
			$def_design = $db->fetch_array($query);

			// Alle auf Null setzen
			$db->query("UPDATE ".TABLE_PREFIX."designs d
            SET d.standard = '0'
            WHERE d.tid = '".$def_design['tid']."'");

			// Neuen Standard auf 1 setzen
			$db->query("UPDATE ".TABLE_PREFIX."designs d
            SET d.standard = '1'
            WHERE d.did = '".$mybb->get_input('did', MyBB::INPUT_INT)."'");

			$mybb->input['module'] = "designconfigurator";
			$mybb->input['action'] = $lang->designconfigurator_manage_design_setdefault;
			log_admin_action(htmlspecialchars_uni($def_design['name']));

			flash_message($lang->designconfigurator_manage_design_setdefault, 'success');
			admin_redirect("index.php?module=style-designconfigurator");
			exit;
		}

		###########################
		### LIGHT- UND DARKMODE ###
		###########################

		// Light-/Dark Übersicht
		if ($mybb->input['action'] == "mode") {

			// Optionen im Header bilden
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_overview_mode);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc
			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// Light/Dark hinzufügen Button
			$sub_tabs['designconfigurator_add_mode'] = [
				"title" => $lang->designconfigurator_manage_add_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=add_mode",
				"description" => $lang->designconfigurator_manage_add_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_mode');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Übersichtsseite
			$form = new Form("index.php?module=style-designconfigurator&amp;action=mode", "post");

			$form_container = new FormContainer($lang->designconfigurator_manage_overview_mode);
			// Style-Name
			$form_container->output_row_header($lang->designconfigurator_manage_designname, array('style' => 'text-align: center;'));
			// Style-ID
			$form_container->output_row_header($lang->designconfigurator_manage_theme, array('style' => 'text-align: center;'));
			// Default Mode
			$form_container->output_row_header($lang->designconfigurator_manage_defaultmode, array('style' => 'text-align: center;'));
			// Optionen
			$form_container->output_row_header($lang->designconfigurator_manage_options, array('style' => 'text-align: center; width: 20%;'));

			// Alle Einträge - nach Style sortieren
			$query_mode = $db->simple_select("designs", "*", "headerimage = '' AND accentcolor1 = '' AND accentcolor2 = '' AND individual_colors = ''", ["order_by" => 'tid', 'order_dir' => 'ASC']);

			while ($designconfigurator_mode = $db->fetch_array($query_mode)) {

				// Stylename & -ID
				$form_container->output_cell('<center><strong>'.htmlspecialchars_uni($designconfigurator_mode['name']).'</strong></center>');
				$form_container->output_cell('<center><strong>'.htmlspecialchars_uni($designconfigurator_mode['tid']).'</strong></center>');
				$form_container->output_cell('<center><strong>'.htmlspecialchars_uni($designconfigurator_mode['root']).'</strong></center>');

				// OPTIONEN
				$popup = new PopupMenu("designconfigurator_{$designconfigurator_mode['did']}", $lang->designconfigurator_manage_options);
				$popup->add_item(
					$lang->designconfigurator_manage_edit,
					"index.php?module=style-designconfigurator&amp;action=edit_mode&amp;did={$designconfigurator_mode['did']}"
				);
				$popup->add_item(
					$lang->designconfigurator_manage_delete,
					"index.php?module=style-designconfigurator&amp;action=delete_mode&amp;did={$designconfigurator_mode['did']}"
					."&amp;my_post_key={$mybb->post_code}"
				);
				$form_container->output_cell($popup->fetch(), array("class" => "align_center"));
				$form_container->construct_row();
			}

			$form_container->end();
			$form->end();
			$page->output_footer();

			exit;
		}

		// LIGHT- UND DARKMODE HINZUFÜGEN
		if ($mybb->input['action'] == "add_mode") {

			if ($mybb->request_method == "post") {

				// Check if required fields are not empty
				if (empty($mybb->input['tid'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_designID;
				}
				if (empty($mybb->input['light_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_light_root;
				}
				if (empty($mybb->input['dark_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_dark_root;
				}
				if (empty($mybb->input['root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_rootdef;
				}

				// No errors - insert
				if (empty($errors)) {

					$designname = $db->fetch_field($db->simple_select("themes", "name", "tid = '".$mybb->input['tid']."'"), "name");

					$new_mode = array(
						"name" => $db->escape_string($designname),
						"tid" => $db->escape_string($mybb->input['tid']),
						"root" => $db->escape_string($mybb->input['root']),
						"light_root" => $db->escape_string($mybb->input['light_root']),
						"dark_root" => $db->escape_string($mybb->input['dark_root'])
					);

					$db->insert_query("designs", $new_mode);

					$mybb->input['module'] = "designconfigurator&amp;action=mode";
					$mybb->input['action'] = $lang->designconfigurator_manage_mode_added;
					log_admin_action(htmlspecialchars_uni($designname));

					flash_message($lang->designconfigurator_manage_mode_added, 'success');
					admin_redirect("index.php?module=style-designconfigurator&amp;action=mode");
				}
			}

			$page->add_breadcrumb_item($lang->designconfigurator_manage_add_mode);

			// Editor scripts
			$page->extra_header .= '
            <link rel="stylesheet" href="../jscripts/sceditor/themes/mybb.css" type="text/css" media="all" />
            <script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1822"></script>
            <script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1827"></script>
            <script type="text/javascript" src="../jscripts/sceditor/plugins/undo.js?ver=1805"></script>
            <link href="./jscripts/codemirror/lib/codemirror.css?ver=1813" rel="stylesheet">
            <link href="./jscripts/codemirror/theme/mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/lib/codemirror.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/xml/xml.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/javascript/javascript.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/css/css.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/htmlmixed/htmlmixed.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/dialog/dialog-mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/addon/dialog/dialog.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/searchcursor.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/search.js?ver=1821"></script>
            <script src="./jscripts/codemirror/addon/fold/foldcode.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/xml-fold.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/foldgutter.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/fold/foldgutter.css?ver=1813" rel="stylesheet">
            ';

			// Build options header
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_add_mode);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc

			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// Light/Dark hinzufügen Button
			$sub_tabs['designconfigurator_add_mode'] = [
				"title" => $lang->designconfigurator_manage_add_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=add_mode",
				"description" => $lang->designconfigurator_manage_add_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_add_mode');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Stylesheets Dropbox
			$theme = [];
			$sort = [];
			$themes_query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes t
            WHERE tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage != '')
            AND tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage = '' AND light_root != '' AND dark_root != '')
            AND tid != 1
            ORDER BY name ASC
            ");
			while ($themes = $db->fetch_array($themes_query)) {
				$tid = $themes['tid'];
				$theme[$tid] = $themes['name'];
				$sort[$tid] = $themes['name'];
			}

			// Build the form
			$form = new Form("index.php?module=style-designconfigurator&amp;action=add_mode", "post", "", 1);
			$form_container = new FormContainer($lang->designconfigurator_manage_add_mode);

			// Themen ID
			$form_container->output_row(
				$lang->designconfigurator_manage_design_designID_title."<em>*</em>",
				$lang->designconfigurator_manage_mode_designID_desc,
				$form->generate_select_box('tid', $sort, '', array('id' => 'tid')),
				'tid'
			);

			// Standard Root
			$rootsystem = array(
				"" => $lang->designconfigurator_manage_design_root_select_def,
				"light" => $lang->designconfigurator_manage_design_root_select_light,
				"dark" => $lang->designconfigurator_manage_design_root_select_dark,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_root_title."<em>*</em>",
				$lang->designconfigurator_manage_design_root_desc,
				$form->generate_select_box('root', $rootsystem, '', array('id' => 'root')),
				'root'
			);

			$light_root_editor = $form->generate_text_area('light_root', $mybb->get_input('light_root'), array(
				'id' => 'light_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_lightmode_title. "<em>*</em>",
				$lang->designconfigurator_manage_design_lightmode_desc,
				$light_root_editor,
				'light_root'
			);

			$dark_root_editor = $form->generate_text_area('dark_root', $mybb->get_input('dark_root'), array(
				'id' => 'dark_root',
				'style' => 'width: 99%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_darkmode_title. "<em>*</em>",
				$lang->designconfigurator_manage_design_darkmode_desc,
				$dark_root_editor,
				'dark_root'
			);

			$form_container->end();
			$buttons[] = $form->generate_submit_button($lang->designconfigurator_manage_submit_add_mode);
			$form->output_submit_wrapper($buttons);

			$form->end();

			$admin_options['codepress'] = 1;

			if ($admin_options['codepress'] != 0) {
				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("dark_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';

				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("light_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';
			}

			$page->output_footer();

			exit;
		}

		// LIGHT- UND DARKMODE BEARBEITEN
		if ($mybb->input['action'] == "edit_mode") {

			if ($mybb->request_method == "post") {

				// Check if required fields are not empty
				if (empty($mybb->input['tid'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_designID;
				}
				if (empty($mybb->input['light_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_light_root;
				}
				if (empty($mybb->input['dark_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_dark_root;
				}
				if (empty($mybb->input['root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_rootdef;
				}

				// No errors - insert the terms of use
				if (empty($errors)) {

					$did = $mybb->get_input('did', MyBB::INPUT_INT);
					$designname = $db->fetch_field($db->simple_select("themes", "name", "tid = '".$mybb->input['tid']."'"), "name");

					$edited_mode = [
						"tid" => $db->escape_string($mybb->input['tid']),
						"name" => $db->escape_string($designname),
						"root" => $db->escape_string($mybb->input['root']),
						"light_root" => $db->escape_string($mybb->input['light_root']),
						"dark_root" => $db->escape_string($mybb->input['dark_root'])
					];

					$db->update_query("designs", $edited_mode, "did='{$did}'");

					$mybb->input['module'] = "designconfigurator";
					$mybb->input['action'] = $lang->designconfigurator_manage_mode_edited;
					log_admin_action(htmlspecialchars_uni($designname));

					flash_message($lang->designconfigurator_manage_mode_edited, 'success');
					admin_redirect("index.php?module=style-designconfigurator&amp;action=mode");
				}

			}

			$page->add_breadcrumb_item($lang->designconfigurator_manage_edit_mode);

			// Editor scripts
			$page->extra_header .= '
            <link rel="stylesheet" href="../jscripts/sceditor/themes/mybb.css" type="text/css" media="all" />
            <script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1822"></script>
            <script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1827"></script>
            <script type="text/javascript" src="../jscripts/sceditor/plugins/undo.js?ver=1805"></script>
            <link href="./jscripts/codemirror/lib/codemirror.css?ver=1813" rel="stylesheet">
            <link href="./jscripts/codemirror/theme/mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/lib/codemirror.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/xml/xml.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/javascript/javascript.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/css/css.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/htmlmixed/htmlmixed.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/dialog/dialog-mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/addon/dialog/dialog.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/searchcursor.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/search.js?ver=1821"></script>
            <script src="./jscripts/codemirror/addon/fold/foldcode.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/xml-fold.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/foldgutter.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/fold/foldgutter.css?ver=1813" rel="stylesheet">
            ';

			// Build options header
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_edit_design);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc

			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// Light/Dark Bearbeiten Button
			$sub_tabs['designconfigurator_mode_edit'] = [
				"title" => $lang->designconfigurator_manage_edit_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=edit_mode",
				"description" => $lang->designconfigurator_manage_edit_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_mode_edit');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Get the data
			$did = $mybb->get_input('did', MyBB::INPUT_INT);

			// Stylesheets Dropbox
			$theme = [];
			$sorted = [];
			$themes_query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes t
            WHERE tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage != '')
            AND tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage = '' AND light_root != '' AND dark_root != '' AND tid = '".$did."')
            AND tid != 1
            ORDER BY name ASC
            ");
			while ($themes = $db->fetch_array($themes_query)) {
				$tid = $themes['tid'];
				$theme[$tid] = $themes['name'];
				$sorted[$tid] = $themes['name'];
			}

			$edit_query = $db->simple_select("designs", "*", "did={$did}");
			$edit_mode = $db->fetch_array($edit_query);

			// Build the form
			$form = new Form("index.php?module=style-designconfigurator&amp;action=edit_mode", "post", "", 1);
			echo $form->generate_hidden_field('did', $did);

			$form_container = new FormContainer($lang->designconfigurator_manage_edit_mode);

			// Themen ID
			$form_container->output_row(
				$lang->designconfigurator_manage_design_designID_title."<em>*</em>",
				$lang->designconfigurator_manage_mode_designID_desc,
				$form->generate_select_box('tid', $sorted, $edit_mode['tid'], array('id' => 'tid')),
				'tid'
			);

			// Standard Root
			$rootsystem = array(
				"" => $lang->designconfigurator_manage_design_root_select_def,
				"light" => $lang->designconfigurator_manage_design_root_select_light,
				"dark" => $lang->designconfigurator_manage_design_root_select_dark,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_root_title."<em>*</em>",
				$lang->designconfigurator_manage_design_root_desc,
				$form->generate_select_box('root', $rootsystem, $edit_mode['root'], array('id' => 'root')),
				'root'
			);

			$light_root_editor = $form->generate_text_area('light_root', $edit_mode['light_root'], array(
				'id' => 'light_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_lightmode_title. "<em>*</em>",
				$lang->designconfigurator_manage_design_lightmode_desc,
				$light_root_editor,
				'light_root'
			);

			$dark_root_editor = $form->generate_text_area('dark_root', $edit_mode['dark_root'], array(
				'id' => 'dark_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_darkmode_title. "<em>*</em>",
				$lang->designconfigurator_manage_design_darkmode_desc,
				$dark_root_editor,
				'dark_root'
			);

			$form_container->end();
			$buttons[] = $form->generate_submit_button($lang->designconfigurator_manage_submit_edit_mode);
			$form->output_submit_wrapper($buttons);
			$form->end();

			$admin_options['codepress'] = 1;

			if ($admin_options['codepress'] != 0) {
				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("dark_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';

				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("light_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';
			}

			$page->output_footer();

			exit;
		}

		// Delete Light- & Dark
		if ($mybb->input['action'] == "delete_mode") {

			// Get data
			$did = $mybb->get_input('did', MyBB::INPUT_INT);
			$query = $db->simple_select("designs", "*", "did={$did}");
			$del_mode = $db->fetch_array($query);

			// Error Handling
			if (empty($did)) {
				flash_message($lang->designconfigurator_manage_error_invalid, 'error');
				admin_redirect("index.php?module=style-designconfigurator&amp;action=mode");
			}

			// Cancel button pressed?
			if (isset($mybb->input['no']) && $mybb->input['no']) {
				admin_redirect("index.php?module=style-designconfigurator&amp;action=mode");
			}

			if (!verify_post_check($mybb->input['my_post_key'])) {
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=style-designconfigurator&amp;action=mode");
			} // all fine
			else {
				if ($mybb->request_method == "post") {

					$db->delete_query("designs", "did='{$did}'");

					$mybb->input['module'] = "designconfigurator&amp;action=mode";
					$mybb->input['action'] = $lang->designconfigurator_manage_mode_deleted;
					log_admin_action(htmlspecialchars_uni($del_mode['name']));

					flash_message($lang->designconfigurator_manage_mode_deleted, 'success');
					admin_redirect("index.php?module=style-designconfigurator&amp;action=mode");
				} else {
					$page->output_confirm_action(
						"index.php?module=style-designconfigurator&amp;action=delete_mode&amp;did={$did}",
						$lang->designconfigurator_manage_delete_page
					);
				}
			}
			exit;
		}

		########################################
		### INDIVIDUELLE DESIGN-AKZENTFARBEN ###
		########################################

		// Akzentfarbe Übersicht
		if ($mybb->input['action'] == "accentcolor") {

			// Optionen im Header bilden
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_overview_accentcolor);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc
			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];
			// individuelle Designfarbe hinzufügen Button
			$sub_tabs['designconfigurator_add_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_add_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=add_accentcolor",
				"description" => $lang->designconfigurator_manage_add_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_accentcolor');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Übersichtsseite
			$form = new Form("index.php?module=style-designconfigurator&amp;action=accentcolor", "post");

			$form_container = new FormContainer($lang->designconfigurator_manage_overview_accentcolor);
			// Designame
			$form_container->output_row_header($lang->designconfigurator_manage_designname, array('style' => 'text-align: center; width: 15%;'));
			// Style-ID
			$form_container->output_row_header($lang->designconfigurator_manage_theme, array('style' => 'text-align: center; width: 7%;'));
			// Farben-Vorschau
			$form_container->output_row_header($lang->designconfigurator_manage_individualcolors, array('style' => 'text-align: center;'));
			// Light/Dark Optionen
			$form_container->output_row_header($lang->designconfigurator_manage_modeoption, array('style' => 'text-align: center; width: 10%;'));
			// Optionen
			$form_container->output_row_header($lang->designconfigurator_manage_options, array('style' => 'text-align: center; width: 20%;'));

			// Alle Einträge - nach Style sortieren
			$query_color = $db->simple_select("designs", "*", "individual_colors != ''", ["order_by" => 'tid', 'order_dir' => 'ASC']);

			while ($designconfigurator_color = $db->fetch_array($query_color)) {

				// Stylename & -ID
				$form_container->output_cell('<center><strong>'.htmlspecialchars_uni($designconfigurator_color['name']).'</strong></center>');
				$form_container->output_cell('<center><strong>'.htmlspecialchars_uni($designconfigurator_color['tid']).'</strong></center>');

				// Farben-Vorschau
				$individual_colors = explode (", ", $designconfigurator_color['individual_colors']);
				$accentcolors = "";
				foreach ($individual_colors as $individual_color) {
					$accentcolors .= '<div style="display: flex;justify-content: center;align-items: center;"><div style="height: 11px;width: 10px;background:'.$individual_color.';margin-right: 5px;"></div><div>'.$individual_color.'</div></div>';
				}
				$form_container->output_cell('<center><strong>'.$accentcolors.'</strong></center>');

				// Light/Dark Optionen
				if (!empty($designconfigurator_color['light_root'])) {
					$sonne = "Light";
				} else {
					$sonne = "";
				}
				if (!empty($designconfigurator_color['dark_root'])) {
					$mond = "Dark";
				} else {
					$mond = "";
				}
				if (!empty($designconfigurator_color['light_root']) && !empty($designconfigurator_color['dark_root'])) {

					if ($designconfigurator_color['root'] == "dark") {
						$mond = "<u>".$mond."</u>";
						$sonne = $sonne;
					} else {
						$mond = $mond;
						$sonne = "<u>".$sonne."</u>";
					}

					$and = " & ";
				} else {
					$and = "";
				}
				$mode = $sonne . $and . $mond;
				$form_container->output_cell('<center><strong>'.$mode.'</strong></center>');

				// OPTIONEN
				$popup = new PopupMenu("designconfigurator_{$designconfigurator_color['did']}", $lang->designconfigurator_manage_options);
				$popup->add_item(
					$lang->designconfigurator_manage_edit,
					"index.php?module=style-designconfigurator&amp;action=edit_accentcolor&amp;did={$designconfigurator_color['did']}"
				);
				$popup->add_item(
					$lang->designconfigurator_manage_delete,
					"index.php?module=style-designconfigurator&amp;action=delete_accentcolor&amp;did={$designconfigurator_color['did']}"
					."&amp;my_post_key={$mybb->post_code}"
				);
				$form_container->output_cell($popup->fetch(), array("class" => "align_center"));
				$form_container->construct_row();
			}

			$form_container->end();
			$form->end();
			$page->output_footer();

			exit;
		}

		// DESIGN-AKZENTFARBEN HINZUFÜGEN
		if ($mybb->input['action'] == "add_accentcolor") {

			if ($mybb->request_method == "post") {

				// Check if required fields are not empty
				if (empty($mybb->input['tid'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_designID;
				}
				if (empty($mybb->input['light_root']) AND empty($mybb->input['dark_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_root;
				}
				if (empty($mybb->input['root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_rootdef;
				}
				if (empty($mybb->input['individual_colors'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_individualcolors;
				}

				// No errors - insert
				if (empty($errors)) {

					$designname = $db->fetch_field($db->simple_select("themes", "name", "tid = '".$mybb->input['tid']."'"), "name");

					$new_accentcolor = array(
						"name" => $db->escape_string($designname),
						"tid" => $db->escape_string($mybb->input['tid']),
						"root" => $db->escape_string($mybb->input['root']),
						"light_root" => $db->escape_string($mybb->input['light_root']),
						"dark_root" => $db->escape_string($mybb->input['dark_root']),
						"individual_colors" => $db->escape_string($mybb->input['individual_colors'])
					);

					$db->insert_query("designs", $new_accentcolor);

					$mybb->input['module'] = "designconfigurator&amp;action=accentcolor";
					$mybb->input['action'] = $lang->designconfigurator_manage_accentcolor_added;
					log_admin_action(htmlspecialchars_uni($designname));

					flash_message($lang->designconfigurator_manage_accentcolor_added, 'success');
					admin_redirect("index.php?module=style-designconfigurator&amp;action=accentcolor");
				}
			}

			$page->add_breadcrumb_item($lang->designconfigurator_manage_add_accentcolor);

			// Editor scripts
			$page->extra_header .= '
            <link rel="stylesheet" href="../jscripts/sceditor/themes/mybb.css" type="text/css" media="all" />
            <script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1822"></script>
            <script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1827"></script>
            <script type="text/javascript" src="../jscripts/sceditor/plugins/undo.js?ver=1805"></script>
            <link href="./jscripts/codemirror/lib/codemirror.css?ver=1813" rel="stylesheet">
            <link href="./jscripts/codemirror/theme/mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/lib/codemirror.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/xml/xml.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/javascript/javascript.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/css/css.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/htmlmixed/htmlmixed.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/dialog/dialog-mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/addon/dialog/dialog.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/searchcursor.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/search.js?ver=1821"></script>
            <script src="./jscripts/codemirror/addon/fold/foldcode.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/xml-fold.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/foldgutter.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/fold/foldgutter.css?ver=1813" rel="stylesheet">
            ';

			// Build options header
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_add_accentcolor);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc
			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];
			// individuelle Designfarbe hinzufügen Button
			$sub_tabs['designconfigurator_add_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_add_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=add_accentcolor",
				"description" => $lang->designconfigurator_manage_add_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_add_accentcolor');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Stylesheets Dropbox
			$theme = [];
			$sort = [];
			$themes_query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes t
            WHERE tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage != '')
            AND tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage = '' AND light_root != '' AND dark_root != '')
            AND tid != 1
            ORDER BY name ASC
            ");
			while ($themes = $db->fetch_array($themes_query)) {
				$tid = $themes['tid'];
				$theme[$tid] = $themes['name'];
				$sort[$tid] = $themes['name'];
			}

			// Build the form
			$form = new Form("index.php?module=style-designconfigurator&amp;action=add_accentcolor", "post", "", 1);
			$form_container = new FormContainer($lang->designconfigurator_manage_add_accentcolor);

			// Themen ID
			$form_container->output_row(
				$lang->designconfigurator_manage_design_designID_title."<em>*</em>",
				$lang->designconfigurator_manage_accentcolor_designID_desc,
				$form->generate_select_box('tid', $sort, '', array('id' => 'tid')),
				'tid'
			);

			// Standard Root
			$rootsystem = array(
				"" => $lang->designconfigurator_manage_design_root_select_def,
				"light" => $lang->designconfigurator_manage_design_root_select_light,
				"dark" => $lang->designconfigurator_manage_design_root_select_dark,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_root_title."<em>*</em>",
				$lang->designconfigurator_manage_design_root_desc,
				$form->generate_select_box('root', $rootsystem, '', array('id' => 'root')),
				'root'
			);

			// Aktzentfarbe(n)
			$form_container->output_row(
				$lang->designconfigurator_manage_accentcolor_individualcolors_title."<em>*</em>",
				$lang->designconfigurator_manage_accentcolor_individualcolors_desc,
				$form->generate_text_box('individual_colors', $mybb->get_input('individual_colors'))
			);

			$light_root_editor = $form->generate_text_area('light_root', $mybb->get_input('light_root'), array(
				'id' => 'light_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_lightmode_title,
				$lang->designconfigurator_manage_design_lightmode_desc,
				$light_root_editor,
				'light_root'
			);

			$dark_root_editor = $form->generate_text_area('dark_root', $mybb->get_input('dark_root'), array(
				'id' => 'dark_root',
				'style' => 'width: 99%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_darkmode_title,
				$lang->designconfigurator_manage_design_darkmode_desc,
				$dark_root_editor,
				'dark_root'
			);

			$form_container->end();
			$buttons[] = $form->generate_submit_button($lang->designconfigurator_manage_submit_add_accentcolor);
			$form->output_submit_wrapper($buttons);

			$form->end();

			$admin_options['codepress'] = 1;

			if ($admin_options['codepress'] != 0) {
				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("dark_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';

				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("light_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';
			}

			$page->output_footer();

			exit;
		}

		// DESIGN-AKZENTFARBEN BEARBEITEN
		if ($mybb->input['action'] == "edit_accentcolor") {

			if ($mybb->request_method == "post") {

				// Check if required fields are not empty
				if (empty($mybb->input['tid'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_designID;
				}
				if (empty($mybb->input['light_root']) AND empty($mybb->input['dark_root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_root;
				}
				if (empty($mybb->input['root'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_rootdef;
				}
				if (empty($mybb->input['individual_colors'])) {
					$errors[] = $lang->designconfigurator_manage_error_no_individualcolors;
				}

				// No errors - insert the terms of use
				if (empty($errors)) {

					$did = $mybb->get_input('did', MyBB::INPUT_INT);
					$designname = $db->fetch_field($db->simple_select("themes", "name", "tid = '".$mybb->input['tid']."'"), "name");

					$edited_accentcolor = [
						"tid" => $db->escape_string($mybb->input['tid']),
						"name" => $db->escape_string($designname),
						"root" => $db->escape_string($mybb->input['root']),
						"light_root" => $db->escape_string($mybb->input['light_root']),
						"dark_root" => $db->escape_string($mybb->input['dark_root']),
						"individual_colors" => $db->escape_string($mybb->input['individual_colors'])
					];

					$db->update_query("designs", $edited_accentcolor, "did='{$did}'");

					$mybb->input['module'] = "designconfigurator";
					$mybb->input['action'] = $lang->designconfigurator_manage_accentcolor_edited;
					log_admin_action(htmlspecialchars_uni($designname));

					flash_message($lang->designconfigurator_manage_accentcolor_edited, 'success');
					admin_redirect("index.php?module=style-designconfigurator&amp;action=accentcolor");
				}

			}

			$page->add_breadcrumb_item($lang->designconfigurator_manage_edit_accentcolor);

			// Editor scripts
			$page->extra_header .= '
            <link rel="stylesheet" href="../jscripts/sceditor/themes/mybb.css" type="text/css" media="all" />
            <script type="text/javascript" src="../jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=1822"></script>
            <script type="text/javascript" src="../jscripts/bbcodes_sceditor.js?ver=1827"></script>
            <script type="text/javascript" src="../jscripts/sceditor/plugins/undo.js?ver=1805"></script>
            <link href="./jscripts/codemirror/lib/codemirror.css?ver=1813" rel="stylesheet">
            <link href="./jscripts/codemirror/theme/mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/lib/codemirror.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/xml/xml.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/javascript/javascript.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/css/css.js?ver=1813"></script>
            <script src="./jscripts/codemirror/mode/htmlmixed/htmlmixed.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/dialog/dialog-mybb.css?ver=1813" rel="stylesheet">
            <script src="./jscripts/codemirror/addon/dialog/dialog.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/searchcursor.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/search/search.js?ver=1821"></script>
            <script src="./jscripts/codemirror/addon/fold/foldcode.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/xml-fold.js?ver=1813"></script>
            <script src="./jscripts/codemirror/addon/fold/foldgutter.js?ver=1813"></script>
            <link href="./jscripts/codemirror/addon/fold/foldgutter.css?ver=1813" rel="stylesheet">
            ';

			// Build options header
			$page->output_header($lang->designconfigurator_manage." - ".$lang->designconfigurator_manage_add_accentcolor);

			// Design Übersichtsseite Button
			$sub_tabs['designconfigurator'] = [
				"title" => $lang->designconfigurator_manage_overview_designs,
				"link" => "index.php?module=style-designconfigurator",
				"description" => $lang->designconfigurator_manage_overview_designs_desc
			];
			// Light/Dark Button
			$sub_tabs['designconfigurator_mode'] = [
				"title" => $lang->designconfigurator_manage_overview_mode,
				"link" => "index.php?module=style-designconfigurator&amp;action=mode",
				"description" => $lang->designconfigurator_manage_overview_mode_desc
			];
			// individuelle Designfarbe Button
			$sub_tabs['designconfigurator_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_overview_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=accentcolor",
				"description" => $lang->designconfigurator_manage_overview_accentcolor_desc
			];
			// individuelle Designfarbe hinzufügen Button
			$sub_tabs['designconfigurator_edit_accentcolor'] = [
				"title" => $lang->designconfigurator_manage_edit_accentcolor,
				"link" => "index.php?module=style-designconfigurator&amp;action=edit_accentcolor",
				"description" => $lang->designconfigurator_manage_edit_accentcolor_desc
			];

			$page->output_nav_tabs($sub_tabs, 'designconfigurator_edit_accentcolor');

			// Show errors
			if (isset($errors)) {
				$page->output_inline_error($errors);
			}

			// Get the data
			$did = $mybb->get_input('did', MyBB::INPUT_INT);

			// Stylesheets Dropbox
			$theme = [];
			$sorted = [];
			$themes_query = $db->query("SELECT * FROM ".TABLE_PREFIX."themes t
            WHERE tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage != '')
            AND tid NOT IN(SELECT tid FROM ".TABLE_PREFIX."designs WHERE headerimage = '' AND light_root != '' AND dark_root != '' AND tid = '".$did."')
            AND tid != 1
            ORDER BY name ASC
            ");
			while ($themes = $db->fetch_array($themes_query)) {
				$tid = $themes['tid'];
				$theme[$tid] = $themes['name'];
				$sorted[$tid] = $themes['name'];
			}
			$edit_query = $db->simple_select("designs", "*", "did={$did}");
			$edit_accentcolor = $db->fetch_array($edit_query);

			// Build the form
			$form = new Form("index.php?module=style-designconfigurator&amp;action=edit_accentcolor", "post", "", 1);
			echo $form->generate_hidden_field('did', $did);

			$form_container = new FormContainer($lang->designconfigurator_manage_edit_accentcolor);

			// Themen ID
			$form_container->output_row(
				$lang->designconfigurator_manage_design_designID_title."<em>*</em>",
				$lang->designconfigurator_manage_accentcolor_designID_desc,
				$form->generate_select_box('tid', $sorted, $edit_accentcolor['tid'], array('id' => 'tid')),
				'tid'
			);

			// Standard Root
			$rootsystem = array(
				"" => $lang->designconfigurator_manage_design_root_select_def,
				"light" => $lang->designconfigurator_manage_design_root_select_light,
				"dark" => $lang->designconfigurator_manage_design_root_select_dark,
			);
			$form_container->output_row(
				$lang->designconfigurator_manage_design_root_title."<em>*</em>",
				$lang->designconfigurator_manage_design_root_desc,
				$form->generate_select_box('root', $rootsystem, $edit_accentcolor['root'], array('id' => 'root')),
				'root'
			);

			// Aktzentfarbe(n)
			$form_container->output_row(
				$lang->designconfigurator_manage_accentcolor_individualcolors_title."<em>*</em>",
				$lang->designconfigurator_manage_accentcolor_individualcolors_desc,
				$form->generate_text_box('individual_colors', htmlspecialchars_uni($edit_accentcolor['individual_colors']))
			);

			$light_root_editor = $form->generate_text_area('light_root', $edit_accentcolor['light_root'], array(
				'id' => 'light_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_lightmode_title,
				$lang->designconfigurator_manage_design_lightmode_desc,
				$light_root_editor,
				'light_root'
			);

			$dark_root_editor = $form->generate_text_area('dark_root', $edit_accentcolor['dark_root'], array(
				'id' => 'dark_root',
				'style' => 'width: 75%;',
				'class' => '',
				'rows' => '30'
			)
			);

			$form_container->output_row(
				$lang->designconfigurator_manage_design_darkmode_title,
				$lang->designconfigurator_manage_design_darkmode_desc,
				$dark_root_editor,
				'dark_root'
			);

			$form_container->end();
			$buttons[] = $form->generate_submit_button($lang->designconfigurator_manage_submit_edit_accentcolor);
			$form->output_submit_wrapper($buttons);
			$form->end();

			$admin_options['codepress'] = 1;

			if ($admin_options['codepress'] != 0) {
				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("dark_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';

				echo '<script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("light_root"), {
                        lineNumbers: true,
                        lineWrapping: true,
                        foldGutter: true,
                        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                        viewportMargin: Infinity,
                        indentWithTabs: true,
                        indentUnit: 4,
                        mode: "text/html",
                        theme: "mybb"
                    });
                </script>';
			}

			$page->output_footer();

			exit;
		}

		// Delete Akzentfarbe
		if ($mybb->input['action'] == "delete_accentcolor") {

			// Get data
			$did = $mybb->get_input('did', MyBB::INPUT_INT);
			$query = $db->simple_select("designs", "*", "did={$did}");
			$del_accentcolor = $db->fetch_array($query);

			// Error Handling
			if (empty($did)) {
				flash_message($lang->designconfigurator_manage_error_invalid, 'error');
				admin_redirect("index.php?module=style-designconfigurator&amp;action=accentcolor");
			}

			// Cancel button pressed?
			if (isset($mybb->input['no']) && $mybb->input['no']) {
				admin_redirect("index.php?module=style-designconfigurator&amp;action=accentcolor");
			}

			if (!verify_post_check($mybb->input['my_post_key'])) {
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=style-designconfigurator&amp;action=accentcolor");
			} // all fine
			else {
				if ($mybb->request_method == "post") {

					$db->delete_query("designs", "did='{$did}'");

					$mybb->input['module'] = "designconfigurator&amp;action=accentcolor";
					$mybb->input['action'] = $lang->designconfigurator_manage_accentcolor_deleted;
					log_admin_action(htmlspecialchars_uni($del_accentcolor['name']));

					flash_message($lang->designconfigurator_manage_accentcolor_deleted, 'success');
					admin_redirect("index.php?module=style-designconfigurator&amp;action=accentcolor");
				} else {
					$page->output_confirm_action(
						"index.php?module=style-designconfigurator&amp;action=delete_accentcolor&amp;did={$did}",
						$lang->designconfigurator_manage_delete_page
					);
				}
			}
			exit;
		}

	}
}

// Root Design in die Headerincloude laden
function designconfigurator_headerinclude() {

	global $db, $cache, $mybb, $theme, $templates, $lang, $style, $headerinclude, $theme, $header, $footer, $rootsystem, $designheader, $headerimage, $designroot, $switcher_guest_js, $lightdark_button, $design_option, $activedimm;

	// STYLE ID
	if ($mybb->user['uid'] != 0) {
		$style_id = $mybb->user['style'];
	} else {
		$style_id = $theme['tid'];
	}
	$saveurl = $_SERVER['REQUEST_URI']; 

	if ($mybb->user['uid'] != 0) {	
		// DESIGNNAME
		$designname = $mybb->user['designname'];
		// LIGHT/DARK
		$designdimm = $mybb->user['designdimm'];
		// AKTZENTFARBEN
		$own_accentcolor = $mybb->user['individual_colors'];
	} else {
		$designname = "";
		$designdimm = "";
		$own_accentcolor = "";
	}

	// ARRAY BILDEN TIDs
	$tid_query = $db->simple_select("designs", "tid");
	$designTIDs = [];
	while ($designthemes = $db->fetch_array($tid_query)) {
		$designTIDs[] = $designthemes['tid'];
	}

	// wenn der aktuelle Style eine Style Option hat
	if (in_array($style_id, $designTIDs)) {

		// Rausfinden, was der Style für ne Option hat
		$designoption_header = $db->fetch_field($db->simple_select("designs", "headerimage", "tid = '".$style_id."'"), "headerimage");
		$designoption_individualcolors = $db->fetch_field($db->simple_select("designs", "individual_colors", "tid = '".$style_id."'"), "individual_colors");

		// Farb-/Headerwechsel
		if (!empty($designoption_header) AND empty($designoption_individualcolors)) {

			// Wenn kein Design ausgewählt wurde, dann Standard nehmen
			if (empty($designname)) {

				$count_default = $db->fetch_field($db->query("SELECT COUNT(*) as default_design FROM ".TABLE_PREFIX."designs
                WHERE tid = '".$style_id."'
                AND standard = '1'
                "), 'default_design');

				// Team hat vergessen eine Standardvariante zu setzen
				if ($count_default < 1) {

					// Erste Variante => did am kleinsten
					$default_did = $db->fetch_field($db->simple_select("designs", "did", "tid = '".$style_id."'", ["order_dir" => "ASC", "order_by" => "did", "limit" => "1"]), "did");

					$default_design = $db->fetch_field($db->simple_select("designs", "name", "did = '".$default_did."'"), "name");
					$default_root = $db->fetch_field($db->simple_select("designs", "root", "did = '".$default_did."'"), "root");
					$design_path = $db->fetch_field($db->simple_select("designs", "path", "did = '".$default_did."'"), "path");

				} else {

					// Standard-Modus
					$default_design = $db->fetch_field($db->simple_select("designs", "name", "tid = '".$style_id."' AND standard = '1'"), "name");
					$default_root = $db->fetch_field($db->simple_select("designs", "root", "tid = '".$style_id."' AND standard = '1'"), "root");
					$design_path = $db->fetch_field($db->simple_select("designs", "path", "tid = '".$style_id."' AND standard = '1'"), "path");

				}

				$lightroot = $db->fetch_field($db->simple_select("designs", "light_root", "name = '".$default_design."'"), "light_root");
				$darkroot = $db->fetch_field($db->simple_select("designs", "dark_root", "name = '".$default_design."'"), "dark_root");

				if (!empty($lightroot) AND !empty($darkroot)) {

					// Wenn auch kein Light/Dark ausgewählt ist => Standard
					if (empty($designdimm)) {

						if ($mybb->user['uid'] == '0') {

							if ($default_root == 'light') {
								if (!empty($_COOKIE['theme_design'])) {
									if ($_COOKIE['theme_design'] == "dark_design") {
										$defaultroot = "dark_root";
										$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
									}
								} else {
									$defaultroot = "light_root";
									$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
								}
							} else {
								if (!empty($_COOKIE['theme_design'])) {
									if ($_COOKIE['theme_design'] == "dark_design") {
										$defaultroot = "light_root";
										$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
									}
								} else {
									$defaultroot = "dark_root";
									$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
								}
							}

							$design_option = "design";
							eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_guest")."\";");
						} else {
							if ($default_root == 'light') {
								$defaultroot = "light_root";
								$activedimm = "dark";
								$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
							} else {
								$defaultroot = "dark_root";
								$activedimm = "light";
								$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
							}

							eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_member")."\";");
						}

						eval("\$switcher_guest_js .= \"" . $templates->get ("designconfigurator_switcher_guest_js_design") . "\";");

					} else {
						// Light == 1 || Dark == 2
						if ($designdimm == 1) {
							$defaultroot = "light_root";
							$activedimm = "dark";
							$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
						} else {
							$defaultroot = "dark_root";
							$activedimm = "light";
							$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
						}

						eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_member")."\";");
					}

				} else {
					if (!empty($lightroot)) {
						$defaultroot = "light_root";
					} else {
						$defaultroot = "dark_root";
					}
				}

				$designroot = $db->fetch_field($db->simple_select("designs", $defaultroot, "name = '".$default_design."'"), $defaultroot);

			} else {

				$lightroot = $db->fetch_field($db->simple_select("designs", "light_root", "name = '".$designname."'"), "light_root");
				$darkroot = $db->fetch_field($db->simple_select("designs", "dark_root", "name = '".$designname."'"), "dark_root");
				$default_root = $db->fetch_field($db->simple_select("designs", "root", "name = '".$designname."'"), "root");

				if (!empty($lightroot) AND !empty($darkroot)) {

					// Wenn auch kein Light/Dark ausgewählt ist => Standard
					if (empty($designdimm)) {
						if ($default_root == 'light') {
							$designroot = $lightroot;
						} else {
							$designroot = $darkroot;
						}
					} else {
						// Light == 1 || Dark == 2
						if ($designdimm == 1) {
							$designroot = $lightroot;
						} else {
							$designroot = $darkroot;
						}
					}

				} else {
					if (!empty($lightroot)) {
						$designroot = $lightroot;
					} else {
						$designroot = $darkroot;
					}
				}

				$design_path = $db->fetch_field($db->simple_select("designs", "path", "name = '".$designname."'"), "path");

			}


			if (!empty($design_path)) {
				$theme['imgdir'] = $design_path;
			}
			$rootsystem = "<style type=\"text/css\">{$designroot}</style>";
		}
		// Light/Dark Modus
		else if ($designoption_header == "" AND $designoption_individualcolors == "") {

			// Wenn kein Modus eingestellt ist, dann Standard nehmen
			if (empty($designdimm)) {

				// Standard-Modus
				$default_root = $db->fetch_field($db->simple_select("designs", "root", "tid = '".$style_id."'"), "root");

				if ($mybb->user['uid'] == '0') {

					if ($default_root == 'light') {
						if (!empty($_COOKIE['theme_mode'])) {
							if ($_COOKIE['theme_mode'] == "dark_mode") {
								$defaultroot = "dark_root";
								$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
							}
						} else {
							$defaultroot = "light_root";
							$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
						}
					} else {
						if (!empty($_COOKIE['theme_mode'])) {
							if ($_COOKIE['theme_mode'] == "dark_mode") {
								$defaultroot = "light_root";
								$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
							}
						} else {
							$defaultroot = "dark_root";
							$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
						}
					}

					$design_option = "mode";
					eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_guest")."\";");

				} else {
					if ($default_root == 'light') {
						$defaultroot = "light_root";
						$activedimm = "dark";
						$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
					} else {
						$defaultroot = "dark_root";
						$activedimm = "light";
						$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
					}

					eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_member")."\";");
				}

				eval("\$switcher_guest_js .= \"" . $templates->get ("designconfigurator_switcher_guest_js_mode") . "\";");

				$designroot = $db->fetch_field($db->simple_select("designs", $defaultroot, "tid = '".$style_id."'"), $defaultroot);

			} else {

				// Light == 1 || Dark == 2
				if ($designdimm == 1) {
					$designroot = $db->fetch_field($db->simple_select("designs", "light_root", "tid = '".$style_id."'"), "light_root");
					$activedimm = "dark";
					$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
				} else {
					$designroot = $db->fetch_field($db->simple_select("designs", "dark_root", "tid = '".$style_id."'"), "dark_root");
					$activedimm = "light";
					$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
				}

				eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_member")."\";");

			}

			$rootsystem = "<style type=\"text/css\">{$designroot}</style>";
		}
		// DESIGNS MIT AKZENTFARBEN
		else if ($designoption_header == "" AND $designoption_individualcolors != "") {

			$lightroot = $db->fetch_field($db->simple_select("designs", "light_root", "tid = '".$style_id."'"), "light_root");
			$darkroot = $db->fetch_field($db->simple_select("designs", "dark_root", "tid = '".$style_id."'"), "dark_root");
			$default_root = $db->fetch_field($db->simple_select("designs", "root", "tid = '".$style_id."'"), "root");

			if (!empty($lightroot) AND !empty($darkroot)) {

				// Wenn auch kein Light/Dark ausgewählt ist => Standard
				if (empty($designdimm)) {

					if ($mybb->user['uid'] == '0') {

						if ($default_root == 'light') {
							if (!empty($_COOKIE['theme_accentcolor'])) {
								if ($_COOKIE['theme_accentcolor'] == "dark_accentcolor") {
									$designroot = $darkroot;
									$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
								}
							} else {
								$designroot = $lightroot;
								$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
							}
						} else {
							if (!empty($_COOKIE['theme_accentcolor'])) {
								if ($_COOKIE['theme_accentcolor'] == "dark_accentcolor") {
									$designroot = $lightroot;
									$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
								}
							} else {
								$designroot = $darkroot;
								$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
							}
						}

						$design_option = "accentcolor";
						eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_guest")."\";");
					} else {
						if ($default_root == 'light') {
							$designroot = $lightroot;
							$activedimm = "dark";
							$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
						} else {
							$designroot = $darkroot;
							$activedimm = "light";
							$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
						}

						eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_member")."\";");
					}

					eval("\$switcher_guest_js .= \"" . $templates->get ("designconfigurator_switcher_guest_js_accentcolor") . "\";");

				} else {
					// Light == 1 || Dark == 2
					if ($designdimm == 1) {
						$designroot = $lightroot;
						$activedimm = "dark";
						$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_dark;
					} else {
						$designroot = $darkroot;
						$activedimm = "light";
						$lang->switcher_lightdarkbutton = $lang->switcher_lightdarkbutton_light;
					}

					eval("\$lightdark_button .= \"" . $templates->get("designconfigurator_switcher_button_member")."\";");
				}

			} else {
				if (!empty($lightroot)) {
					$designroot = $lightroot;
				} else {
					$designroot = $darkroot;
				}
			}

			if (!empty($own_accentcolor)) {

				// Farbe(n) in Array (spalten)
				$individualcolors = explode (", ", $own_accentcolor);

				// So oft durchgehen wie Farben eintragbar sind
				$allroot_colors = "";
				$number = 0;
				foreach ($individualcolors as $individualcolor) {

					$number ++;

					$root_accentcolor = "--accent".$number.": ".$individualcolor.";";

					$allroot_colors .= $root_accentcolor."\n";
				}

			} else {

				// Aktzentfarbe(n) ziehen
				$individualcolors = $db->fetch_field($db->simple_select("designs", "individual_colors", "tid = '".$style_id."'"), "individual_colors");
				// Farbe(n) in Array (spalten)
				$individualcolors = explode (", ", $individualcolors);

				// So oft durchgehen wie Farben eintragbar sind
				$allroot_colors = "";
				$number = 0;
				foreach ($individualcolors as $individualcolor) {

					$number ++;

					$root_accentcolor = "--accent".$number.": ".$individualcolor.";";

					$allroot_colors .= $root_accentcolor."\n";
				}

			}

			$rootsystem = "<style type=\"text/css\">:root {\n".$allroot_colors."}\n".$designroot."</style>";

		}

	} else {
		$rootsystem = "";
	}

}

// USERCP
// Anzeige Usercp-Menu
function designconfigurator_usercpmenu() {
	global $mybb, $templates, $lang, $usercpmenu, $collapsed, $collapsedimg;

	// SPRACHDATEI LADEN
	$lang->load("designconfigurator");

	eval("\$usercpmenu .= \"".$templates->get("designconfigurator_usercp_nav")."\";");
}

// Seite im Usercp
function designconfigurator_usercp() {

	global $mybb, $db, $plugins, $templates, $theme, $lang, $header, $headerinclude, $footer, $usercpnav, $accentcolors_own, $designswitch, $designswitch_link, $avtive_design, $mode_option, $lightdarkmode, $accentcolors, $accentcolors_add, $color_own;

	// SPRACHDATEI LADEN
	$lang->load("designconfigurator");

	// DAS ACTION MENÜ
	$mybb->input['action'] = $mybb->get_input('action');

	// USER-ID
	$user_id = $mybb->user['uid'];

	// STYLE ID
	$style_id = $mybb->user['style'];

	// DESIGNNAME
	$designname = $mybb->user['designname'];

	// LIGHT/DARK
	$designdimm = $mybb->user['designdimm'];

	// AKTZENTFARBEN
	$own_accentcolor = $mybb->user['individual_colors'];

	// BENUTZERGRUPPEN
	$usergroup = $mybb->user['usergroup'];
	$additionalgroups  = $mybb->user['additionalgroups'];

	if ($mybb->input['action'] == "designconfigurator") {

        add_breadcrumb($lang->designconfigurator_usercp, "usercp.php?action=designconfigurator");

		// ARRAY BILDEN TIDs
		$tid_query = $db->simple_select("designs", "tid");
		$designTIDs = [];
		while ($designthemes = $db->fetch_array($tid_query)) {
			$designTIDs[] = $designthemes['tid'];
		}

		if (in_array($style_id, $designTIDs)) {

			// Rausfinden, was der Style für ne Option hat
			$designoption_header = $db->fetch_field($db->simple_select("designs", "headerimage", "tid = '".$style_id."'"), "headerimage");
			$designoption_individualcolors = $db->fetch_field($db->simple_select("designs", "individual_colors", "tid = '".$style_id."'"), "individual_colors");


			// Headerimage = Farb-/Headerwechsel
			if (!empty($designoption_header) AND empty($designoption_individualcolors)) {

				if (!empty($additionalgroups)) {
					$additionalgroups = explode (",", $additionalgroups);
					$additionalgroups_sql = "";	
					foreach ($additionalgroups as $additionalgroup) {
						$additionalgroups_sql .= "OR (concat(',',allowed_usergroups,',') LIKE '%,".$additionalgroup.",%')\n";
					}
				} else {
					$additionalgroups_sql = "";
				}

				$designs_query = $db->query("SELECT * FROM ".TABLE_PREFIX."designs
                WHERE tid = '".$style_id."'
				AND allowed_usergroups = 'all' OR (concat(',',allowed_usergroups,',') LIKE '%,".$usergroup.",%') 
				$additionalgroups_sql
				ORDER BY name ASC
                ");

				// Headerbildchen auslesen lassen
				while ($designs = $db->fetch_array($designs_query)) {

					// Helligkeitsmodus erkennen
					if (!empty($designs['light_root'])) {
						$sonne = $lang->designconfigurator_lightdark_info_light;
					} else {
						$sonne = "";
					}
					if (!empty($designs['dark_root'])) {
						$mond = $lang->designconfigurator_lightdark_info_dark;
					} else {
						$mond = "";
					}
					if (!empty($designs['light_root']) && !empty($designs['dark_root'])) {
						$and = " & ";
					} else {
						$and = "";
					}

					$mode_option = $sonne.$and.$mond;

					// Aktuelles Design verblassen lassen
					if ($designname != '') {

						if ($designname == $designs['name']) {
							$avtive_design = "opacity: 0.5;";
							eval("\$designswitch_link = \"".$templates->get("designconfigurator_designswitch_active")."\";");
						} else {
							$avtive_design = "";
							eval("\$designswitch_link = \"".$templates->get("designconfigurator_designswitch_link")."\";");
						}

					} else {

						// Standard Design
						$count_default = $db->fetch_field($db->query("SELECT COUNT(*) as default_design FROM ".TABLE_PREFIX."designs
                        WHERE tid = '".$style_id."'
                        AND standard = '1'
                        "), 'default_design');

						// Team hat vergessen eine Standardvariante zu setzen
						if ($count_default < 1) {
							// Erste Variante => did am kleinsten
							$default_did = $db->fetch_field($db->simple_select("designs", "did", "tid = '".$style_id."'", ["order_dir" => "ASC", "order_by" => "did", "limit" => "1"]), "did");
							$default_design = $db->fetch_field($db->simple_select("designs", "name", "did = '".$default_did."'"), "name");
						} else {
							// Standard-Modus
							$default_design = $db->fetch_field($db->simple_select("designs", "name", "tid = '".$style_id."' AND standard = '1'"), "name");
						}

						if ($default_design == $designs['name']) {
							$avtive_design = "opacity: 0.5;";
							eval("\$designswitch_link = \"".$templates->get("designconfigurator_designswitch_active")."\";");
						} else {
							$avtive_design = "";
							eval("\$designswitch_link = \"".$templates->get("designconfigurator_designswitch_link")."\";");
						}

					}

					eval("\$designswitch .= \"" . $templates->get ("designconfigurator_designswitch") . "\";");
				}

				// keine Angaben vom User => Standard
				if (empty($designname)) {

					$count_default = $db->fetch_field($db->query("SELECT COUNT(*) as default_design FROM ".TABLE_PREFIX."designs
                    WHERE tid = '".$style_id."'
                    AND standard = '1'
                    "), 'default_design');

					// Team hat vergessen eine Standardvariante zu setzen
					if ($count_default < 1) {
						// Erste Variante => did am kleinsten
						$default_did = $db->fetch_field($db->simple_select("designs", "did", "tid = '".$style_id."'", ["order_dir" => "ASC", "order_by" => "did", "limit" => "1"]), "did");
						$designname = $db->fetch_field($db->simple_select("designs", "name", "did = '".$default_did."'"), "name");
					} else {
						$designname = $db->fetch_field($db->simple_select("designs", "name", "tid = '".$style_id."' AND standard = '1'"), "name");
					}

				} else {
					$designname = $designname;
				}

				$lightroot = $db->fetch_field($db->simple_select("designs", "light_root", "name = '".$designname."'"), "light_root");
				$darkroot = $db->fetch_field($db->simple_select("designs", "dark_root", "name = '".$designname."'"), "dark_root");
				$defroot = $db->fetch_field($db->simple_select("designs", "root", "name = '".$designname."'"), "root");

				// Filtern hat der Style Light/Dark
				if (!empty($lightroot) AND !empty($darkroot)) {

					if (!empty($designdimm)) {

						// Light ist ausgefällt - light blass + kein Link
						if ($designdimm == "1") {

							$lightdarkmode_active_light = "style=\"opacity: 0.5;\"";
							$lightdarkmode_active_dark = "";

							$lightdarkmode_link_light = $lang->designconfigurator_lightdark_link_light;
							$lightdarkmode_link_dark = "<a href=\"usercp.php?action=designconfigurator&designdimm=dark\">".$lang->designconfigurator_lightdark_link_dark."</a>";
						}
						// Dark ist ausgefällt - dark blass + kein Link
						else {

							$lightdarkmode_active_light = "";
							$lightdarkmode_active_dark = "style=\"opacity: 0.5;\"";

							$lightdarkmode_link_light = "<a href=\"usercp.php?action=designconfigurator&designdimm=light\">".$lang->designconfigurator_lightdark_link_light."</a>";
							$lightdarkmode_link_dark = $lang->designconfigurator_lightdark_link_dark;
						}

					} else {
						if ($defroot == "light") {

							$lightdarkmode_active_light = "style=\"opacity: 0.5;\"";
							$lightdarkmode_active_dark = "";

							$lightdarkmode_link_light = $lang->designconfigurator_lightdark_link_light;
							$lightdarkmode_link_dark = "<a href=\"usercp.php?action=designconfigurator&designdimm=dark\">".$lang->designconfigurator_lightdark_link_dark."</a>";

						} else {

							$lightdarkmode_active_light = "";
							$lightdarkmode_active_dark = "style=\"opacity: 0.5;\"";

							$lightdarkmode_link_light = "<a href=\"usercp.php?action=designconfigurator&designdimm=light\">".$lang->designconfigurator_lightdark_link_light."</a>";
							$lightdarkmode_link_dark = $lang->designconfigurator_lightdark_link_dark;
						}
					}

					eval("\$lightdarkmode .= \"" . $templates->get ("designconfigurator_lightdarkmode") . "\";");

				} else {

					if (!empty($lightroot)) {
						$designroot = $lang->designconfigurator_lightdarkmode_none_light;
					} else {
						$designroot = $lang->designconfigurator_lightdarkmode_none_dark;
					}

					$lang->designconfigurator_lightdarkmode_none = $lang->sprintf($lang->designconfigurator_lightdarkmode_none_mode, $designroot);

					eval("\$lightdarkmode .= \"" . $templates->get ("designconfigurator_lightdarkmode_none") . "\";");
				}

				eval("\$accentcolors .= \"" . $templates->get ("designconfigurator_accentcolor_none") . "\";");

			}
			// LIGHT / DARK DESIGN
			else if (empty($designoption_header) AND empty($designoption_individualcolors)) {

				// Wenn kein Light/Dark ausgewählt => Standard
				if (empty($designdimm)) {

					$defroot = $db->fetch_field($db->simple_select("designs", "root", "tid = '".$style_id."'"), "root");

					if ($defroot == "light") {

						$lightdarkmode_active_light = "style=\"opacity: 0.5;\"";
						$lightdarkmode_active_dark = "";

						$lightdarkmode_link_light = $lang->designconfigurator_lightdark_link_light;
						$lightdarkmode_link_dark = "<a href=\"usercp.php?action=designconfigurator&designdimm=dark\">".$lang->designconfigurator_lightdark_link_dark."</a>";

					} else {

						$lightdarkmode_active_light = "";
						$lightdarkmode_active_dark = "style=\"opacity: 0.5;\"";

						$lightdarkmode_link_light = "<a href=\"usercp.php?action=designconfigurator&designdimm=light\">".$lang->designconfigurator_lightdark_link_light."</a>";
						$lightdarkmode_link_dark = $lang->designconfigurator_lightdark_link_dark;
					}


				} else {

					// Light ist ausgefüllt - light blass + kein Link
					if ($designdimm == "1") {

						$lightdarkmode_active_light = "style=\"opacity: 0.5;\"";
						$lightdarkmode_active_dark = "";

						$lightdarkmode_link_light = $lang->designconfigurator_lightdark_link_light;
						$lightdarkmode_link_dark = "<a href=\"usercp.php?action=designconfigurator&designdimm=dark\">".$lang->designconfigurator_lightdark_link_dark."</a>";
					}

					// Dark ist ausgefällt - dark blass + kein Link
					else {

						$lightdarkmode_active_light = "";
						$lightdarkmode_active_dark = "style=\"opacity: 0.5;\"";

						$lightdarkmode_link_light = "<a href=\"usercp.php?action=designconfigurator&designdimm=light\">".$lang->designconfigurator_lightdark_link_light."</a>";
						$lightdarkmode_link_dark = $lang->designconfigurator_lightdark_link_dark;
					}

				}

				eval("\$lightdarkmode .= \"" . $templates->get ("designconfigurator_lightdarkmode") . "\";");

				eval("\$designswitch .= \"" . $templates->get ("designconfigurator_designswitch_none") . "\";");
				eval("\$accentcolors .= \"" . $templates->get ("designconfigurator_accentcolor_none") . "\";");
			}
			// DESIGNS MIT AKZENTFARBEN
			else if (empty($designoption_header) AND !empty($designoption_individualcolors)) {

				$lightroot = $db->fetch_field($db->simple_select("designs", "light_root", "tid = '".$style_id."'"), "light_root");
				$darkroot = $db->fetch_field($db->simple_select("designs", "dark_root", "tid = '".$style_id."'"), "dark_root");
				$defroot = $db->fetch_field($db->simple_select("designs", "root", "tid = '".$style_id."'"), "root");

				// Filtern hat der Style Light/Dark
				if (!empty($lightroot) AND !empty($darkroot)) {

					if (!empty($designdimm)) {

						// Light ist ausgefällt - light blass + kein Link
						if ($designdimm == "1") {

							$lightdarkmode_active_light = "style=\"opacity: 0.5;\"";
							$lightdarkmode_active_dark = "";

							$lightdarkmode_link_light = $lang->designconfigurator_lightdark_link_light;
							$lightdarkmode_link_dark = "<a href=\"usercp.php?action=designconfigurator&designdimm=dark\">".$lang->designconfigurator_lightdark_link_dark."</a>";
						}
						// Dark ist ausgefällt - dark blass + kein Link
						else {

							$lightdarkmode_active_light = "";
							$lightdarkmode_active_dark = "style=\"opacity: 0.5;\"";

							$lightdarkmode_link_light = "<a href=\"usercp.php?action=designconfigurator&designdimm=light\">".$lang->designconfigurator_lightdark_link_light."</a>";
							$lightdarkmode_link_dark = $lang->designconfigurator_lightdark_link_dark;
						}

					} else {
						if ($defroot == "light") {

							$lightdarkmode_active_light = "style=\"opacity: 0.5;\"";
							$lightdarkmode_active_dark = "";

							$lightdarkmode_link_light = $lang->designconfigurator_lightdark_link_light;
							$lightdarkmode_link_dark = "<a href=\"usercp.php?action=designconfigurator&designdimm=dark\">".$lang->designconfigurator_lightdark_link_dark."</a>";

						} else {

							$lightdarkmode_active_light = "";
							$lightdarkmode_active_dark = "style=\"opacity: 0.5;\"";

							$lightdarkmode_link_light = "<a href=\"usercp.php?action=designconfigurator&designdimm=light\">".$lang->designconfigurator_lightdark_link_light."";
							$lightdarkmode_link_dark = $lang->designconfigurator_lightdark_link_dark;
						}
					}

					eval("\$lightdarkmode .= \"" . $templates->get ("designconfigurator_lightdarkmode") . "\";");

				} else {

					if (!empty($lightroot)) {
						$designroot = $lang->designconfigurator_lightdarkmode_none_light;
					} else {
						$designroot = $lang->designconfigurator_lightdarkmode_none_dark;
					}

					$lang->designconfigurator_lightdarkmode_none = $lang->sprintf($lang->designconfigurator_lightdarkmode_none_mode, $designroot);

					eval("\$lightdarkmode .= \"" . $templates->get ("designconfigurator_lightdarkmode_none") . "\";");
				}

				// Aktzentfarbe(n) ziehen
				$individualcolors = $db->fetch_field($db->simple_select("designs", "individual_colors", "tid = '".$style_id."'"), "individual_colors");
				// Farbe(n) in Array (spalten)
				$individualcolors = explode (", ", $individualcolors);

				$number = 0;
				// so oft wie Farben angegeben, so oft Inputfeld
				foreach ($individualcolors as $accentcolor) {

					$number ++;

					// EIGENE FARBE(N) ANZEIGEN
					$accentcolors_own = "";
					if (!empty($own_accentcolor)) {

						// Farbe(n) in Array (spalten)
						$own_individualcolors = explode (", ", $own_accentcolor);
						// Array fängt bei 0 an
						$color_number = $number - 1;
						// Hex von de Stelle $color_number
						$color_own = $own_individualcolors[$color_number];

						if ($color_own != $accentcolor) {
							eval("\$accentcolors_own .= \"" . $templates->get ("designconfigurator_accentcolor_own") . "\";");
						}
					}

					eval("\$accentcolors_add .= \"" . $templates->get ("designconfigurator_accentcolor_add") . "\";");
				}

				if ($number > 1) {
					$lang->designconfigurator_accentcolor_button = $lang->designconfigurator_accentcolors_button;
				} else {
					$lang->designconfigurator_accentcolor_button = $lang->designconfigurator_accentcolor_button;
				}

				eval("\$accentcolors .= \"" . $templates->get ("designconfigurator_accentcolor") . "\";");


				eval("\$designswitch .= \"" . $templates->get ("designconfigurator_designswitch_none") . "\";");
			}


		} else {

			$lang->designconfigurator_lightdarkmode_none = $lang->designconfigurator_lightdarkmode_none;

			eval("\$designswitch .= \"" . $templates->get ("designconfigurator_designswitch_none") . "\";");
			eval("\$lightdarkmode .= \"" . $templates->get ("designconfigurator_lightdarkmode_none") . "\";");
			eval("\$accentcolors .= \"" . $templates->get ("designconfigurator_accentcolor_none") . "\";");
		}

		// Farb-/Headerkombination wechseln
		$designswitch_name = $mybb->get_input('designswitch');
		if ($designswitch_name) {

			$update_designame = [
				"designname" => $db->escape_string($designswitch_name)
			];

			$db->update_query("users", $update_designame, "uid = '".$user_id."'");

			redirect("usercp.php?action=designconfigurator", $lang->designconfigurator_redirect_designswitch);

		}

		// Light-/Dark wechseln
		$modeswitch = $mybb->get_input('designdimm');
		if ($modeswitch) {

			if ($modeswitch == "light") {
				$modeswitch = "1";
			}
			if ($modeswitch == "dark") {
				$modeswitch = "2";
			}

			$update_designdimm = [
				"designdimm" => $db->escape_string($modeswitch)
			];

			$db->update_query("users", $update_designdimm, "uid = '".$user_id."'");

			redirect("usercp.php?action=designconfigurator", $lang->designconfigurator_redirect_designdimm);

		}


		// Light-/Dark wechseln - Global Button
		//wurde der button gedrückt? 
		if(isset($mybb->input['send_indexdimm'])) {
			$indexswitch = $mybb->get_input('indexdimm');
			if ($indexswitch) {

				if ($indexswitch == "light") {
					$indexswitch = "1";
				}
				if ($indexswitch == "dark") {
					$indexswitch = "2";
				}

				$update_indexswitchdimm = [
					"designdimm" => $db->escape_string($indexswitch)
				];

				$db->update_query("users", $update_indexswitchdimm, "uid = '".$user_id."'");

				redirect($mybb->get_input('saveurl'), $lang->designconfigurator_redirect_designdimm);

			}
		}

		// TEMPLATE FÜR DIE SEITE
		eval("\$page = \"".$templates->get("designconfigurator")."\";");
		output_page($page);
		die();
	}

	// Aktzentfarbe(n) speichern
	if ($mybb->input['action'] == "designconfigurator_accentcolor") {

		// Aktzentfarbe(n) ziehen
		$individualcolors = $db->fetch_field($db->simple_select("designs", "individual_colors", "tid = '".$style_id."'"), "individual_colors");
		// Farbe(n) in Array (spalten)
		$individualcolors = explode (", ", $individualcolors);

		// So oft durchgehen wie Farben eintragbar sind
		$allcolors = "";
		$number = 0;
		foreach ($individualcolors as $individualcolor) {

			$number ++;

			// Feld muss für Farbe ausgefüllt sein, sonst Standardfarbe eintragen
			if (!empty($mybb->get_input('accentcolor'.$number.''))) {
				$new_accentcolor = $mybb->get_input('accentcolor'.$number.'');
			} else {
				$new_accentcolor = $individualcolor;
			}

			$allcolors .= $new_accentcolor.", ";
		}

		// letztes Komma und Leerzeichen vom Farb-String entfernen
		$allcolors_string = substr($allcolors, 0, -2);

		$new_colors = [
			"individual_colors" => $db->escape_string($allcolors_string),
		];

		$db->update_query("users", $new_colors, "uid = '{$user_id}'");

		redirect("usercp.php?action=designconfigurator", $lang->designconfigurator_redirect_accentcolor);

	}

}

// Werte löschen, wenn Style geändert wird
function designconfigurator_userupdate(&$datahandler) {

	global $db, $user;

	if ($datahandler->user_update_data['style']) {

		$update_designconfi = [
			"designname" => '',
			"individual_colors" => '',
			"designdimm" => '',
		];

		$db->update_query("users", $update_designconfi, "uid = '".$user['uid']."'");
	}
}

// ONLINE ANZEIGE - WER IST WO
function designconfigurator_online_activity($user_activity) {

	global $parameters, $user;

	$split_loc = explode(".php", $user_activity['location']);
	if ($split_loc[0] == $user['location']) {
		$filename = '';
	} else {
		$filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));
	}

	switch ($filename) {
		case 'usercp':
			if ($parameters['action'] == "designconfigurator") {
				$user_activity['activity'] = "designconfigurator";
			}
			break;
	}


	return $user_activity;
}

function designconfigurator_online_location($plugin_array) {

	global $mybb, $theme, $lang;

	if ($plugin_array['user_activity']['activity'] == "designconfigurator") {
		$plugin_array['location_name'] = $lang->designconfigurator_online_location;
	}


	return $plugin_array;
}

// MyALERTS
function designconfigurator_alerts() {

	global $mybb, $lang;

	$lang->load('designconfigurator');

	/**
	* Alert formatter for my custom alert type.
	*/
	class MybbStuff_MyAlerts_Formatter_designconfiguratornewDesignFormatter extends MybbStuff_MyAlerts_Formatter_AbstractFormatter {
		
        /**
		* Format an alert into it's output string to be used in both the main alerts listing page and the popup.
		*
		* @param MybbStuff_MyAlerts_Entity_Alert $alert The alert to format.
		*
		* @return string The formatted alert string.
		*/
		public function formatAlert(MybbStuff_MyAlerts_Entity_Alert $alert, array $outputAlert) {
			global $db;
			$alertContent = $alert->getExtraDetails();
			$userid = $db->fetch_field($db->simple_select("users", "uid", "username = '{$alertContent['username']}'"), "uid");
			$user = get_user($userid);
			$alertContent['username'] = format_name($user['username'], $user['usergroup'], $user['displaygroup']);
			return $this->lang->sprintf(
				$this->lang->designconfigurator_newDesign,
				$alertContent['username'],
				$alertContent['from'],
				$alertContent['themename']
			);
	
        }

        /**
        * Init function called before running formatAlert(). Used to load language files and initialize other required
        * resources.
        *
        * @return void
        */
        public function init() {
            if (!$this->lang->designconfigurator_newDesign) {
                $this->lang->load('designconfigurator');
            }	
        }

        /**
        * Build a link to an alert's content so that the system can redirect to it.
        *
        * @param MybbStuff_MyAlerts_Entity_Alert $alert The alert to build the link for.
        *
        * @return string The built alert, preferably an absolute link.
        */
        public function buildShowLink(MybbStuff_MyAlerts_Entity_Alert $alert) {
            $alertContent = $alert->getExtraDetails();
            return $this->mybb->settings['bburl'] . '/usercp.php?action=designconfigurator';
        }
    }

    if (class_exists('MybbStuff_MyAlerts_AlertFormatterManager')) {
        $formatterManager = MybbStuff_MyAlerts_AlertFormatterManager::getInstance();

        if (!$formatterManager) {
            $formatterManager = MybbStuff_MyAlerts_AlertFormatterManager::createInstance($mybb, $lang);
        }

        $formatterManager->registerFormatter(
            new MybbStuff_MyAlerts_Formatter_designconfiguratornewDesignFormatter($mybb, $lang, 'designconfigurator_newDesign')
        );
    }

}

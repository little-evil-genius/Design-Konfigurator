# Design-Konfigurator
Mit diesem Plugin lassen sich über das AdminCP verschiedene Designoptionen per Root Verzeichnis festlegen. Unter anderem Light-/Darkmodes, Designs mit Aktzentfarben, welche die User individuell anpassen können und verschiedene Farb-/Headervarianten. Die User können dann auf einer extra Seite sich ihre Wunschvariante selbstaussuchen.

# Designs mit verschiedene Farb- und Headervarianten

# Designs mit einen Light-/Darkmodus

# Designs mit individuellen Design-Akzentfarben

# Vorraussetzungen
Eine ganz klare Vorraussetzung für dieses Plugin sind Designs mit einem Root-Verzeichnis. Es müssen nicht zwangsläufig all eure Designs so erstellt worden sein, aber die Designs, welche ihr über das Plugin steuern möchtet. Für Root-Verzeichnisse gibt es im Netz etliche Tutorials. Ich habe euch das Tutorial von White_Rabbit (Tom) verlinkt - (<a href="https://epic.quodvide.de/showthread.php?tid=124">EPIC</a> || <a href="https://storming-gates.de/showthread.php?tid=1012727">SG</a>)<br>
<br>
Damit die Admins im ACP die neue Seite sehen können müssen die Administrator-Berechtigungen angepasst werden. Dafür geht ihr im ACP auf den Reiter Benutzer & Gruppen und klickt links im Menü Administrator-Berechtigungen an. Dort geht ihr auf den Reiter Benutzergruppen-Berechtigungen und klickt bei der entsprechenden Gruppe auf Optionen und dann auf Berechtigungen ändern. Dort wählt ihr den Reiter Templates & Style aus und stellt bei "Kann den Inhalt des Design Konfigurator verwalten?" auf ja. So kann diese Gruppe nun auf den Design Konfigurator zugreifen.

# Datenbank-Änderungen
hinzugefügte Tabelle:
- PRÄFIX_designs

hinzugefügte Spalten in der Tabelle PRÄFIX_users:
- designname
- designdimm
- individual_colors

# Neue Template-Gruppe innerhalb der Design-Templates
- Design Konfigurator

# Neue Templates (nicht global!)
- designconfigurator	
- designconfigurator_accentcolor	
- designconfigurator_accentcolor_add	
- designconfigurator_accentcolor_none	
- designconfigurator_accentcolor_own	
- designconfigurator_designswitch	
- designconfigurator_designswitch_active	
- designconfigurator_designswitch_link	
- designconfigurator_designswitch_none	
- designconfigurator_lightdarkmode	
- designconfigurator_lightdarkmode_none	
- designconfigurator_switcher_button_guest	
- designconfigurator_switcher_button_member	
- designconfigurator_switcher_guest_js_accentcolor	
- designconfigurator_switcher_guest_js_design	
- designconfigurator_switcher_guest_js_mode	
- designconfigurator_usercp_nav

# Template Änderungen - neue Variablen
- headerinclude - {$rootsystem}{$switcher_guest_js}
- footer - {$lightdark_button}

# Neues CSS - designconfigurator.css
Wird automatisch in jedes bestehende und neue Design hinzugefügt. Man sollte es einfach einmal abspeichern, bevor man im Board mit der Untersuchungsfunktion dies bearbeiten will, da es dann passieren kann, dass das CSS für dieses Plugin in ein anderen Stylesheet gerutscht ist, obwohl es im ACP richtig ist.

# Empfehlungen
- <a href="https://github.com/MyBBStuff/MyAlerts" target="_blank">MyAlerts</a> von EuanT
- Eingebundene Icons von Fontawesome (kann man sonst auch in der Sprachdatei ändern)

# Links
<b>ACP</b>
- euerforum.de/admin/index.php?module=style-designconfigurator
- euerforum.de/admin/index.php?module=style-designconfigurator&action=add_design
- euerforum.de/admin/index.php?module=style-designconfigurator&action=edit_design&did=X
- euerforum.de/admin/index.php?module=style-designconfigurator&action=mode
- euerforum.de/admin/index.php?module=style-designconfigurator&action=add_mode
- euerforum.de/admin/index.php?module=style-designconfigurator&action=edit_mode&did=X
- euerforum.de/admin/index.php?module=style-designconfigurator&action=accentcolor
- euerforum.de/admin/index.php?module=style-designconfigurator&action=add_accentcolor
- euerforum.de/admin/index.php?module=style-designconfigurator&action=edit_accentcolor&did=X
<b>USER-CP</b>
- euerforum.de/usercp.php?action=designconfigurator

# Dark-/Lightbutton auf dem Index
Damit man nicht jedes mal die User-CP Seite aufrufen muss und die Helligkeitsvariante vom Design zu wechseln habe ich noch ein Button programmiert, welcher global einsetzbar ist. Standardmäßig wird die Variable für den Button im Footer Template hinter den Stylechanger gelegt. Da die Variable aber global ist könnt ihr diese über all einfügen. Ihr solltet nur beachten, dass Gäste und User den beide sehen können. Da die Einstellung

# Disclaimer
Der Javascript-Code für den Gäste Dark-/Lightbutton auf dem Index stammt aus diesem Tutorial (<a href="https://storming-gates.de/showthread.php?tid=1012199">SG Thema</a> || <a href="https://epic.quodvide.de/showthread.php?tid=74">EPIC Thema</a>) und wurde nur entsprechend angepasst.

# Demo
<b>ACP - Farb- und Headervarianten</b>
<img src="https://www.bilder-hochladen.net/files/big/m4bn-ej-5fc5.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-ek-8ba3.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-el-36ed.png">
<br><br>
<b>ACP - Light-/Darkmodus</b>
<img src="https://www.bilder-hochladen.net/files/big/m4bn-em-cf1a.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-en-2a3d.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-en-2a3d.png">
<br><br>
<b>ACP - Design-Akzentfarbe(n)</b>
<img src="https://www.bilder-hochladen.net/files/m4bn-es-4b2b.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-et-be21.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-eu-9ce0.png">
<br><br>
<b>USER-CP</b>
<img src="https://www.bilder-hochladen.net/files/big/m4bn-er-2864.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-ep-fdf1.png">
<img src="https://www.bilder-hochladen.net/files/big/m4bn-eq-bdb2.png">
<img src="https://www.bilder-hochladen.net/files/m4bn-ev-6e85.png">
<img src="https://www.bilder-hochladen.net/files/m4bn-ew-de40.png">

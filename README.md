# Design Konfigurator
Mit diesem Plugin lassen sich über das Admin-CP verschiedene Designoptionen per Custom properties (CSS-Variablen) festlegen. Unter anderem Light-/Darkmodes, Designs mit Aktzentfarben, welche die User individuell anpassen können und verschiedene Farb-/Headervarianten. Die User können dann auf einer extra Seite sich ihre Wunschvariante selbstaussuchen.

# Designs mit verschiedene Farb- und Headervarianten
Wer kennt es nicht, man hat ein Haufen Header aber keine Lust oder Ideen für ein komplett neues Design? Sowieso mag man das aktuelle Design vom Code her sehr? Also dupliziert man das Design und tauschte die Grafiken und Farben aus? Mit dem Design Konfigurator muss man keine Designs mehr duplizieren, sondern kann im ACP ganz einfach eine neue Farb-/Headervariante erstellen für ein bestimmtes Design. Wichtig dafür ist, dass die Farben und alles was sich je nach Farb-/Headervariante verändern soll mit sogenanneten Custom properties (CSS-Variablen) definiert wurden. Denn für jede neue Farb-/Headervariante definieren wir neue Werte für diese Variabeln.<br><br>

<b>Neue Farb- und Headervariante hinzufügen:</b><br>
Wenn ihr eine neue Variante hinzufügen wollt wird euch eine etwas länger Maske zum ausfüllen angezeigt. Hier erkläre ich euch einmal für was die einzelnen Felder gebraucht werden.<br><br>

<b>Designname</b> - Hier könnt ihr euch ein Namen für die Farbvariante überlegen. Es ist komplett frei was ihr dort angebt, es sollten nur keine Leerzeichen oder Sonderzeichen benutzt werden. Der Name taucht nirgends sichtbar für User im Forum auf. Nur im ACP wird er angezeigt und später in der Datenbank gespeichert, wenn ein User sich ein Farb- und Headervariante aussucht.<br><br>

<b>Theme-ID</b> - Hier werden all eure bisherigen erstellten Themes (Designs) angezeigt. Wählt das entsprechende Theme aus, für welches die Farb-/Variante sein soll. <i>Hinweis:</i> Ich hab die Abfrage der Themes so geschrieben, dass wenn das Design schon bei einem Light/Dark Design oder Design mit Aktzentfarben eingetragen ist nicht nochmal ausgewählt werden kann. Also nicht wundern, wenn dort weniger angezeigt wird.<br><br>

<b>Headergrafik & erste/zweite Aktzentfarbe</b> - Diese drei Felder werden für die Auswahl später im User-CP benötigt. Es soll dabei helfen den Usern einen kleinen Eindruck zugeben, wie das Design aussieht. Welche Farben dominieren in dem Design und wie sieht der Header aus.<br><br>

<b>Pfad zu den Bildern</b> - Wir alle kennen die Variable $theme['imgdir']. Hinter dieser Variable ist der Pfad zum Grafikordner versteckt, welchen wir bei den Theme-Eigenschaften angegeben können. Da es vorkommen kann, dass man für eine neue Farb- und Headervariante auch einen anderen Grafikordner ansprechen will, zum Beispiel für Default Avatare oder Kategoriebanner, kann man in diesem Feld für jede Variante einen eigenen Pfad angeben. Wenn das Feld freibleibt, dann wird der Pfad genommen, welcher in den Eigenschaften von dem Design angegeben wurde.<br><br>

<b>Standardmode</b> - Hier wählt man aus, welches Helligkeitsmodus man als Standard haben möchte. Das ist gerade wichtig, wenn man ein Light- und Darkmode für die Variante anbieten möchte. Denn jeder User und jeder Gast, außer es ist was anders eingestellt, wird diesen Modus als erstes sehen. <br><br>

<b>Lightmode & Darkmode</b> - Hier werden endlich die Farben definiert für die Variante. Wenn ihr beide Felder ausfüllt habt ihr für euer Design automatisch eine dunkle und helle Version. Falls ihr nur eine helle (Light) Version anbieten wollt, dann müsst ihr nur das Lightmode Feld ausfüllen. Für eine dunkle (Dark) Version eben das Feld Darkmode. Es muss auf jeden Fall ein Feld ausgefüllt sein.<br><br>

Nachdem ihr all eure Versionen gespeichert habt, werden diese erst nach Themen-ID und dann nach dem Namen sortiert. Ihr könnt die Farb-/Headervorschau sehen, welche Helligkeitsversionen es gibt, die Standard ist Unterstrichen) und wie viele Nutzer welche Variante nutzen.<br>
Rechts neben den Namen seht ihr blaue Pfeile. Diese kennen wir schon von den Themes und auch hier haben sie genau die selbe Funktion. Über diese Pfeile kann man eine Standardvariante auswählen. Diese wird Usern und auch Gästen als erstes angezeigt, wenn sie das entsprechende Design unten im Stylechanger auswählen. Solltet ihr mal vergessen eine Variante als Standard zu setzen, dann fängt der Code auch das ab. Dann wird einfach die erste Variante, welche für dieses Design erstellt wurde verwendet.

# Designs mit einen Light-/Darkmodus

# Designs mit individuellen Design-Akzentfarben

# Vorraussetzungen
Eine ganz klare Vorraussetzung für dieses Plugin sind Designs mit Custom properties (CSS-Variablen). Es müssen nicht zwangsläufig all eure Designs so erstellt worden sein, aber die Designs, welche ihr über das Plugin steuern möchtet. Für Custom properties gibt es im Netz etliche Tutorials. Ich habe euch das Tutorial von White_Rabbit (Tom) einmal mitgebracht - (<a href="https://epic.quodvide.de/showthread.php?tid=124">EPIC</a> || <a href="https://storming-gates.de/showthread.php?tid=1012727">SG</a>)<br>
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
Damit man nicht jedes mal die User-CP Seite aufrufen muss und die Helligkeitsvariante vom Design zu wechseln habe ich noch ein Button programmiert, welcher global einsetzbar ist. Standardmäßig wird die Variable für den Button im Footer Template hinter den Stylechanger gelegt. Da die Variable aber global ist könnt ihr diese über all einfügen. Ihr solltet nur beachten, dass Gäste und User den beide sehen können. Bei den Usern wird mit einem Klick auf den Button die Spalte in der users Datenbank entsprechend geupdatet. Bei Gästen läuft der Switch über Cookies. Durch die verschiedene Speicherung gibt es auch zwei Templates für die Buttons - einmal <b>designconfigurator_switcher_button_guest</b> und <b>designconfigurator_switcher_button_member</b>. Ihr könnt die Buttons so coden wie ihr sie braucht und möchtet. <br><b>Wichtig</b> ist nur, dass ihr bestimmte Element nicht weglasst.<br><br>
<b>designconfigurator_switcher_button_guest</b>: <br>onclick="dark_mode_{$design_option}()"<br><br>
<b>designconfigurator_switcher_button_member</b>: <br>usercp.php?action=designconfigurator&indexdimm={$activedimm} (das ist der Link, den ihr braucht zum Updaten der Datenbank)

# Disclaimer
Der Javascript-Code für den Gäste Dark-/Lightbutton auf dem Index stammt aus diesem Tutorial (<a href="https://storming-gates.de/showthread.php?tid=1012199">SG Thema</a> || <a href="https://epic.quodvide.de/showthread.php?tid=74">EPIC Thema</a>) und wurde nur entsprechend angepasst.<br>
Die Darstellung der Auswahl der Farb-/Headervarianten habe ich mir vom Storming Gates abgeschaut. 

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

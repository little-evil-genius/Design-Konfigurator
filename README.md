# Design Konfigurator
Mit diesem Plugin lassen sich über das AdminCP verschiedene Designoptionen per Custom properties (CSS-Variablen) festlegen. Unter anderem Light-/Darkmodes, Designs mit Aktzentfarben, welche die User individuell anpassen können und verschiedene Farb-/Headervarianten. Die User können im User-CP dann ihre Wunschvariante selbst festlegen.

# Designs mit verschiedene Farb- und Headervarianten
Wer kennt es nicht, man hat einen Haufen Header aber keine Lust oder Ideen für ein komplett neues Design? Sowieso mag man das aktuelle Design vom Code her sehr? Also dupliziert man das Design und tauscht die Grafiken und Farben aus? Mit dem Design Konfigurator muss man keine Designs mehr duplizieren, sondern kann im ACP ganz einfach eine neue Farb-/Headervariante für ein bestimmtes Design erstellen. Wichtig dafür ist, dass die Farben und alles was sich je nach Farb-/Headervariante verändern soll, mit sogenannten Custom properties (CSS-Variablen) definiert wurden, denn für jede neue Farb-/Headervariante definieren wir neue Werte für diese Variabeln.<br><br>

<b>Neue Farb- und Headervariante hinzufügen:</b><br>
Wenn ihr eine neue Variante hinzufügen wollt, wird euch eine etwas länger Maske zum ausfüllen angezeigt. Hier erkläre ich euch einmal für was die einzelnen Felder gebraucht werden.<br><br>

<b>Designname</b> - Hier könnt ihr euch einen Namen für die Farbvariante überlegen. Es ist komplett frei, was ihr dort angebt, es sollten nur keine Leerzeichen oder Sonderzeichen benutzt werden. Der Name taucht nirgends sichtbar für User im Forum auf. Nur im ACP wird er angezeigt und später in der Datenbank gespeichert, wenn ein User sich eine Farb- und Headervariante aussucht.<br><br>

<b>Theme-ID</b> - Hier werden all eure bisherigen erstellten Themes (Designs) angezeigt. Wählt das entsprechende Theme aus, für welches die Farb-/Variante sein soll. <i>Hinweis:</i> Ich habe die Abfrage der Themes so geschrieben, dass wenn das Design schon bei einem Light/Dark Design oder Design mit Aktzentfarben eingetragen ist, nicht nochmal ausgewählt werden kann. Also nicht wundern, wenn dort weniger angezeigt wird.<br><br>

<b>Headergrafik & erste/zweite Aktzentfarbe</b> - Diese drei Felder werden für die Auswahl später im User-CP benötigt. Es soll dabei helfen, den Usern einen kleinen Eindruck zu geben, wie das Design aussieht. Welche Farben in dem Design dominieren und wie der Header aussieht. Als Headergrafik muss nicht zwangsläufig ein klassischer Header angegeben werden. Sollte das Design mit einem Hintergrundbild arbeiten, dann kann man auch das angeben oder ihr macht einen Screenshot von dem Design und verlinkt dies. Es geht hier schlichtweg nur darum, eine Preview für die User im User-CP zu haben.<br><br>

<b>Pfad zu den Bildern</b> - Wir alle kennen die Variable $theme['imgdir']. Hinter dieser Variable ist der Pfad zum Grafikordner versteckt, welchen wir bei den Theme-Eigenschaften angegeben können. Da es vorkommen kann, dass man für eine neue Farb- und Headervariante auch einen anderen Grafikordner ansprechen will, zum Beispiel für Default Avatare oder Kategoriebanner, kann man in diesem Feld für jede Variante einen eigenen Pfad angeben. Wenn das Feld frei bleibt, dann wird der Pfad genommen, welcher in den Eigenschaften von dem Design angegeben wurde.<br><br>

<b>Standardmodus</b> - Hier wählt man aus, welchen Helligkeitsmodus man als Standard haben möchte. Das ist gerade wichtig, wenn man ein Light- und Darkmode für die Variante anbieten möchte. Denn jeder User und jeder Gast, außer es ist was anders eingestellt, wird diesen Modus sehen. <br><br>

<b>Zugelassene Benutzergruppen</b> - Diese Funktion kennen wir schon von den Theme-Einstellungen von MyBB und im Grunde ist es genau dieselbe Funktion. Wenn das Team eine neue Farb- und Headervariante erstmal in Ruhe testen will, kann es einfach die entsprechenden Gruppen aussuchen und dann wird diese Auswahloption auch nur Accounts in dieser Gruppe angezeigt. Dabei ist es egal, ob die Gruppe in der primären oder sekundären Gruppe angegeben ist.  <br><br>

<b>Lightmode & Darkmode</b> - Hier werden endlich die CSS-Variablen für die Variante definiert. Wenn ihr beide Felder ausfüklt, habt ihr für euer Design automatisch eine dunkle und helle Version. Falls ihr nur eine helle (Light) Version anbieten wollt, dann müsst ihr nur das Lightmode Feld ausfüllen. Für eine dunkle (Dark) Version eben das Feld Darkmode. Es muss auf jeden Fall ein Feld ausgefüllt sein.<br><br>

<b>Benachrichtigung für die User</b> - Bei einer Farb- und Headervariante wird an die Hauptaccounts ein Alert geschickt, mit dem Hinweis, dass es für Design X eine neue Farb- und Headervariante gibt. Das ist an sich praktisch, aber gerade bei Varianten, die noch ausgtestet werden müssen oder die für Designs sind, welche noch nicht freigeschaltet sind, eher unpraktisch, deswegen kann man für jede Variante selbst entscheiden, ob ein Alert geschickt werden soll oder nicht.<br><br>

Nachdem ihr all eure Versionen gespeichert habt, werden diese erst nach Themen-ID und dann nach dem Namen sortiert. Ihr könnt die Farb-/Headervorschau sehen, welche Helligkeitsversionen es gibt (die Standardversion ist Unterstrichen) und wie viele Nutzer welche Variante nutzen. Damit die Übersicht nicht zu voll wird, werden die Kombinationen ab 10 Stück auf mehrere Seiten durch eine Multipage gestückelt.<br>
Rechts neben den Namen seht ihr blaue Pfeile. Diese kennen wir schon von den Themes und auch hier haben sie genau dieselbe Funktion. Über diese Pfeile kann man eine Standardvariante auswählen. Diese wird Usern und auch Gästen als erstes angezeigt, wenn sie das entsprechende Design unten im Stylechanger auswählen. Solltet ihr mal vergessen eine Variante als Standard zu setzen, dann fängt der Code auch das ab. Dann wird einfach die erste Variante, welche für dieses Design erstellt wurde, verwendet.

# Designs mit einen Light-/Darkmodus
Manchmal hätte man gerne das gleiche Design einfach nochmal in dunkel oder in hell. Auch hier könnte man das Design einfach duplizieren und einmal in hell und einmal in dunkel anbieten, allerdings stopft das auf Dauer den Stylechanger ja auch etwas voll. Mit der zweiten Design Option von diesem Plugin kann man auch recht einfach einen Light- und Darkmode für ein Design erstellen. Auch hierfür benötigt man wieder Custom properties (CSS-Variablen). Das hinzufügen eines Light-/Darkmode ist nicht ganz so umfangreich wie beim hinzufügen einer neuen Header-/Farbvariante.<br><br>

<b>Theme-ID</b> - Hier werden all eure bisherigen erstellten Themes (Designs) angezeigt. Wählt das entsprechende Theme aus, für welches ihr ein Light-/Darkmode einrichten möchtet. <i>Hinweis:</i> Ich habe die Abfrage der Themes so geschrieben, dass wenn das Design schon bei einem Design mit verschieden Farb-/Headervarianten oder Design mit Aktzentfarben eingetragen ist, nicht nochmal ausgewählt werden kann. Auch wenn es schon zu einem Design einen Light-/Darkmode gibt, kann es nicht noch einmal ausgewählt werden. Also nicht wundern, wenn dort weniger angezeigt wird.<br><br>

<b>Standardmode</b> - Hier wählt man aus, welchen Helligkeitsmodus man als Standard haben möchte. Denn jeder User und jeder Gast, außer es ist was anders eingestellt, wird diesen Modus sehen. <br><br>

<b>Lightmode & Darkmode</b> - Hier werden die CSS-Variablen definiert. Hier sollten auf jeden Fall beide Felder ausgefüllt werden, da es sonst keinen Sinn macht für dieses Design diese Switchversion anzubieten.<br><br>

In der Übersicht der Light-/Darkdesigns wird der Designname, die Theme-ID und der Standardmode angezeigt. 

# Designs mit individuellen Design-Akzentfarben
Es gibt Designs, die arbeiten mit einer oder vielleicht auch einer zweiten Highlightfarbe. Als Team findet man die Farbe, welche man gewählt hat besonders toll, aber vielleicht mag nicht  jeder User die Farbe und hätte gerne lieber statt dem Rot ein Grün. Mit den persönlichen individuellen Design-Akzentfarben kann man das in Zukunft für seine User einrichten und jeder kann sich das Design so einfärben wie er oder sie es gerne hätte.<br>
Genau wie bei den anderen zwei Designoptionen wird auch hier wieder mit den Custom properties (CSS-Variablen) gearbeitet und dieses Mal mit ganz besonderen. Und zwar mit --accent[Nummer]. Hinter dieser Variable verstecken sich die Farben, welche das Team als Standard bzw die User sich als individuelle Farben festgelegt haben. Das Plugin ist so geschrieben, dass man nicht nur eine, sondern mehrere Akzentfarben festlegen kann. In der Reihenfolge wie die Hex-Codes angegeben sind, sind auch die Nummern dann für die CSS-Variablen.<br><br>
<b>Ein Beispiel:</b><br>
Als Standardfarben ist folgendes eingetragen: "#6B2C75, #33752C, #6e8d92". Dann muss man für #6B2C75 --accent1 nutzen, für #33752C --accent2 und für #6e8d92 --accent3.<br><br>

<b>Theme-ID</b> - Hier werden all eure bisherigen erstellten Themes (Designs) angezeigt. Wählt das entsprechende Theme aus, für welches ihr ein Design mit Akzentfarbe(n) einrichten möchtet. <i>Hinweis:</i> Ich habe die Abfrage der Themes so geschrieben, dass wenn das Design schon bei einem Design mit verschiedenen Farb-/Headervarianten oder Light-/Darkmodus eingetragen ist, nicht nochmal ausgewählt werden kann. Auch wenn es schon ein Design mit Akzentfarbe(n) gibt, kann es nicht noch einmal ausgewählt werden. Also nicht wundern, wenn dort weniger angezeigt wird.<br><br>

<b>Standardmode</b> - Hier wählt man aus, welchen Helligkeitsmodus man als Standard haben möchte. Denn jeder User und jeder Gast, außer es ist was anders eingestellt, wird diesen Modus sehen. <br><br>

<b>Standard-Aktzentfarbe(n)</b> - Hier werden die Standard-Aktzentfarben für das Design eingetragen. Ich bin von Hex-Codes ausgegangen, aber es sollte auch mit anderen Angaben funktionieren. Wenn ihr mehrere Farben eintragen wollt, dann ist es wichtig, dass zwischen den Farben ein , und Leerzeichen stehen! Sonst wird das später nicht ordentlich in der PHP gesplittet. Wenn ihr normale 7-stellige Hex-Codes für die Farben verwendet, dann können bis zu 55 Farben eingetragen werden. Auch wenn das etwas viel wäre :D <br><br>

<b>Lightmode & Darkmode</b> - Hier werden die CSS-Variablen für das Design definiert. Wenn ihr beide Felder ausgefüllt habt, habt ihr für euer Design automatisch eine dunkle und helle Version. Falls ihr nur eine helle (Light) Version anbieten wollt, dann müsst ihr nur das Lightmode Feld ausfüllen. Für eine dunkle (Dark) Version eben das Feld Darkmode. Es muss auf jeden Fall ein Feld ausgefüllt sein. Die Variable(n) --accent[Nummer] sollen hier <b>nicht</b> nochmal definiert werden!<br><br>

Auf der Übersichtsseite der Designs mit Akzentfarbe(n) wird der Designname und die entsprechende Theme-ID angezeigt. Genauso, welche Helligkeitsmöglichkeiten es für das Design gibt und im Falle, dass es einen Light und Darkmodus gibt, wird der Standardmodus unterstrichen. Auch werden die Standard-Akzentfarbe(n) aufgelistet.<br><br>

Ein gutes Live Beispiel für Light-/Darkdesigns sind die zwei Designs "Postcards from Scotland" & "Auld Lang Syne" vom <a href="https://shadesoflife.de/index.php">Shades of Life</a>. Beide Designs sind gleich aufgebaut nur einmal in dunklen Farben und einmal in hellen Farben. Beide Designs könnte man übers ACP einpflegen und im Stylechanger wäre das Design nur einmal aufgelistet.

# Design Konfiguration im User-CP
Die Verwaltung vom Design habe ich ins User-CP verlegt. Dort findet ihr erstmal den Platz, um euren Usern zu erklären, wie das ganze Prinzip der persönlichen Anpassungen funktioniert. Den habe ich nicht vorgeschrieben, da es so individuell geschrieben werden kann, dass es jedes Team selbst schreiben muss. :D<br>
Im User-CP wird je nach Design und Einstellung im ACP der entsprechende Bereich angezeigt. Es gibt drei Teile: Light- und Darkmodus, Header-/Farbkombination und individuelle Anpassungen. Wenn ein Light-/Darkmodus für das Design angeboten wird, werden zwei Buttons angezeigt. Ein Button für den Lightmodus und einen für den Darkmodus. Der Button für den schon aktiven Modus ist ausgeblichen und kann nicht angeklickt werden. So wissen die User direkt, welchen Modus sie haben. Sollte es für das Design keine Möglichkeit geben zwischen den zwei Modi zu wählen, steht dort ein Default Text mit der Angabe, welcher Modus vom Team angeboten wird.<br>
Der Bereich Header-/Farbkombination ist nur aktiv in Designs, welche verschiedene Kombinationen anbieten. Dort werden solche Vorschaubilder wie schon im ACP angezeigt. Zusätzlich gibt es die Information, ob es für diese Variante nur einen Light- oder Darkmodus gibt oder sogar ein Switch möglich ist. Genau wie bei den Buttons für Light und Dark, wird auch hier die aktive Farb-/Headerkombination verblasst dargestellt und kann nicht ausgewählt werden.<br>
Bei den individuellen Anpassungen werden bei Designs mit individuellen Aktzentfarben das oder die Input-Feld(er) für die Aktzentfarben angezeigt. Die User bekommen einmal die Standardfarbe angezeigt und wenn sie eine andere Farbe für sich eingetragen haben, auch diese.

# Dark-/Lightbutton auf dem Index
Damit man nicht jedes mal die User-CP Seite aufrufen muss um die Helligkeitsvariante vom Design zu wechseln, habe ich noch einen Button programmiert, welcher global einsetzbar ist. Dieser Button wird aber nur angezeigt, wenn das Design oder die Farb-/Headervariante auch die Möglichkeit hat zu switchen. Standardmäßig wird die Variable für den Button im Footer-Template hinter den Stylechanger gelegt. Da die Variable aber global ist, könnt ihr diese überall einfügen. Ihr solltet nur beachten, dass Gäste und User den Button sehen können. Bei den Usern wird mit einem Klick auf den Button die Spalte in der users Datenbank entsprechend geupdatet. Bei Gästen läuft der Switch über Cookies. Durch die verschiedene Speicherung gibt es auch zwei Templates für die Buttons - einmal <b>designconfigurator_switcher_button_guest</b> und <b>designconfigurator_switcher_button_member</b>. Ihr könnt die Buttons so coden wie ihr sie braucht und möchtet. <br><b>Wichtig</b> ist nur, dass ihr bestimmte Element nicht weglasst.<br><br>
<b>designconfigurator_switcher_button_guest</b>: <br>onclick="dark_mode_{$design_option}()"<br><br>
<b>designconfigurator_switcher_button_member</b>: <br>usercp.php?action=designconfigurator&indexdimm={$activedimm} (das ist der Link, den ihr zum Updaten der Datenbank braucht)

# Voraussetzungen
Eine ganz klare Voraussetzung für dieses Plugin sind Designs mit Custom properties (CSS-Variablen). Es müssen nicht zwangsläufig all eure Designs so erstellt worden sein, aber die Designs, welche ihr über das Plugin steuern möchtet. Für Custom properties gibt es im Netz etliche Tutorials und Anleitungen. Ich habe euch das Tutorial von White_Rabbit (Tom) einmal mitgebracht <a href="https://epic.quodvide.de/showthread.php?tid=124">EPIC</a> || <a href="https://storming-gates.de/showthread.php?tid=1012727">SG</a><br>
<br>
Damit die Admins im ACP die neue Seite sehen und verwalten können, müssen die Administrator-Berechtigungen angepasst werden. Dafür geht ihr im ACP auf den Reiter Benutzer & Gruppen und klickt links im Menü Administrator-Berechtigungen an. Dort geht ihr auf den Reiter Benutzergruppen-Berechtigungen und klickt bei der entsprechenden Gruppe auf Optionen und dann auf Berechtigungen ändern. Dort wählt ihr den Reiter Templates & Style aus und stellt bei "Kann den Inhalt des Design Konfigurators verwalten?" auf ja. So kann diese Gruppe nun auf den Design Konfigurator zugreifen.

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
Wird automatisch in jedes bestehende und neue Design hinzugefügt. Man sollte es einfach einmal abspeichern, bevor man dies im Board mit der Untersuchungsfunktion bearbeiten will, da es dann passieren kann, dass das CSS für dieses Plugin in einen anderen Stylesheet gerutscht ist, obwohl es im ACP richtig ist.

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
- euerforum.de/admin/index.php?module=style-designconfigurator&action=edit_accentcolor&did=X<br><br>
<b>USER-CP</b>
- euerforum.de/usercp.php?action=designconfigurator

# Disclaimer
Der Javascript-Code für den Gäste Light-/Darkbutton auf dem Index stammt aus diesem Tutorial (<a href="https://storming-gates.de/showthread.php?tid=1012199">SG Thema</a> || <a href="https://epic.quodvide.de/showthread.php?tid=74">EPIC Thema</a>) und wurde nur entsprechend angepasst.<br>
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

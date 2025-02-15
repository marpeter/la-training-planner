const Disziplinen = [
    { name: "Ausdauer", img: ""},
    { name: "Hochsprung", img: "assets/Hochsprung.png"},
    { name: "Hochsprung ohne Anlage", img: "assets/Hochsprung.png"},
    { name: "Koordination", img: "assets/Koordination.png"},
    { name: "Schnelligkeit", img: "assets/Lauf.png"},
    { name: "Schnelllaufen", img: "assets/Lauf.png"},
    { name: "Stabweitsprung", img: "assets/Weitsprung.png"},
    { name: "Staffellauf", img: "assets/Lauf.png"},
    { name: "Staffellauf Hürde", img: "assets/Huerdenlauf.png"},
    { name: "Überlaufen", img: "assets/Hochsprung.png"},
    { name: "Weitsprung (mit Grube)", img: "assets/Weitsprung.png"},
    { name: "Weitsprung (ohne Grube)", img: "assets/Weitsprung.png"},
    { name: "Wurf", img: "assets/Wurf.png"},
];

const Übungen = {
  AchterInKurvenLaufen:  { name: "8er in Kurven laufen", material: "Hütchen", duration: "10", repeat: "", details: ["5 Sprünge hoch auf Sitzsteine"]},
  AnhängerAbhängerStaffel: { name: "Anhänger - Abhänger Staffel", material: "Seile", duration: "10", repeat: "1 mal", details: [] },
  AnhängerAbhängerStaffelHürde:{ name: "Anhänger + Abhängerstaffel", material: "Hürden, Hütchen, Seile", duration: "10", repeat: "2-3 Runden", details: [] },
  Auslaufen: { name: "Auslaufen", material: "", duration: "5", repeat: "2 Runden", details: []},
  AutofahrenKurz: { name: "Autofahren mit Gängen", material: "", duration: "5", repeat: "2-3 Runden", details: ["Selbst mitlaufen"] },
  AutofahrenLang: { name: "Autofahren mit Gängen", material: "Hütchen", duration: "10", repeat: "2-3 Runden", details: [] },
  BällePrellen: { name: "Bälle prellen", material: "Bälle", duration: "5", repeat: "2-3 Runden", details: [] },
  BällePrellenRennen: { name: "Mit Bällen prellen und rennen", material: "Bälle", duration: "5", repeat: "2-3 Runden", details: [] },
  BallInReifenDopsen: { name: "Ball in Reifen dopsen lassen", material: "Bälle, Reifen", duration: "15", repeat: "", details: ["Zwei Kinder stehen sich immer gegenüber. Immer wenn ein Ball daneben geht machen beide Kinder 3 Liegestützen"]},
  BandKnotenHochspringen: { name: "Band schräg knoten und hochspringen lassen", material: "Band", duration: "10", repeat: "", details: []},
  BandSchrägHochspringen: { name: "Band schräg nach unten und hochspringen in Kurve", material: "Band, Hütchen (?)", duration: "10", repeat: "", details: []},
  BeidbeinigeHürdensprünge: { name: "Beidbeinige Hürdensprünge", material: "Hohe Hürden", duration: "10", repeat: "", details: ["Die wartenden Kinder machen Standwagen"]},
  BeiWortRennen: { name: "Geschichte und immer bei Wörtern wird gerannt", material: "Hütchen", duration: "10", repeat: "", details: []},
  Biathlon: { name: "Biathlon", material: "Bälle, Hütchen", duration: "10", repeat: "1 mal", details: [] },
  Brennball: { name: "Brennball", material: "Hütchen, Bälle", duration: "10", repeat: "1 mal", details: [] },
  ChinesischeMauer: { name: "Chinesische Mauer", material: "Hütchen", duration: "10", repeat: "", details: [] },
  CrosslaufAufRasen : { name: "Crosslauf auf Rasen", material: "Hürden, Hütchen", duration: "10", repeat: "2-3 Runden", details: [] },
  CrosslaufAufRasenIntervalle : { name: "Crosslauf auf Rasen mit Intervallbereichen", material: "Hürden, Hütchen", duration: "10", repeat: "1 mal", details: [] },
  DurchReifenHüpfen: { name: "Durch Reifen hüpfen", material: "Reifen", duration: "10", repeat: "", details: [] },
  EinlaufenBällePrellen: { name: "Einlaufen und Ball prellen", material: "Bälle", duration: "10", repeat: "2-3 Runden", details: [] },
  EinlaufenMitSprungseil: { name: "Einlaufen mit Sprungseil", material: "Sprungseil", duration: "10", repeat: "2-3 Runden", details: [] },
  FormenAblaufen: { name: "Formen ablaufen", material: "", duration: "10", repeat: "je nach Rundengröße", details: [] },
  FormenAblaufenAufRasen: { name: "Formen ablaufen auf Rasen", material: "Hütchen", duration: "10", repeat: "2-3 Runden", details: [] },
  HochsprungAnAnlage: { name: "Hochsprung an Anlage mit Hütchen als Absperrung, ggf. im Schersprung", material: "Hütchen", duration: "10", repeat: "", details: ["5 Sprünge hoch auf Sitzsteine"]},
  HüpfenderTausendfüßler: { name: "Der hüpfende Tausendfüßler", material: "Hütchen", duration: "10", repeat: "", details: []},
  Hürdenbahn: { name: "Hürdenbahn", material: "Hürden, Medibälle", duration: "10", repeat: "", details: ["Die, die warten heben Medibälle vom Boden auf und strecken sie nach oben"]},
  HürdenbahnEinfach: { name: "Hürdenbahn", material: "Hürden", duration: "10", repeat: "", details: []},
  HürdenbahnMitSchwungbein: { name: "Hürdenbahn mit Schwungbeintraining", material: "Bananenkisten, bunte Bälle", duration: "10", repeat: "", details: []},
  HürdenbahnMitStaffel: { name: "Hürdenbahn und Staffel", material: "Bloxx, Hürden", duration: "10", repeat: "", details: [] },
  HürdenlaufenAnHand: { name: "Hürdenlaufen an der Hand", material: "Bananenkisten, Hütchen", duration: "10", repeat: "", details: ["Kinder laufen an der Hand über die Hürden"]},
  HütchenAbwerfen: { name: "Abwerfen von Hütchen auf dem Boden", material: "Hütchen, Bälle", duration: "15", repeat: "", details: [] },
  Klammerlauf_01: { name: "Klammerlauf", material: "Klammern", duration: "10", repeat: "1 mal", details: [] },
  Klammerlauf_02: { name: "Klammerlauf", material: "Klammern", duration: "10", repeat: "2-3 Runden", details: ["Wir verteilen uns auf der Bahn, damit jeder mehr Klammern bekommt"] },
  Klammerlauf_03: { name: "Klammerlauf mit Intervallbereichen", material: "Klammern", duration: "10", repeat: "1 mal", details: [] },
  KoordinationsLeiter: { name: "Koordinationsleiter", material: "Koordinationsleiter", duration: "10", repeat: "", details: ["Die wartenden Kinder machen Standwagen"]},
  LäuferGegenWerfer: { name: "Läufer gegen Werfer im Kreis", material: "Ball, Hütchen", duration: "10", repeat: "", details: [] },
  LaufABC_01: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Froschsprünge", "Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]},
  LaufABC_02: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Seitgalopp", "Seitkreuzschritte", "Schlagläufe", "Rückwärtslauf", "Steigerung"]},
  LaufABC_03: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Schlagläufe",
              "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Steps", "Steigerung"]},
  LaufABC_04: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]},
  LaufABC_05:  { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Steigerung"]},
  LaufABC_06: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitgalopp", "Steigerung"]},
  LaufABC_07: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Schlagläufe", "Anversen", "Skippings", "Knieheberlauf", "Steps", "B Steps", "Steigerung"]},
  LaufABC_08: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Seitgalopp", "Steigerung"]},
  LaufABC_09: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Schlagläufe", "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Steps", "Steigerung"]},
  LaufABC_10: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Schlagläufe", "Steps", "Steigerung"]},
  LaufABC_11: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hampelmannsprünge", "Grätschsprünge", "seitliche Sprünge", "Einbeinsprünge", "Sprung mit Anziehen der Knie", "Einbeinsprünge mit Beinwechsel"]},
  LaufABC_12: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Froschsprünge", "Hopserlauf über Hütchen", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]},
  LaufABC_13: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf über Hütchen", "Skippings über Hütchen", "Seitkreuzschritte", "Steigerung"]},
  LaufABC_14: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheber mit hohen Armen", "Skippings", "Steps", "Seitgalopp mit Schwungarmen", "Steigerung"]},
  LaufABC_15: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Schlagläufe", "Anversen", "Skippings mit hohen Armen", "Knieheber mit hohen Armen", "Steps", "B Steps", "Schubkarre", "Steigerung"]},
  LaufABC_16: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Schlagläufe", "Anversen", "Knieheber mit hohen Armen", "Skippings mit hohen Armen", "Steps", "Seitkreuzschritte", "Steigerung"]},
  LaufABC_17: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Schlagläufe", "Anversen", "Knieheber mit hohen Armen", "Skippings mit hohen Armen", "Steps", "Seitkreuzschritte", "Schubkarre", "Steigerung"]},
  LaufABC_18: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Schlagläufe", "Anversen", "Knieheber", "Skippings", "Steps", "B Steps", "Schubkarre", "Steigerung"]},
  LaufABC_19: { name: "Lauf ABC",
    material: "Hütchen", duration: "10", repeat: " - ",
    details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Schubkarre", "Steigerung"]},
  LaufenZuMusik: { name: "Verschiedene Laufstile zur Musik", material: "Musik", duration: "10", repeat: "", details: ["Vorwärts", "Rückwärts", "Seitlich", "Krabbeln", "Hüpfen"] },
  MedibälleHinHerWerfen: { name: "Medibälle hin und her werfen", material: "Medibälle", duration: "10", repeat: "", details: [] },
  MitSeilFangen: { name: "Mit Seil fangen spielen", material: "Hütchen, Seil", duration: "10", repeat: "", details: [] },
  Nummernwettläufe: { name: "Nummernwettläufe", material: "Bananenkisten, Hütchen", duration: "10", repeat: "", details: ["Immer eine Nummer wird aufgerufen und darf laufen"] },
  Pendelstaffel: { name: "Pendelstaffel", material: "Bloxx, Hütchen", duration: "10", repeat: "", details: [] },
  PendelstaffelMitBloxxHürden: { name: "Pendelstaffel mit Bloxx + Hürden", material: "Bloxx, Hürden", duration: "10", repeat: "", details: [] },
  PendelstaffelMitHürden: { name: "Pendelstaffel mit Hürden", material: "Hürden, Hütchen", duration: "10", repeat: "", details: [] },
  Reaktionsstart_01: { name: "Reaktionsstart aus verschiedenen Lagen mit Worten", material: "Hütchen", duration: "10", repeat: "", details: []},
  Reaktionsstart_02: { name: "Reaktionsstart mit Bällen, die von hinten kommen", material: "Bälle, Hütchen", duration: "10", repeat: "", details: ["Die, die warten, machen 10 Kniebeugen"]},
  Reaktionsstart_03: { name: "Reaktionsstart aus verschiedenen Lagen mit Worten, mit hohen Armen sprinten", material: "Hütchen", duration: "10", repeat: "", details: []},
  ReihenfolgeErarbeiten: { name: "Reihenfolge im Reifen erarbeiten", material: "Reifen", duration: "10", repeat: "", details: []},
  Rundenstaffel: { name: "Rundenstaffel", material: "Staffelhölzer", duration: "15", repeat: "1 mal", details: []},
  SauDurchsDorf: { name: "Sau durchs Dorf", material: "Medibälle, Tennisbälle", duration: "10", repeat: "", details: []},
  SauDurchsDorfLang: { name: "Sau durchs Dorf", material: "Hütchen, Bälle, Medibälle", duration: "15", repeat: "", details: []},
  Schattenlaufen: { name: "Schattenlaufen", material: "", duration: "10", repeat: "", details: [] },
  SchereSteinPapier: { name: "Schere-Stein-Papier", material: "", duration: "10", repeat: "je nach Rundengröße", details: ["Der Gewinner rennt eine kleine Bahn, der Verlierer eine große Bahn"] },
  SchereSteinPapierRennen: { name: "Schere, Stein, Papier und Rennen", material: "Hütchen, Spielkarten", duration: "10", repeat: "", details: ["Verlierer macht 3 Situps"]},
  SechsTageRennen: { name: "6 Tage Rennen", material: "Hütchen", duration: "10", repeat: "1 mal", details: [] },
  SeilDannRennen: { name: "Seil und dann losrennen", material: "Hütchen, Spielkarten (?)", duration: "10", repeat: "", details: ["Verlierer macht 3 Situps"]},
  SeileRausziehen: { name: "Seile rausziehen", material: "Hütchen, Seile", duration: "10", repeat: "", details: ["Kinder ziehen sich die zusammengelegte Seile aus der Hose hinten raus"]},
  Seilspringen: { name: "Seilspringen", material: "Sprungseile", duration: "10", repeat: "", details: []},
  SeilspringenLangesSeil: { name: "Seilspringen langes Seil", material: "Langes Seil", duration: "10", repeat: "", details: [] },
  SeilspringenUndRunden: { name: "Seilspringen und Runden laufen", material: "Sprungseile", duration: "5", repeat: "2-3 Runden", details: [] },
  SpringenAmReifen: { name: "Springen am Reifen und Koordinationsleiter", material: "Reifen, Koordinationsleiter", duration: "10", repeat: "", details: [] },
  StabweitsprungMitKisten: { name: "Stabweitsprung in Sprunggrube mit Bananenkisten", material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "", details: ["Sprünge die Treppenstufen hoch &rarr; jedes Kind nach dem Sprung auf dem Rückweg rechts", "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"] },
  StabweitsprungOhneKisten: { name: "Stabweitsprung in Sprunggrube ohne Bananenkisten", material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "", details: ["Sprünge die Treppenstufen hoch &rarr; jedes Kind nach dem Sprung auf dem Rückweg rechts", "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"] },
  StaffelMitEinholen: { name: "Staffel mit Einholen", material: "Hürden, Hütchen", duration: "10", repeat: "1 mal", details: ["Großes Feld"] },
  Staffelrennen: { name: "Staffelrennen", material: "Hütchen", duration: "10", repeat: "", details: [] },
  StaffelrennenMitHürden: { name: "Staffelrennen mit Hürden", material: "Hürden, Hütchen", duration: "10", repeat: "", details: [] },
  StaffelrennenMitKarten: { name: "Staffelrennen mit Karten holen", material: "Hütchen, Spielkarten", duration: "10", repeat: "",details: []},
  StaffelrennenMitTransport: { name: "Staffelrennen und Sachen hin und her tragen", material: "Boxen, Sachen zum Transport", duration: "10", repeat: "", details: [] },
  StartAusVerschPositionen: { name: "Start aus verschiedenen Positionen", material: "Hütchen", duration: "10", repeat: "", details: ["Die, die warten, machen 3 Liegestützen"]},
  StarWars: { name: "Star Wars", material: "Hütchen", duration: "10", repeat: "", details: [] },
  SteigesprüngeAufBahn: { name: "Steigesprünge auf der Bahn", material: "Bananenkisten, Hütchen", duration: "10", repeat: "", details: [] },
  SteigesprüngeÜberBahn: { name: "Steigesprünge über die Bahn", material: "Bananenkisten", duration: "10", repeat: "", details: [] },
  TheorieStaffelübergabe: { name: "Theorie Staffelübergabe", material: "Staffelhölzer", duration: "10", repeat: "", details: [] },
  Transportlauf_01: { name: "Transportlauf", material: "Tennisbälle, Bananenkisten", duration: "5", repeat: "2-3 Runden", details: [] },
  Transportlauf_02: { name: "Transportlauf", material: "Hütchen, etwas zu transportieren", duration: "10", repeat: "1 mal", details: [] },
  Transportlauf_03: { name: "Transportlauf", material: "Klammern", duration: "10", repeat: "1 mal", details: [] },
  TransportSprint: { name: "Transportsprint", material: "Hütchen, etwas zum Transportieren", duration: "10", repeat: "", details: [] },
  TransportSprintLang: { name: "Transportsprint", material: "Hütchen, etwas zum Transportieren", duration: "15", repeat: "", details: [] },
  TransportSprintArme: { name: "Transportsprint mit Armen seitlich halten", material: "Hütchen, etwas zum Transportieren", duration: "10", repeat: "", details: [] },
  TransportSprintHürde: { name: "Transportsprint mit Hürden", material: "Hütchen, etwas zum Transportieren", duration: "10", repeat: "", details: [] },
  TurnenAufHochsprungMatte: { name: "Turnen auf der Hochsprungmatte", material: "Hochsprungmatte", duration: "15", repeat: "Rollen, Räder", details: [] },
  TurnenAufMatte: { name: "Turnen auf der Matte", material: "Hütchen", duration: "10", repeat: "Purzelbäume, Räder", details: ["5 Sprünge hoch auf Sitzsteine"] },
  ÜberBloxxLaufen: { name: "Über Bloxx laufen", material: "Bloxx, Hütchen", duration: "10", repeat: "", details: [] },
  ÜberholstaffelKurz: { name: "Überholstaffel", material: "", duration: "5", repeat: "2-3 Runden", details: [] },
  ÜberholstaffelLang: { name: "Überholstaffel", material: "", duration: "10", repeat: "2-3 Runden", details: [] },
  ÜberholstaffelOhneWdh: { name: "Überholstaffel", material: "Hütchen (?)", duration: "10", repeat: "", details: [] },
  WeitsprungInGrube: { name: "Weitsprung in Sprunggrube ohne Bananenkisten",
    material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
    details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
              "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]},
  WeitsprungInGrubeMitBananenKiste: { name: "Weitsprung in Sprunggrube mit Bananenkisten",
    material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
    details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
              "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]},
  WerfenMitAnlauf_01: { name: "Werfen mit Anlauf",
    material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
    details: ["3 Liegestützen nach jedem Wurf"]},
  WerfenMitAnlauf_02: { name: "Werfen mit Anlauf",
    material: "Bälle u.A., Maßband, Hütchen", duration: "15", repeat: "",
    details: []},
  WerfenMitAnlauf_03: { name: "Werfen mit Anlauf, ggf. auch stoßen",
    material: "Bälle, Maßband, Hütchen, Medibälle", duration: "15", repeat: "",
    details: ["3 Liegestützen nach jedem Wurf"]},
  WerfenMitAnlauf_04:  { name: "Werfen mit Anlauf",
    material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
    details: ["Medibälle, die durch die Beine wie eine 8 geführt werden müssen (5 Durchgänge)"]},
  WerfenMitAnlauf_05: { name: "Werfen mit Anlauf",
    material: "Alle werfbaren Gegenstände, Hütchen. Maßband", duration: "10", repeat: "",
    details: ["3 Liegesützen nach jedem Wurf"]},
  WerfenMitAnlauf_06: { name: "Werfen mit Anlauf oder stoßen",
    material: "Alle werfbaren Gegenstände, Hütchen. Maßband", duration: "10", repeat: "",
    details: ["3 Liegesützen nach jedem Wurf"]},
  WerfenOhneAnlauf_01: { name: "Werfen ohne Anlauf",
    material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
    details: ["3 Liegestützen nach jedem Wurf"]},
  WerfenOhneAnlauf_02: { name: "Werfen ohne Anlauf",
    material: "Bälle u.A., Maßband, Hütchen", duration: "15", repeat: "",
    details: []},
  WerfenOhneAnlauf_03: { name: "Werfen ohne Anlauf, ggf. auch stoßen",
    material: "Bälle, Maßband, Hütchen, Medibälle", duration: "15", repeat: "",
    details: ["3 Liegestützen nach jedem Wurf"]},
  WerfenOhneAnlauf_04: { name: "Werfen ohne Anlauf",
    material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
    details: ["Medibälle, die durch die Beine wie eine 8 geführt werden müssen (5 Durchgänge)"]},
  WerfenOhneAnlauf_05:  { name: "Werfen ohne Anlauf",
    material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
    details: ["3 Liegesützen nach jedem Wurf"]},
  WerfenOhneAnlauf_06: { name: "Werfen ohne Anlauf oder stoßen",
    material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
    details: ["3 Liegesützen nach jedem Wurf"]},  
  WerTriffRollendenBall: { name: "Wer trifft den rollenden Ball", material: "Bälle, großer Ball", duration: "15", repeat: "", details: ["Zwei Kinder stehen sich immer gegenüber. Immer wenn ein Ball daneben geht machen beide Kinder 3 Liegestützen"]},
  Zeitschätzlauf: { name: "Zeitschätzlauf", material: "Hütchen", duration: "10", repeat: "1 mal", details: [] },
  ZweiRundenRollbrett: { name: "2 Runden mit Rollbrett",
    material: "Rollbretter", duration: "10", repeat: "1 mal",
    details: ["Immer ein Kind sitzt drauf und das andere zieht"] },
};

const TrainingsPlaene = [
    { id: 1, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"],
      warmup: [ Übungen.SeilspringenUndRunden,   Übungen.LaufABC_01],
      mainex: [ Übungen.SeilspringenLangesSeil,  Übungen.Seilspringen, Übungen.WeitsprungInGrubeMitBananenKiste, Übungen.WeitsprungInGrube],
      ending: [ Übungen.AnhängerAbhängerStaffel, Übungen.Auslaufen]},
    { id: 2, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"],
      warmup: [ Übungen.SeilspringenUndRunden,   Übungen.LaufABC_01],
      mainex: [ Übungen.SeilspringenLangesSeil,  Übungen.Seilspringen, Übungen.WeitsprungInGrubeMitBananenKiste, Übungen.WeitsprungInGrube],
      ending: [ Übungen.SechsTageRennen,         Übungen.Auslaufen]},
    { id: 3, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [ Übungen.ÜberholstaffelKurz,      Übungen.LaufABC_02],
      mainex: [ Übungen.SpringenAmReifen,        Übungen.WerfenOhneAnlauf_01, Übungen.WerfenMitAnlauf_01],
      ending: [ Übungen.Zeitschätzlauf,          Übungen.Auslaufen]},
    { id: 4, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [ Übungen.Transportlauf_01,        Übungen.LaufABC_02],
      mainex: [ Übungen.SteigesprüngeAufBahn,    Übungen.WerfenOhneAnlauf_01, Übungen.WerfenMitAnlauf_01],
      ending: [ Übungen.Biathlon,                Übungen.Auslaufen]},
    { id: 5, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [ Übungen.AutofahrenKurz,          Übungen.LaufABC_03],
      mainex: [ Übungen.ÜberBloxxLaufen,         Übungen.WerfenOhneAnlauf_02, Übungen.WerfenMitAnlauf_02],
      ending: [ Übungen.Transportlauf_02,        Übungen.Auslaufen]},
    { id: 6, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [ Übungen.ÜberholstaffelKurz,      Übungen.LaufABC_03],
      mainex: [ Übungen.DurchReifenHüpfen,       Übungen.WerfenOhneAnlauf_03, Übungen.WerfenMitAnlauf_03],
      ending: [ Übungen.Transportlauf_02,        Übungen.Auslaufen]},
    { id: 7, disciplines: ["Ausdauer", "Wurf"],
      warmup: [ Übungen.BällePrellen,            Übungen.LaufABC_02],
      mainex: [ Übungen.Pendelstaffel,           Übungen.SauDurchsDorf, Übungen.WerfenOhneAnlauf_04, Übungen.WerfenMitAnlauf_04],
      ending: [ Übungen.Biathlon,                Übungen.Auslaufen]},
    { id: 8, disciplines: ["Ausdauer", "Hochsprung"],
      warmup: [ Übungen.FormenAblaufenAufRasen,  Übungen.LaufABC_04],
      mainex: [ Übungen.LäuferGegenWerfer,       Übungen.BandKnotenHochspringen, Übungen.AchterInKurvenLaufen, Übungen.HochsprungAnAnlage],
      ending: [ Übungen.Klammerlauf_01,          Übungen.Auslaufen]},
    { id: 9, disciplines: ["Ausdauer", "Hochsprung"],
      warmup: [ Übungen.AutofahrenLang,          Übungen.LaufABC_04],
      mainex: [ Übungen.Staffelrennen,           Übungen.BandSchrägHochspringen, Übungen.AchterInKurvenLaufen, Übungen.HochsprungAnAnlage],
      ending: [ Übungen.Transportlauf_03,        Übungen.Auslaufen]},
    { id: 10, disciplines: ["Schnelllaufen", "Hochsprung ohne Anlage"],
      warmup: [ Übungen.EinlaufenMitSprungseil,  Übungen.LaufABC_05],
      mainex: [ Übungen.StarWars,                Übungen.Reaktionsstart_01, Übungen.Pendelstaffel, Übungen.BandSchrägHochspringen],
      ending: [ Übungen.ZweiRundenRollbrett,     Übungen.Auslaufen]},
     { id: 11, disciplines: ["Schnelllaufen", "Hochsprung ohne Anlage"],
      warmup: [ Übungen.Klammerlauf_02,          Übungen.LaufABC_06],
      mainex: [ Übungen.ChinesischeMauer,        Übungen.Reaktionsstart_02, Übungen.StaffelrennenMitKarten, Übungen.BandSchrägHochspringen],
      ending: [ Übungen.SchereSteinPapier,       Übungen.Auslaufen]},
     { id: 12, disciplines: ["Hochsprung ohne Anlage", "Wurf"],
      warmup: [ Übungen.EinlaufenMitSprungseil,  Übungen.LaufABC_05],
      mainex: [ Übungen.MitSeilFangen,           Übungen.BandSchrägHochspringen, Übungen.WerfenOhneAnlauf_05, Übungen.WerfenMitAnlauf_05],
      ending: [ Übungen.ZweiRundenRollbrett,     Übungen.Auslaufen] },
    { id: 13, disciplines: ["Hochsprung ohne Anlage", "Wurf"],
      warmup: [ Übungen.EinlaufenBällePrellen,   Übungen.LaufABC_07],
      mainex: [ Übungen.StaffelrennenMitTransport, Übungen.BandSchrägHochspringen, Übungen.WerfenOhneAnlauf_06, Übungen.WerfenMitAnlauf_06],
      ending: [ Übungen.Biathlon,                Übungen.Auslaufen] },
    { id: 14, disciplines: ["Ausdauer", "Schnelllaufen"],
      warmup: [ Übungen.AutofahrenLang,          Übungen.LaufABC_07],
      mainex: [ Übungen.StarWars,                Übungen.StartAusVerschPositionen, Übungen.SchereSteinPapierRennen, Übungen.StaffelrennenMitKarten],
      ending: [ Übungen.SchereSteinPapier,       Übungen.Auslaufen] },
    { id: 15, disciplines: ["Ausdauer", "Schnelllaufen"],
      warmup: [ Übungen.Zeitschätzlauf,          Übungen.LaufABC_01],
      mainex: [ Übungen.StarWars,                Übungen.BeiWortRennen, Übungen.SeilDannRennen, Übungen.StaffelrennenMitHürden],
      ending: [ Übungen.FormenAblaufen,          Übungen.Auslaufen] },
    { id: 16, disciplines: ["Schnelllaufen", "Überlaufen"],
      warmup: [ Übungen.ÜberholstaffelLang,      Übungen.LaufABC_05],
      mainex: [ Übungen.StarWars,                Übungen.Hürdenbahn, Übungen.HürdenbahnMitSchwungbein, Übungen.ÜberholstaffelOhneWdh],
      ending: [ Übungen.ZweiRundenRollbrett,     Übungen.Auslaufen] },
    { id: 17, disciplines: ["Schnelllaufen", "Überlaufen"],
      warmup: [ Übungen.FormenAblaufen,          Übungen.LaufABC_08],
      mainex: [ Übungen.Staffelrennen,           Übungen.HürdenbahnEinfach, Übungen.HürdenbahnMitSchwungbein, Übungen.StaffelrennenMitHürden],
      ending: [ Übungen.SechsTageRennen,         Übungen.Auslaufen] },
    { id: 18, disciplines: ["Schnelligkeit", "Wurf"],
      warmup: [ Übungen.AutofahrenKurz,          Übungen.LaufABC_09],
      mainex: [ Übungen.Pendelstaffel,           Übungen.BallInReifenDopsen, Übungen.HütchenAbwerfen],
      ending: [ Übungen.Brennball,               Übungen.Auslaufen] },
    { id: 19, disciplines: ["Schnelligkeit", "Wurf"],
      warmup: [ Übungen.ZweiRundenRollbrett,     Übungen.LaufABC_07],
      mainex: [ Übungen.TransportSprint,         Übungen.WerTriffRollendenBall, Übungen.SauDurchsDorfLang, Übungen.WerfenOhneAnlauf_05],
      ending: [ Übungen.Biathlon,                Übungen.Auslaufen] },
    { id: 20, disciplines: ["Ausdauer", "Überlaufen"],
      warmup: [ Übungen.CrosslaufAufRasen,       Übungen.LaufABC_05],
      mainex: [ Übungen.PendelstaffelMitBloxxHürden, Übungen.KoordinationsLeiter, Übungen.HürdenbahnMitSchwungbein, Übungen.ÜberholstaffelOhneWdh],
      ending: [ Übungen.Klammerlauf_02,          Übungen.Auslaufen] },
    { id: 21, disciplines: ["Ausdauer", "Überlaufen"],
      warmup: [ Übungen.AnhängerAbhängerStaffelHürde, Übungen.LaufABC_05],
      mainex: [ Übungen.HürdenbahnMitStaffel,    Übungen.ReihenfolgeErarbeiten, Übungen.HürdenbahnMitSchwungbein, Übungen.MedibälleHinHerWerfen],
      ending: [ Übungen.CrosslaufAufRasen,       Übungen.Auslaufen] },
    { id: 22, disciplines: ["Überlaufen", "Wurf"],
      warmup: [ Übungen.BällePrellenRennen,      Übungen.LaufABC_10],
      mainex: [ Übungen.PendelstaffelMitHürden,  Übungen.HürdenbahnMitSchwungbein, Übungen.WerfenOhneAnlauf_06, Übungen.WerfenMitAnlauf_06],
      ending: [ Übungen.Biathlon,                Übungen.Auslaufen] },
    { id: 23, disciplines: ["Überlaufen", "Wurf"],
      warmup: [ Übungen.ZweiRundenRollbrett,     Übungen.LaufABC_07],
      mainex: [ Übungen.TransportSprintHürde,    Übungen.HürdenbahnMitSchwungbein, Übungen.SauDurchsDorfLang, Übungen.WerfenOhneAnlauf_05],
      ending: [ Übungen.AnhängerAbhängerStaffelHürde, Übungen.Auslaufen] },
    { id: 24, disciplines: ["Ausdauer", "Koordination"],
      warmup: [ Übungen.AnhängerAbhängerStaffel, Übungen.LaufABC_05],
      mainex: [ Übungen.SeileRausziehen,         Übungen.KoordinationsLeiter, Übungen.HürdenlaufenAnHand, Übungen.Schattenlaufen],
      ending: [ Übungen.CrosslaufAufRasen,       Übungen.Auslaufen] },
    { id: 25, disciplines: ["Ausdauer", "Koordination"],
      warmup: [ Übungen.LaufenZuMusik,           Übungen.LaufABC_11],
      mainex: [ Übungen.SeileRausziehen,         Übungen.KoordinationsLeiter, Übungen.Nummernwettläufe, Übungen.Schattenlaufen],
      ending: [ Übungen.StaffelMitEinholen,      Übungen.Auslaufen] },
    { id: 26, disciplines: ["Ausdauer", "Stabweitsprung"],
      warmup: [ Übungen.SeilspringenUndRunden,   Übungen.LaufABC_12],
      mainex: [ Übungen.SeilspringenLangesSeil,  Übungen.Seilspringen, Übungen.StabweitsprungMitKisten, Übungen.StabweitsprungOhneKisten ],
      ending: [ Übungen.AnhängerAbhängerStaffel, Übungen.Auslaufen] },
    { id: 27, disciplines: ["Ausdauer", "Stabweitsprung"],
      warmup: [ Übungen.SeilspringenUndRunden,   Übungen.LaufABC_12],
      mainex: [ Übungen.HüpfenderTausendfüßler,  Übungen.SteigesprüngeÜberBahn, Übungen.StabweitsprungMitKisten, Übungen.StabweitsprungOhneKisten ],
      ending: [ Übungen.SechsTageRennen,         Übungen.Auslaufen] },
    { id: 28, disciplines: ["Ausdauer", "Hochsprung"],
      warmup: [ Übungen.FormenAblaufenAufRasen,  Übungen.LaufABC_04],
      mainex: [ Übungen.LäuferGegenWerfer,       Übungen.BandKnotenHochspringen, Übungen.AchterInKurvenLaufen, Übungen.HochsprungAnAnlage ],
      ending: [ Übungen.Klammerlauf_03,          Übungen.Auslaufen] },
    { id: 29, disciplines: ["Ausdauer", "Hochsprung"],
      warmup: [ Übungen.FormenAblaufenAufRasen,  Übungen.LaufABC_04],
      mainex: [ Übungen.LäuferGegenWerfer,       Übungen.BandKnotenHochspringen, Übungen.TurnenAufMatte, Übungen.HochsprungAnAnlage ],
      ending: [ Übungen.Klammerlauf_03,          Übungen.Auslaufen] },
    { id: 30, disciplines: ["Ausdauer", "Kondition"],
      warmup: [ Übungen.AnhängerAbhängerStaffelHürde, Übungen.LaufABC_13],
      mainex: [ Übungen.SeileRausziehen,         Übungen.KoordinationsLeiter, Übungen.HürdenlaufenAnHand, Übungen.Schattenlaufen ],
      ending: [ Übungen.CrosslaufAufRasenIntervalle, Übungen.Auslaufen] },
    { id: 31, disciplines: ["Hochsprung", "Schnelllaufen"],
      warmup: [ Übungen.EinlaufenMitSprungseil,  Übungen.LaufABC_14],
      mainex: [ Übungen.StarWars,                Übungen.Reaktionsstart_03, Übungen.Pendelstaffel, Übungen.BandSchrägHochspringen ],
      ending: [ Übungen.ZweiRundenRollbrett,     Übungen.Auslaufen] },
    { id: 32, disciplines: ["Schnelligkeit", "Wurf"],
      warmup: [ Übungen.ZweiRundenRollbrett,     Übungen.LaufABC_15],
      mainex: [ Übungen.TransportSprintArme,     Übungen.WerTriffRollendenBall, Übungen.SauDurchsDorfLang, Übungen.WerfenOhneAnlauf_05 ],
      ending: [ Übungen.Biathlon,                Übungen.Auslaufen] },
    { id: 33, disciplines: ["Ausdauer", "Überlaufen"],
      warmup: [ Übungen.CrosslaufAufRasen,       Übungen.LaufABC_16],
      mainex: [ Übungen.PendelstaffelMitHürden,  Übungen.KoordinationsLeiter, Übungen.HürdenbahnMitSchwungbein, Übungen.ÜberholstaffelOhneWdh ],
      ending: [ Übungen.Klammerlauf_03,          Übungen.Auslaufen] },
    { id: 34, disciplines: ["Ausdauer", "Überlaufen"],
      warmup: [ Übungen.CrosslaufAufRasen,       Übungen.LaufABC_17],
      mainex: [ Übungen.PendelstaffelMitHürden,  Übungen.BeidbeinigeHürdensprünge, Übungen.HürdenbahnMitSchwungbein, Übungen.ÜberholstaffelOhneWdh ],
      ending: [ Übungen.Klammerlauf_03,          Übungen.Auslaufen] },
    { id: 35, disciplines: ["Schnelligkeit", "Wurf"],
      warmup: [ Übungen.ZweiRundenRollbrett,     Übungen.LaufABC_18],
      mainex: [ Übungen.TransportSprint,         Übungen.TurnenAufHochsprungMatte, Übungen.SauDurchsDorfLang, Übungen.WerfenOhneAnlauf_05 ],
      ending: [ Übungen.Biathlon,                Übungen.Auslaufen] },
    { id: 36, disciplines: ["Staffellauf"],
      warmup: [ Übungen.ZweiRundenRollbrett,     Übungen.LaufABC_18],
      mainex: [ Übungen.TransportSprintLang,     Übungen.TurnenAufHochsprungMatte, Übungen.TheorieStaffelübergabe ],
      ending: [ Übungen.Rundenstaffel,           Übungen.Auslaufen] },
    { id: 37, disciplines: ["Staffellauf"],
      warmup: [ Übungen.CrosslaufAufRasenIntervalle, Übungen.LaufABC_19],
      mainex: [ Übungen.PendelstaffelMitHürden,  Übungen.KoordinationsLeiter, Übungen.HürdenbahnMitSchwungbein ],
      ending: [ Übungen.TheorieStaffelübergabe,  Übungen.Rundenstaffel, Übungen.Auslaufen] },
];
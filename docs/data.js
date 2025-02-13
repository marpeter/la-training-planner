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

const TrainingsPlaene = [
    { id: 1, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"],
      warmup: [
        { name: "Seilspringen und Runden laufen",
          material: "Sprungseile", duration: "5", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Froschsprünge", "Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
        }],
      mainex: [
        { name: "Seilspringen langes Seil",
          material: "Langes Seil", duration: "10", repeat: "",
          details: [] },
        { name: "Seilspringen",
          material: "Sprungseile", duration: "10", repeat: "",
          details: []},
        { name: "Weitsprung in Sprunggrube mit Bananenkisten",
          material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
          details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
                    "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]},
        { name: "Weitsprung in Sprunggrube ohne Bananenkisten",
          material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
          details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
                    "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]
        }],
      ending: [
        { name: "Anhänger - Abhänger Staffel",
          material: "Seil", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}] 
    },

    { id: 2, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"],
      warmup: [
        { name: "Seilspringen und Runden laufen",
          material: "Sprungseile", duration: "5", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Froschsprünge", "Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
        }],
      mainex: [
        { name: "Seilspringen langes Seil",
          material: "Langes Seil", duration: "10", repeat: "",
          details: [] },
        { name: "Seilspringen",
          material: "Sprungseile", duration: "10", repeat: "",
          details: []},
        { name: "Weitsprung in Sprunggrube mit Bananenkisten",
          material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
          details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
                    "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]},
        { name: "Weitsprung in Sprunggrube ohne Bananenkisten",
          material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
          details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
                    "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]
        }],
      ending: [
        { name: "6 Tage Rennen",
          material: "Hütchen", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]
    },

    { id: 3, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [
        { name: "Überholstaffel",
          material: "", duration: "5", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Seitgalopp", "Seitkreuzschritte", "Schlagläufe", "Rückwärtslauf", "Steigerung"]
        }],
      mainex: [
        { name: "Springen am Reifen und Koordinationsleiter",
          material: "Reifen, Koordinationsleiter", duration: "10", repeat: "",
          details: [] },
        { name: "Werfen ohne Anlauf",
          material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
          details: ["3 Liegestützen nach jedem Wurf"]},
        { name: "Werfen mit Anlauf",
          material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
          details: ["3 Liegestützen nach jedem Wurf"]
        }],
      ending: [
        { name: "Zeitschätzlauf",
          material: "Hütchen", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]
    },

    { id: 4, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [
        { name: "Transportlauf",
          material: "Tennisbälle, Bananenkisten", duration: "5", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Seitgalopp", "Seitkreuzschritte", "Schlagläufe", "Rückwärtslauf", "Steigerung"]
        }],
      mainex: [
        { name: "Steigesprünge auf der Bahn",
          material: "Bananenkisten, Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Werfen ohne Anlauf",
          material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
          details: ["3 Situps nach jedem Wurf"]},
        { name: "Werfen mit Anlauf",
          material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
          details: ["3 Situps nach jedem Wurf"]
        }],
      ending: [
        { name: "Biathlon",
          material: "Hütchen", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]
    },

    { id: 5, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [
        { name: "Autofahren mit Gängen",
          material: "", duration: "5", repeat: "2-3 Runden",
          details: ["Selbst mitlaufen"] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Schlagläufe",
                    "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Steps", "Steigerung"]
        }],
      mainex: [
        { name: "Über Bloxx laufen",
          material: "Bloxx, Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Werfen ohne Anlauf",
          material: "Bälle u.A., Maßband, Hütchen", duration: "15", repeat: "",
          details: []},
        { name: "Werfen mit Anlauf",
          material: "Bälle u.A., Maßband, Hütchen", duration: "15", repeat: "",
          details: []
        }],
      ending: [
        { name: "Transportlauf",
          material: "Hütchen, etwas zu transportieren", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]
    },

    { id: 6, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
      warmup: [
        { name: "Überholstaffel",
          material: "", duration: "5", repeat: "2-3 Runden",
          details: ["Selbst mitlaufen bei schwacher Gruppe"] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Schlagläufe",
                    "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Steps", "Steigerung"]
        }],
      mainex: [
        { name: "Durch Reifen hüpfen",
          material: "Reifen", duration: "10", repeat: "",
          details: [] },
        { name: "Werfen ohne Anlauf, ggf. auch stoßen",
          material: "Bälle, Maßband, Hütchen, Medibälle", duration: "15", repeat: "",
          details: ["3 Liegestützen nach jedem Wurf"]},
        { name: "Werfen mit Anlauf, ggf. auch stoßen",
          material: "Bälle, Maßband, Hütchen, Medibälle", duration: "15", repeat: "",
          details: ["3 Liegestützen nach jedem Wurf"]
        }],
      ending: [
        { name: "Transportlauf",
          material: "Hütchen, etwas zu transportieren", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]
    },

    { id: 7, disciplines: ["Ausdauer", "Wurf"],
      warmup: [
        { name: "Bälle prellen",
          material: "Bälle", duration: "5", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Seitgalopp", "Seitkreuzschritte", "Schlagläufe", "Rückwärtslauf", "Steigerung"]
        }],
      mainex: [
        { name: "Pendelstaffel",
          material: "Bloxx, Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Sau durchs Dorf",
          material: "Medibälle, Tennisbälle", duration: "10", repeat: "",
          details: []},
        { name: "Werfen ohne Anlauf",
          material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
          details: ["Medibälle, die durch die Beine wie eine 8 geführt werden müssen (5 Durchgänge)"]},
        { name: "Werfen mit Anlauf",
          material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
          details: ["Medibälle, die durch die Beine wie eine 8 geführt werden müssen (5 Durchgänge)"]
        }],
      ending: [
        { name: "Biathlon",
          material: "", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

    { id: 8, disciplines: ["Ausdauer", "Hochsprung"],
      warmup: [
        { name: "Formen ablaufen auf Rasen",
          material: "Hütchen", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
        }],
      mainex: [
        { name: "Läufer gegen Werfer im Kreis",
          material: "Ball, Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Band schräg knoten und hochspringen lassen",
          material: "Band", duration: "10", repeat: "",
          details: []},
        { name: "8er in Kurven laufen",
          material: "Hütchen", duration: "10", repeat: "",
          details: ["5 Sprünge hoch auf Sitzsteine"]},
        { name: "Hochsprung an Anlage mit Hütchen als Absperrung",
          material: "Hütchen", duration: "10", repeat: "",
          details: ["5 Sprünge hoch auf Sitzsteine"]
        }],
      ending: [
        { name: "Klammerlauf",
          material: "Klammern", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 9, disciplines: ["Ausdauer", "Hochsprung"],
      warmup: [
        { name: "Autofahren mit Gängen",
          material: "Hütchen", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
        }],
      mainex: [
        { name: "Staffelrennen",
          material: "Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Band schräg nach unten und hochspringen in Kurve",
          material: "Band", duration: "10", repeat: "",
          details: []},
        { name: "8er in Kurven laufen",
          material: "Hütchen", duration: "10", repeat: "",
          details: ["5 Sprünge hoch auf Sitzsteine"]},
        { name: "Hochsprung an Anlage mit Hütchen als Absperrung, ggf. im Schersprung",
          material: "Hütchen", duration: "10", repeat: "",
          details: ["5 Sprünge hoch auf Sitzsteine"]
        }],
      ending: [
        { name: "Transportlauf",
          material: "Klammern", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 10, disciplines: ["Schnelllaufen", "Hochsprung ohne Anlage"],
      warmup: [
        { name: "Einlaufen mit Sprungseil",
          material: "Sprungseil", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Steigerung"]
        }],
      mainex: [
        { name: "Star Wars",
          material: "Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Reaktionsstart aus verschiedenen Lagen mit Worten",
          material: "Hütchen", duration: "10", repeat: "",
          details: []},
        { name: "Pendelstaffel",
          material: "Hütchen", duration: "10", repeat: "",
          details: []},
        { name: "Band schräg nach unten und hochspringen in Kurve",
          material: "Band, Hütchen", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "2 Runden mit Rollbrett",
          material: "Rollbretter", duration: "10", repeat: "1 mal",
          details: ["Immer ein Kind sitzt drauf und das andere zieht"] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 11, disciplines: ["Schnelllaufen", "Hochsprung ohne Anlage"],
      warmup: [
        { name: "Klammerlauf",
          material: "Klammern", duration: "10", repeat: "2-3 Runden",
          details: ["Wir verteilen uns auf der Bahn, damit jeder mehr Klammern bekommt"] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitgalopp", "Steigerung"]
        }],
      mainex: [
        { name: "Chinesische Mauer",
          material: "Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Reaktionsstart mit Bällen, die von hinten kommen",
          material: "Bälle, Hütchen", duration: "10", repeat: "",
          details: ["Die, die warten, machen 10 Kniebeugen"]},
        { name: "Staffelrennen mit Karten holen",
          material: "Hütchen, Spielkarten", duration: "10", repeat: "",
          details: []},
        { name: "Band schräg nach unten und hochspringen in Kurve",
          material: "Band, Hütchen", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "Schere-Stein-Papier",
          material: "", duration: "10", repeat: "je nach Rundengröße",
          details: ["Der Gewinner rennt eine kleine Bahn, der Verlierer eine große Bahn"] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 12, disciplines: ["Hochsprung ohne Anlage", "Wurf"],
      warmup: [
        { name: "Einlaufen mit Sprungseil",
          material: "Sprungseil", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritt", "Steigerung"]
        }],
      mainex: [
        { name: "Mit Seil fangen spielen",
          material: "Hütchen, Seil", duration: "10", repeat: "",
          details: [] },
        { name: "Band schräg nach unten und hochspringen",
          material: "Band", duration: "10", repeat: "",
          details: []},
        { name: "Werfen ohne Anlauf",
          material: "Alle werfbaren Gegenstände, Hütchen. Maßband", duration: "10", repeat: "",
          details: ["3 Liegesützen nach jedem Wurf"]},
        { name: "Werfen mit Anlauf",
          material: "Alle werfbaren Gegenstände, Hütchen. Maßband", duration: "10", repeat: "",
          details: ["3 Liegesützen nach jedem Wurf"]
        }],
      ending: [
        { name: "2 Runden mit Rollbrett",
          material: "Rollbretter", duration: "10", repeat: "1 mal",
          details: ["Immer ein Kind sitzt drauf und das andere zieht"] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 13, disciplines: ["Hochsprung ohne Anlage", "Wurf"],
      warmup: [
        { name: "Einlaufen und Ball prellen",
          material: "Bälle", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Schlagläufe", "Anversen", "Skippings", "Knieheberlauf", "Steps", "B Steps", "Steigerung"]
        }],
      mainex: [
        { name: "Staffelrennen und Sachen hin und her tragen",
          material: "Boxen, Sachen zum Transport", duration: "10", repeat: "",
          details: [] },
        { name: "Band schräg nach unten und hochspringen",
          material: "Band", duration: "10", repeat: "",
          details: []},
        { name: "Werfen ohne Anlauf oder stoßen",
          material: "Alle werfbaren Gegenstände, Hütchen. Maßband", duration: "10", repeat: "",
          details: ["3 Liegesützen nach jedem Wurf"]},
        { name: "Werfen mit Anlauf oder stoßen",
          material: "Alle werfbaren Gegenstände, Hütchen. Maßband", duration: "10", repeat: "",
          details: ["3 Liegesützen nach jedem Wurf"]
        }],
      ending: [
        { name: "Biathlon",
          material: "Bälle, Hütchen", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 14, disciplines: ["Ausdauer", "Schnelllaufen"],
      warmup: [
        { name: "Autofahren mit Gängen",
          material: "", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Schlagläufe", "Anversen", "Skippings", "Knieheberlauf", "Steps", "B Steps", "Steigerung"]
        }],
      mainex: [
        { name: "Star Wars",
          material: "Bloxx", duration: "10", repeat: "",
          details: [] },
        { name: "Start aus verschiedenen Positionen",
          material: "Hütchen", duration: "10", repeat: "",
          details: ["Die, die warten, machen 3 Liegestützen"]},
        { name: "Schere, Stein, Papier und Rennen",
          material: "Hütchen, Spielkarten", duration: "10", repeat: "",
          details: ["Verlierer macht 3 Situps"]},
        { name: "Staffelrennen mit Karten",
          material: "Hütchen, Spielkarten", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "Schere, Stein, Papier",
          material: "", duration: "10", repeat: "je nach Rundengröße",
          details: ["Der Gewinner rennt eine kleine Bahn, der Verlierer eine große Bahn"] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 15, disciplines: ["Ausdauer", "Schnelllaufen"],
      warmup: [
        { name: "Zeitschätzlauf",
          material: "Hütchen", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Froschsprünge", "Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
        }],
      mainex: [
        { name: "Star Wars",
          material: "Bloxx", duration: "10", repeat: "",
          details: [] },
        { name: "Geschichte und immer bei Wörtern wird gerannt",
          material: "Hütchen", duration: "10", repeat: "",
          details: []},
        { name: "Seil und dann losrennen",
          material: "Hütchen, Spielkarten", duration: "10", repeat: "",
          details: ["Verlierer macht 3 Situps"]},
        { name: "Staffelrennen mit Hürden",
          material: "Hürden, Hütchen", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "Formen ablaufen",
          material: "", duration: "10", repeat: "je nach Rundengröße",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 16, disciplines: ["Schnelllaufen", "Überlaufen"],
      warmup: [
        { name: "Überholstaffel",
          material: "", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Steigerung"]
        }],
      mainex: [
        { name: "Star Wars",
          material: "Bloxx", duration: "10", repeat: "",
          details: [] },
        { name: "Hürdenbahn",
          material: "Hürden, Medibälle", duration: "10", repeat: "",
          details: ["Die, die warten heben Medibälle vom Boden auf und strecken sie nach oben"]},
        { name: "Hürdenbahn mit Schwungbeintraining",
          material: "Bananenkisten, bunte Bälle", duration: "10", repeat: "",
          details: []},
        { name: "Überholstaffel",
          material: "Hütchen", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "2 Runden mit Rollbrett",
          material: "Rollbretter", duration: "10", repeat: "1 mal",
          details: ["Immer ein Kind sitzt drauf und das andere zieht"] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 17, disciplines: ["Schnelllaufen", "Überlaufen"],
      warmup: [
        { name: "Formen ablaufen",
          material: "Hütchen", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Seitgalopp", "Steigerung"]
        }],
      mainex: [
        { name: "Staffelrennen",
          material: "Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Hürdenbahn",
          material: "Hürden", duration: "10", repeat: "",
          details: []},
        { name: "Hürdenbahn mit Schwungbeintraining",
          material: "Hütchen, Spielkarten", duration: "10", repeat: "",
          details: []},
        { name: "Staffelrennen mit Hürden",
          material: "Hürden, Hütchen", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "Sechs Tage Rennen",
          material: "Seil", duration: "10", repeat: "je nach Rundengröße",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 18, disciplines: ["Schnelligkeit", "Wurf"],
      warmup: [
        { name: "Autofahren mit Gängen",
          material: "", duration: "5", repeat: "2-3 Runden",
          details: ["Selbst mitlaufen"] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Schlagläufe", "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Steps", "Steigerung"]
        }],
      mainex: [
        { name: "Pendelstaffel",
          material: "Hütchen, Bloxx", duration: "10", repeat: "",
          details: [] },
        { name: "Ball in Reifen dopsen lassen",
          material: "Bälle, Reifen", duration: "15", repeat: "",
          details: ["Zwei Kinder stehen sich immer gegenüber. Immer wenn ein Ball daneben geht machen beide Kinder 3 Liegestützen"]},
        { name: "Abwerfen von Hütchen auf dem Boden",
          material: "Hütchen, Bälle", duration: "15", repeat: "",
          details: []
        }],
      ending: [
        { name: "Brennball",
          material: "Hütchen, Bälle", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 19, disciplines: ["Schnelligkeit", "Wurf"],
      warmup: [
        { name: "2 Runden mit Rollbrett",
          material: "Rollbretter", duration: "10", repeat: "1 mal",
          details: ["Immer ein Kind sitzt drauf und das andere zieht"] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Schlagläufe", "Anversen", "Skippings", "Knieheberlauf", "Steps", "B Steps", "Steigerung"]
        }],
      mainex: [
        { name: "Transportsprint",
          material: "Hütchen, etwas zum Transportieren", duration: "10", repeat: "",
          details: [] },
        { name: "Wer trifft den rollenden Ball",
          material: "Bälle, großer Ball", duration: "15", repeat: "",
          details: ["Zwei Kinder stehen sich immer gegenüber. Immer wenn ein Ball daneben geht machen beide Kinder 3 Liegestützen"]},
        { name: "Sau durchs Dorf",
          material: "Hütchen, Bälle, Medibälle", duration: "15", repeat: "",
          details: []},
        { name: "Werfen ohne Anlauf",
          material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
          details: ["3 Liegesützen nach jedem Wurf"]
        }],
      ending: [
        { name: "Biathlon",
          material: "Hütchen, Bälle", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 20, disciplines: ["Ausdauer", "Überlaufen"],
      warmup: [
        { name: "Crosslauf auf Rasen",
          material: "Hürden, Hütchen", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Steigerung"]
        }],
      mainex: [
        { name: "Pendelstaffel mit Bloxx + Hürden",
          material: "Bloxx, Hürden", duration: "10", repeat: "",
          details: [] },
        { name: "Koordinationsleiter",
          material: "Koordinationsleiter", duration: "10", repeat: "",
          details: ["Die wartenden Kinder machen Standwagen"]},
        { name: "Hürdenbahn mit Schwungbeintraining",
          material: "Bananenkisten, bunte Bälle", duration: "10", repeat: "",
          details: []},
        { name: "Überholstaffel",
          material: "Hütchen", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "Klammerlauf",
          material: "Klammern", duration: "10", repeat: "1 mal",
          details: ["Wir verteilen uns auf der Bahn, damit jeder mehr Klammern bekommt"] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 21, disciplines: ["Ausdauer", "Überlaufen"],
      warmup: [
        { name: "Anhänger + Abhängerstaffel",
          material: "Hürden, Hütchen, Seile", duration: "10", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Skippings", "Steps", "Seitkreuzschritte", "Steigerung"]
        }],
      mainex: [
        { name: "Hürdenbahn und Staffel",
          material: "Bloxx, Hürden", duration: "10", repeat: "",
          details: [] },
        { name: "Reihenfolge im Reifen erarbeiten",
          material: "Reifen", duration: "10", repeat: "",
          details: []},
        { name: "Hürdenbahn mit Schwungbeintraining",
          material: "Bananenkisten, bunte Bälle", duration: "10", repeat: "",
          details: []},
        { name: "Medibälle hin und her werfen",
          material: "Medibälle", duration: "10", repeat: "",
          details: []
        }],
      ending: [
        { name: "Crosslauf auf Rasen",
          material: "Hürden, Hütchen", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

     { id: 22, disciplines: ["Überlaufen", "Wurf"],
      warmup: [
        { name: "Mit Bällen prellen und rennen",
          material: "Bälle", duration: "5", repeat: "2-3 Runden",
          details: [] },
        { name: "Lauf ABC",
          material: "Hütchen", duration: "10", repeat: " - ",
          details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Schlagläufe", "Steps", "Steigerung"]
        }],
      mainex: [
        { name: "Pendelstaffel mit Hürden",
          material: "Hürden, Hütchen", duration: "10", repeat: "",
          details: [] },
        { name: "Hürdenbahn mit Schwungbeintraining",
          material: "Bananenkisten, bunte Bälle", duration: "10", repeat: "",
          details: []},
        { name: "Werfen ohne Anlauf oder stoßen",
          material: "Alle werfbaren Gegenständ, Hütchen, Maßband", duration: "10", repeat: "",
          details: ["3 Liegesützen nach jedem Wurf"]},
        { name: "Werfen mit Anlauf oder stoßen",
          material: "Alle werfbaren Gegenständ, Hütchen, Maßband", duration: "10", repeat: "",
          details: ["3 Liegesützen nach jedem Wurf"]
        }],
      ending: [
        { name: "Biathlon",
          material: "Bälle, Hütchen", duration: "10", repeat: "1 mal",
          details: [] },
        { name: "Auslaufen",
          material: "", duration: "5", repeat: "2 Runden",
          details: []}]    
     },

  ];
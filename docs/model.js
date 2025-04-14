import { loadObjectFromCSV } from "./data/utils.js";

let Disciplines = {};
let Exercises = {};
let  ExerciseWorksForDiscipline = [];
class TrainingPlan {
  constructor(disciplines) {
    this.id = 1;
    this.disciplines = disciplines;
    this.warmup = []; 
    this.mainex = [];
    this.ending = [];
  }

  duration() {
    return this.mainex.reduce( adder, this.ending.reduce( adder, this.warmup.reduce( adder, 0 ) ) );
  }

  static async loadData() {
    Disciplines = await loadObjectFromCSV('data/Disciplines.csv');
    Exercises = await loadObjectFromCSV('data/Exercises.csv',/;/);
    // console.log(JSON.stringify(Exercises));
    Exercises.Auslaufen = { id: "Auslaufen", name: "Auslaufen", warmup: false, runabc: false, mainex: false, ending: false, material: "", duration: "5", repeat: "2 Runden", details: []};

    ExerciseWorksForDiscipline = [
      { exercise: Exercises.AchterInKurvenLaufen,         disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},
      { exercise: Exercises.AnhängerAbhängerStaffel,      disciplines: ["Ausdauer"] },
      { exercise: Exercises.AnhängerAbhängerStaffelHürde, disciplines: ["Ausdauer", "Überlaufen"]},
      { exercise: Exercises.AutofahrenKurz,               disciplines: ["Ausdauer"]},
      { exercise: Exercises.AutofahrenLang,               disciplines: ["Ausdauer"]},
      { exercise: Exercises.BallInReifenDopsen,           disciplines: ["Wurf"]},
      { exercise: Exercises.BandKnotenHochspringen,       disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},
      { exercise: Exercises.BandSchrägHochspringen,       disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},
      { exercise: Exercises.BeiWortRennen,                disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      
      { exercise: Exercises.BeidbeinigeHürdensprünge,
        disciplines: ["Ausdauer", "Überlaufen"]},
      
      { exercise: Exercises.Biathlon,                     disciplines: ["Ausdauer"]},
      { exercise: Exercises.Brennball,                    disciplines: ["Schnelligkeit", "Schnelllaufen", "Wurf"]},
      { exercise: Exercises.BällePrellen,                 disciplines: ["Wurf"]},
      { exercise: Exercises.BällePrellenRennen,           disciplines: ["Wurf"]},
      { exercise: Exercises.BundesligaSprint,             disciplines: ["Schnelligkeit", "Schnelllaufen"] },
      { exercise: Exercises.ChinesischeMauer,             disciplines: ["Schnelligkeit", "Schnelllaufen"]},
    
      { exercise: Exercises.CrosslaufAufRasen,
        disciplines: ["Ausdauer", "Koordination", "Überlaufen"]},
      { exercise: Exercises.CrosslaufAufRasenIntervalle,
        disciplines: ["Ausdauer", "Koordination", "Staffellauf", "Überlaufen"]},
      
      { exercise: Exercises.DerFuchsGehtUm,               disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.Doppelbett,                   disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.DurchReifenHüpfen,            disciplines: ["WeitsprungOhneGrube", "WeitsprungMitGrube"]},
      { exercise: Exercises.EinlaufenBällePrellen,        disciplines: ["Wurf"]},
      
      { exercise: Exercises.EinlaufenMitSprungseil,
        disciplines: ["Hochsprung", "HochsprungOhneAnlage", "Schnelligkeit", "Schnelllaufen", "Wurf"]},
      
      { exercise: Exercises.FCRunde,                      disciplines: ["Ausdauer"]},
      { exercise: Exercises.FormenAblaufen,               disciplines: ["Ausdauer"]},
      { exercise: Exercises.FormenAblaufenAufRasen,       disciplines: ["Ausdauer"]},
      { exercise: Exercises.GeradeNachHintenFallenLassen, disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},
      { exercise: Exercises.HabichtUndHenne,              disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.HochsprungAnAnlage,           disciplines: ["Hochsprung"]},
      { exercise: Exercises.HüpfenderTausendfüßler,       disciplines: ["Stabweitsprung", "WeitsprungOhneGrube", "WeitsprungMitGrube"] },
      { exercise: Exercises.Hürdenbahn,                   disciplines: ["Überlaufen"]},
      { exercise: Exercises.HürdenbahnEinfach,            disciplines: ["Überlaufen"]},
      { exercise: Exercises.HürdenbahnMitSchwungbein,     disciplines: ["Überlaufen"]},
      { exercise: Exercises.HürdenbahnMitStaffel,         disciplines: ["Überlaufen"]},
      { exercise: Exercises.Hunderunde,                   disciplines: ["Ausdauer"]},
  
      { exercise: Exercises.HürdenlaufenAnHand,
        disciplines: ["Ausdauer", "Koordination"]},
    
      { exercise: Exercises.HütchenAbwerfen,              disciplines: ["Wurf"]},
      { exercise: Exercises.Klammerlauf_01,               disciplines: ["Ausdauer"]},
      { exercise: Exercises.Klammerlauf_02,               disciplines: ["Ausdauer"]},
      { exercise: Exercises.Klammerlauf_03,               disciplines: ["Ausdauer"]},
    
      { exercise: Exercises.KoordinationsLeiter,
        disciplines: ["Ausdauer", "Koordination", "Staffellauf", "Überlaufen"]},
      { exercise: Exercises.LaufABC_01,
        disciplines: ["Ausdauer", "Schnelllaufen", "WeitsprungMitGrube"] },
      { exercise: Exercises.LaufABC_02,
        disciplines: ["Ausdauer", "WeitsprungOhneGrube", "Wurf"]},
      { exercise: Exercises.LaufABC_03,
        disciplines: ["Ausdauer", "WeitsprungOhneGrube", "Wurf"]},
      { exercise: Exercises.LaufABC_04,
        disciplines: ["Ausdauer", "Hochsprung"]},
      { exercise: Exercises.LaufABC_05,
        disciplines: ["Ausdauer", "HochsprungOhneAnlage", "Koordination", "Schnelllaufen", "Überlaufen", "Wurf"]},
      { exercise: Exercises.LaufABC_06,
        disciplines: ["Schnelllaufen", "HochsprungOhneAnlage"]},
      { exercise: Exercises.LaufABC_07,
        disciplines: ["Ausdauer", "HochsprungOhneAnlage", "Schnelllaufen", "Überlaufen", "Wurf"]},
      { exercise: Exercises.LaufABC_08,
        disciplines: ["Schnelllaufen", "Überlaufen"]},
      { exercise: Exercises.LaufABC_09,
        disciplines: ["Schnelligkeit", "Wurf"]},
      { exercise: Exercises.LaufABC_10,
        disciplines: ["Überlaufen", "Wurf"]},
      { exercise: Exercises.LaufABC_11,
        disciplines: ["Ausdauer", "Koordination"]},
      { exercise: Exercises.LaufABC_12,
        disciplines: ["Ausdauer", "Stabweitsprung"]},
      { exercise: Exercises.LaufABC_13,
        disciplines: ["Ausdauer", "Koordination"]},
      { exercise: Exercises.LaufABC_14,
        disciplines: ["Hochsprung", "Schnelllaufen"]},
      { exercise: Exercises.LaufABC_15,
        disciplines: ["Schnelligkeit", "Wurf"]},
      { exercise: Exercises.LaufABC_16,
        disciplines: ["Ausdauer", "Überlaufen"]},
      { exercise: Exercises.LaufABC_17,
        disciplines: ["Ausdauer", "Überlaufen"]},
      { exercise: Exercises.LaufABC_18,
        disciplines: ["Schnelligkeit", "Staffellauf", "Wurf"]},
      { exercise: Exercises.LaufABC_19,
        disciplines: ["Staffellauf", "Überlaufen"]},
      { exercise: Exercises.LaufenZuMusik,
        disciplines: ["Ausdauer", "Koordination"]},
    
      { exercise: Exercises.LäuferGegenWerfer,            disciplines: ["Ausdauer"]},
      { exercise: Exercises.MattenbergHochkommen,         disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},
      { exercise: Exercises.MattenbergRückenAufkommen,    disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},

      { exercise: Exercises.MedibälleHinHerWerfen,
        disciplines: ["Ausdauer", "Überlaufen"]}, 
      { exercise: Exercises.MitSeilFangen,
        disciplines: ["HochsprungOhneAnlage", "Wurf"]},
    
      { exercise: Exercises.Nummernwettläufe,             disciplines: ["Ausdauer", "Koordination"]}, 
    
      { exercise: Exercises.Pendelstaffel,
        disciplines: ["Ausdauer", "Hochsprung", "HochsprungOhneAnlage", "Schnelligkeit", "Schnelllaufen", "Wurf"]},
      { exercise: Exercises.PendelstaffelMitBloxxHürden,
        disciplines: ["Ausdauer", "Überlaufen"]},
      { exercise: Exercises.PendelstaffelMitHürden,
        disciplines: ["Ausdauer", "Staffellauf", "Überlaufen", "Wurf"]},
    
      { exercise: Exercises.Reaktionsstart_01,            disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.Reaktionsstart_02,            disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.Reaktionsstart_03,            disciplines: ["Schnelligkeit", "Schnelllaufen"]}, 
      { exercise: Exercises.Reaktionsstart_04,            disciplines: ["Schnelligkeit", "Schnelllaufen"]}, 
      { exercise: Exercises.ReihenfolgeErarbeiten,        disciplines: ["WeitsprungMitGrube", "WeitsprungOhneGrube"]},
      { exercise: Exercises.ReiseNachJerusalemMitRingen,  disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.Rundenstaffel,                disciplines: ["Staffellauf"]},
      { exercise: Exercises.SauDurchsDorf,                disciplines: ["Wurf"]}, 
      { exercise: Exercises.SauDurchsDorfLang,            disciplines: ["Wurf"]},
    
      { exercise: Exercises.Schattenlaufen,
        disciplines: ["Ausdauer", "Koordination"]},
    
      { exercise: Exercises.SchereSteinPapier,            disciplines: ["Ausdauer"]},
      { exercise: Exercises.SchereSteinPapierRennen,      disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.SchersprungAufMatte,          disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},
      { exercise: Exercises.SechsTageRennen,              disciplines: ["Ausdauer"] },
      { exercise: Exercises.SeilDannRennen,               disciplines: ["Ausdauer", "Koordination"]},

      { exercise: Exercises.SeileRausziehen,
        disciplines: ["Ausdauer", "Koordination"]},
    
      { exercise: Exercises.Seilspringen,                 disciplines: ["Stabweitsprung", "WeitsprungMitGrube", "WeitsprungOhneGrube"] },
      { exercise: Exercises.SeilspringenLangesSeil,       disciplines: ["Stabweitsprung", "WeitsprungMitGrube", "WeitsprungOhneGrube"] },
      { exercise: Exercises.SeilspringenUndRunden,        disciplines: ["Stabweitsprung", "WeitsprungMitGrube", "WeitsprungOhneGrube"] },
      { exercise: Exercises.SpringenAmReifen,             disciplines: ["Stabweitsprung", "WeitsprungMitGrube", "WeitsprungOhneGrube"]}, 
      { exercise: Exercises.Stabweitsprung,               disciplines: ["Hochsprung", "HochsprungOhneAnlage", "Stabweitsprung", "WeitsprungMitGrube", "WeitsprungOhneGrube"]},
      
      { exercise: Exercises.StaffelMitEinholen,
        disciplines: ["Ausdauer", "Koordination"]},
    
      { exercise: Exercises.Staffelrennen,                disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.StaffelrennenMitHürden,       disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.StaffelrennenMitKarten,       disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.StaffelrennenMitTransport,    disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.StartAusVerschPositionen,     disciplines: ["Schnelligkeit", "Schnelllaufen"]},    
      { exercise: Exercises.StarWars,                     disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.StationenMitWerfen,           disciplines: ["Wurf"]},
      { exercise: Exercises.SteigesprüngeAufBahn,         disciplines: ["WeitsprungMitGrube", "WeitsprungOhneGrube"]},
      { exercise: Exercises.SteigesprüngeÜberBahn,        disciplines: ["WeitsprungMitGrube", "WeitsprungOhneGrube"] },
      { exercise: Exercises.TheorieStaffelübergabe,       disciplines: ["Staffellauf"]},
      { exercise: Exercises.TicTacToe,                    disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.Transportlauf_01,             disciplines: ["Ausdauer"]},
      { exercise: Exercises.Transportlauf_02,             disciplines: ["Ausdauer"]},
      { exercise: Exercises.Transportlauf_03,             disciplines: ["Ausdauer"]},
    
      { exercise: Exercises.TransportSprint,
        disciplines: ["Schnelligkeit", "Wurf"]},
      { exercise: Exercises.TransportSprintArme,
        disciplines: ["Schnelligkeit", "Wurf"]},
      { exercise: Exercises.TransportSprintHürde,
        disciplines: ["Überlaufen", "Wurf"]},
      { exercise: Exercises.TransportSprintLang,
        disciplines: ["Staffellauf"]},
      { exercise: Exercises.TurnenAufHochsprungMatte,
        disciplines: ["Schnelligkeit", "Staffellauf", "Wurf"]},
    
      { exercise: Exercises.TurnenAufMatte,               disciplines: ["Hochsprung", "HochsprungOhneAnlage"]},
      { exercise: Exercises.ÜberBananenkistenSpringenInBahn, disciplines: ["WeitsprungMitGrube", "WeitsprungOhneGrube"]},
      { exercise: Exercises.ÜberBloxxLaufen,              disciplines: ["WeitsprungOhneGrube", "WeitsprungMitGrube"]},
      { exercise: Exercises.ÜberholstaffelKurz,           disciplines: ["Ausdauer"]},
      { exercise: Exercises.ÜberholstaffelLang,           disciplines: ["Ausdauer"]},
      { exercise: Exercises.ÜberholstaffelOhneWdh,        disciplines: ["Ausdauer"]},
      { exercise: Exercises.VerschiedeneStartformen,      disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.VonInselZuInselSpringenMitReifen, disciplines: ["WeitsprungOhneGrube", "WeitsprungMitGrube"]},
      { exercise: Exercises.WeitsprungInGrube,            disciplines: ["WeitsprungMitGrube"] },
      { exercise: Exercises.WeitsprungInGrubeMitBananenKiste, disciplines: ["WeitsprungMitGrube"] },
      { exercise: Exercises.WerBautDenHöchstenTurm,       disciplines: ["Schnelligkeit", "Schnelllaufen"]},
      { exercise: Exercises.WerfenMitAnlauf,              disciplines: ["Wurf"]},
      { exercise: Exercises.WerfenOhneAnlauf,             disciplines: ["Wurf"]},
      { exercise: Exercises.WerTrifftDieSonne,            disciplines: ["Wurf"]},
      
      { exercise: Exercises.WerTrifftRollendenBall,
        disciplines: ["Schnelligkeit", "Wurf"]},

      { exercise: Exercises.Zeitschätzlauf,               disciplines: ["Ausdauer"]},
    
      { exercise: Exercises.ZweiRundenRollbrett,
        disciplines: ["Hochsprung", "HochsprungOhneAnlage", "Schnelligkeit", "Schnelllaufen", "Staffellauf", "Überlaufen", "Wurf"]},
    ];
  }

  static getAllDisciplines() {
    return Disciplines;
  }

  static generate(forDisciplineIds, targetDuration) {
    if (forDisciplineIds.length==0) return null;

    let suitableExercises = ExerciseWorksForDiscipline.filter(
      (exercise) => forDisciplineIds.filter( (selected) => exercise.disciplines.includes(selected)).length > 0
      ).map( (exercise) => exercise.exercise);

    console.log("Suitable: " + JSON.stringify(suitableExercises));

    let warmups = suitableExercises.filter( (exercise) => exercise.warmup );
    let runabcs = suitableExercises.filter( (exercise) => exercise.runabc );
    let mainexs = suitableExercises.filter( (exercise) => exercise.mainex );
    let endings = suitableExercises.filter( (exercise) => exercise.ending ); 
  
    // console.log(warmups.length + " Warm-ups: " + JSON.stringify(warmups));
    // console.log("RunABCs: " + JSON.stringify(runabcs));
    // console.log("Main exercises: " + JSON.stringify(mainexs));
    // console.log("Endings: " + JSON.stringify(endings));

    const forDisciplines = forDisciplineIds.map( (id) => Disciplines[id]);
    let plan = new TrainingPlan(forDisciplines);
    // the following algorithm is based purely on randomly picking exercises and does
    // not consider potential dependencies between exercises
    let attempts = 0;
    while((plan.duration()!=targetDuration) && (attempts++<10)) {
      console.log("Attempt " + attempts);
      // pick a random warmup and a random runabc
      plan.warmup = [ warmups.at(Math.floor(Math.random()*warmups.length)), runabcs.at(Math.floor(Math.random()*runabcs.length)) ];
      // pick a random ending and add the standard Auslaufen
      plan.ending = [ endings.at(Math.floor(Math.random()*endings.length)), Exercises.Auslaufen ];
      // pick main exercises until the target duration is reached or exceeded.
      plan.mainex = [];
      while(plan.duration()<=targetDuration-10) {
        let index = Math.floor(Math.random()*mainexs.length);
        let exerciseToAdd = mainexs.at(index);
        if(!plan.mainex.includes(exerciseToAdd)) {
          plan.mainex.push(exerciseToAdd);
        }
      }
    }
    if(attempts>=10) return undefined;
    return plan;
  };
};

function adder(total, exercise) { return total + parseInt(exercise.duration) };

export { TrainingPlan };
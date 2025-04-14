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
    // convert exercise details from strings into arrays
    Object.keys(Exercises).forEach( (exercise) =>  {
      Exercises[exercise].details = Exercises[exercise].details=="" ? [] : Exercises[exercise].details.split(':');
    } );
    Exercises.Auslaufen = { id: "Auslaufen", name: "Auslaufen", warmup: false, runabc: false, mainex: false, ending: false, material: "", duration: "5", repeat: "2 Runden", details: []};

    ExerciseWorksForDiscipline = [
      { exercise: Exercises.AchterInKurvenLaufen,         disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.AnhängerAbhängerStaffel,      disciplines: [Disciplines.Ausdauer] },
      { exercise: Exercises.AnhängerAbhängerStaffelHürde, disciplines: [Disciplines.Ausdauer, Disciplines.Überlaufen]},
      { exercise: Exercises.AutofahrenKurz,               disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.AutofahrenLang,               disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.BallInReifenDopsen,           disciplines: [Disciplines.Wurf]},
      { exercise: Exercises.BandKnotenHochspringen,       disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.BandSchrägHochspringen,       disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.BeiWortRennen,                disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      
      { exercise: Exercises.BeidbeinigeHürdensprünge,
        disciplines: [Disciplines.Ausdauer, Disciplines.Überlaufen]},
      
      { exercise: Exercises.Biathlon,                     disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.Brennball,                    disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen, Disciplines.Wurf]},
      { exercise: Exercises.BällePrellen,                 disciplines: [Disciplines.Wurf]},
      { exercise: Exercises.BällePrellenRennen,           disciplines: [Disciplines.Wurf]},
      { exercise: Exercises.BundesligaSprint,             disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen] },
      { exercise: Exercises.ChinesischeMauer,             disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
    
      { exercise: Exercises.CrosslaufAufRasen,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination, Disciplines.Überlaufen]},
      { exercise: Exercises.CrosslaufAufRasenIntervalle,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination, Disciplines.Staffellauf, Disciplines.Überlaufen]},
      
      { exercise: Exercises.DerFuchsGehtUm,               disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.Doppelbett,                   disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.DurchReifenHüpfen,            disciplines: [Disciplines.WeitsprungOhneGrube, Disciplines.WeitsprungMitGrube]},
      { exercise: Exercises.EinlaufenBällePrellen,        disciplines: [Disciplines.Wurf]},
      
      { exercise: Exercises.EinlaufenMitSprungseil,
        disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage, Disciplines.Schnelligkeit, Disciplines.Schnelllaufen, Disciplines.Wurf]},
      
      { exercise: Exercises.FCRunde,                      disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.FormenAblaufen,               disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.FormenAblaufenAufRasen,       disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.GeradeNachHintenFallenLassen, disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.HabichtUndHenne,              disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.HochsprungAnAnlage,           disciplines: [Disciplines.Hochsprung]},
      { exercise: Exercises.HüpfenderTausendfüßler,       disciplines: [Disciplines.Stabweitsprung, Disciplines.WeitsprungOhneGrube, Disciplines.WeitsprungMitGrube] },
      { exercise: Exercises.Hürdenbahn,                   disciplines: [Disciplines.Überlaufen]},
      { exercise: Exercises.HürdenbahnEinfach,            disciplines: [Disciplines.Überlaufen]},
      { exercise: Exercises.HürdenbahnMitSchwungbein,     disciplines: [Disciplines.Überlaufen]},
      { exercise: Exercises.HürdenbahnMitStaffel,         disciplines: [Disciplines.Überlaufen]},
      { exercise: Exercises.Hunderunde,                   disciplines: [Disciplines.Ausdauer]},
  
      { exercise: Exercises.HürdenlaufenAnHand,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},
    
      { exercise: Exercises.HütchenAbwerfen,              disciplines: [Disciplines.Wurf]},
      { exercise: Exercises.Klammerlauf_01,               disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.Klammerlauf_02,               disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.Klammerlauf_03,               disciplines: [Disciplines.Ausdauer]},
    
      { exercise: Exercises.KoordinationsLeiter,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination, Disciplines.Staffellauf, Disciplines.Überlaufen]},
      { exercise: Exercises.LaufABC_01,
        disciplines: [Disciplines.Ausdauer, Disciplines.Schnelllaufen, Disciplines.WeitsprungMitGrube] },
      { exercise: Exercises.LaufABC_02,
        disciplines: [Disciplines.Ausdauer, Disciplines.WeitsprungOhneGrube, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_03,
        disciplines: [Disciplines.Ausdauer, Disciplines.WeitsprungOhneGrube, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_04,
        disciplines: [Disciplines.Ausdauer, Disciplines.Hochsprung]},
      { exercise: Exercises.LaufABC_05,
        disciplines: [Disciplines.Ausdauer, Disciplines.HochsprungOhneAnlage, Disciplines.Koordination, Disciplines.Schnelllaufen, Disciplines.Überlaufen, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_06,
        disciplines: [Disciplines.Schnelllaufen, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.LaufABC_07,
        disciplines: [Disciplines.Ausdauer, Disciplines.HochsprungOhneAnlage, Disciplines.Schnelllaufen, Disciplines.Überlaufen, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_08,
        disciplines: [Disciplines.Schnelllaufen, Disciplines.Überlaufen]},
      { exercise: Exercises.LaufABC_09,
        disciplines: [Disciplines.Schnelligkeit, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_10,
        disciplines: [Disciplines.Überlaufen, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_11,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},
      { exercise: Exercises.LaufABC_12,
        disciplines: [Disciplines.Ausdauer, Disciplines.Stabweitsprung]},
      { exercise: Exercises.LaufABC_13,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},
      { exercise: Exercises.LaufABC_14,
        disciplines: [Disciplines.Hochsprung, Disciplines.Schnelllaufen]},
      { exercise: Exercises.LaufABC_15,
        disciplines: [Disciplines.Schnelligkeit, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_16,
        disciplines: [Disciplines.Ausdauer, Disciplines.Überlaufen]},
      { exercise: Exercises.LaufABC_17,
        disciplines: [Disciplines.Ausdauer, Disciplines.Überlaufen]},
      { exercise: Exercises.LaufABC_18,
        disciplines: [Disciplines.Schnelligkeit, Disciplines.Staffellauf, Disciplines.Wurf]},
      { exercise: Exercises.LaufABC_19,
        disciplines: [Disciplines.Staffellauf, Disciplines.Überlaufen]},
      { exercise: Exercises.LaufenZuMusik,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},
    
      { exercise: Exercises.LäuferGegenWerfer,            disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.MattenbergHochkommen,         disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.MattenbergRückenAufkommen,    disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},

      { exercise: Exercises.MedibälleHinHerWerfen,
        disciplines: [Disciplines.Ausdauer, Disciplines.Überlaufen]}, 
      { exercise: Exercises.MitSeilFangen,
        disciplines: [Disciplines.HochsprungOhneAnlage, Disciplines.Wurf]},
    
      { exercise: Exercises.Nummernwettläufe,             disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]}, 
    
      { exercise: Exercises.Pendelstaffel,
        disciplines: [Disciplines.Ausdauer, Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage, Disciplines.Schnelligkeit, Disciplines.Schnelllaufen, Disciplines.Wurf]},
      { exercise: Exercises.PendelstaffelMitBloxxHürden,
        disciplines: [Disciplines.Ausdauer, Disciplines.Überlaufen]},
      { exercise: Exercises.PendelstaffelMitHürden,
        disciplines: [Disciplines.Ausdauer, Disciplines.Staffellauf, Disciplines.Überlaufen, Disciplines.Wurf]},
    
      { exercise: Exercises.Reaktionsstart_01,            disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.Reaktionsstart_02,            disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.Reaktionsstart_03,            disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]}, 
      { exercise: Exercises.Reaktionsstart_04,            disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]}, 
      { exercise: Exercises.ReihenfolgeErarbeiten,        disciplines: [Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube]},
      { exercise: Exercises.ReiseNachJerusalemMitRingen,  disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.Rundenstaffel,                disciplines: [Disciplines.Staffellauf]},
      { exercise: Exercises.SauDurchsDorf,                disciplines: [Disciplines.Wurf]}, 
      { exercise: Exercises.SauDurchsDorfLang,            disciplines: [Disciplines.Wurf]},
    
      { exercise: Exercises.Schattenlaufen,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},
    
      { exercise: Exercises.SchereSteinPapier,            disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.SchereSteinPapierRennen,      disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.SchersprungAufMatte,          disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.SechsTageRennen,              disciplines: [Disciplines.Ausdauer] },
      { exercise: Exercises.SeilDannRennen,               disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},

      { exercise: Exercises.SeileRausziehen,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},
    
      { exercise: Exercises.Seilspringen,                 disciplines: [Disciplines.Stabweitsprung, Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube] },
      { exercise: Exercises.SeilspringenLangesSeil,       disciplines: [Disciplines.Stabweitsprung, Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube] },
      { exercise: Exercises.SeilspringenUndRunden,        disciplines: [Disciplines.Stabweitsprung, Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube] },
      { exercise: Exercises.SpringenAmReifen,             disciplines: [Disciplines.Stabweitsprung, Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube]}, 
      { exercise: Exercises.Stabweitsprung,               disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage, Disciplines.Stabweitsprung, Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube]},
      
      { exercise: Exercises.StaffelMitEinholen,
        disciplines: [Disciplines.Ausdauer, Disciplines.Koordination]},
    
      { exercise: Exercises.Staffelrennen,                disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.StaffelrennenMitHürden,       disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.StaffelrennenMitKarten,       disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.StaffelrennenMitTransport,    disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.StartAusVerschPositionen,     disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},    
      { exercise: Exercises.StarWars,                     disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.StationenMitWerfen,           disciplines: [Disciplines.Wurf]},
      { exercise: Exercises.SteigesprüngeAufBahn,         disciplines: [Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube]},
      { exercise: Exercises.SteigesprüngeÜberBahn,        disciplines: [Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube] },
      { exercise: Exercises.TheorieStaffelübergabe,       disciplines: [Disciplines.Staffellauf]},
      { exercise: Exercises.TicTacToe,                    disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.Transportlauf_01,             disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.Transportlauf_02,             disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.Transportlauf_03,             disciplines: [Disciplines.Ausdauer]},
    
      { exercise: Exercises.TransportSprint,
        disciplines: [Disciplines.Schnelligkeit, Disciplines.Wurf]},
      { exercise: Exercises.TransportSprintArme,
        disciplines: [Disciplines.Schnelligkeit, Disciplines.Wurf]},
      { exercise: Exercises.TransportSprintHürde,
        disciplines: [Disciplines.Überlaufen, Disciplines.Wurf]},
      { exercise: Exercises.TransportSprintLang,
        disciplines: [Disciplines.Staffellauf]},
      { exercise: Exercises.TurnenAufHochsprungMatte,
        disciplines: [Disciplines.Schnelligkeit, Disciplines.Staffellauf, Disciplines.Wurf]},
    
      { exercise: Exercises.TurnenAufMatte,               disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage]},
      { exercise: Exercises.ÜberBananenkistenSpringenInBahn, disciplines: [Disciplines.WeitsprungMitGrube, Disciplines.WeitsprungOhneGrube]},
      { exercise: Exercises.ÜberBloxxLaufen,              disciplines: [Disciplines.WeitsprungOhneGrube, Disciplines.WeitsprungMitGrube]},
      { exercise: Exercises.ÜberholstaffelKurz,           disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.ÜberholstaffelLang,           disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.ÜberholstaffelOhneWdh,        disciplines: [Disciplines.Ausdauer]},
      { exercise: Exercises.VerschiedeneStartformen,      disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.VonInselZuInselSpringenMitReifen, disciplines: [Disciplines.WeitsprungOhneGrube, Disciplines.WeitsprungMitGrube]},
      { exercise: Exercises.WeitsprungInGrube,            disciplines: [Disciplines.WeitsprungMitGrube] },
      { exercise: Exercises.WeitsprungInGrubeMitBananenKiste, disciplines: [Disciplines.WeitsprungMitGrube] },
      { exercise: Exercises.WerBautDenHöchstenTurm,       disciplines: [Disciplines.Schnelligkeit, Disciplines.Schnelllaufen]},
      { exercise: Exercises.WerfenMitAnlauf,              disciplines: [Disciplines.Wurf]},
      { exercise: Exercises.WerfenOhneAnlauf,             disciplines: [Disciplines.Wurf]},
      { exercise: Exercises.WerTrifftDieSonne,            disciplines: [Disciplines.Wurf]},
      
      { exercise: Exercises.WerTrifftRollendenBall,
        disciplines: [Disciplines.Schnelligkeit, Disciplines.Wurf]},

      { exercise: Exercises.Zeitschätzlauf,               disciplines: [Disciplines.Ausdauer]},
    
      { exercise: Exercises.ZweiRundenRollbrett,
        disciplines: [Disciplines.Hochsprung, Disciplines.HochsprungOhneAnlage, Disciplines.Schnelligkeit, Disciplines.Schnelllaufen, Disciplines.Staffellauf, Disciplines.Überlaufen, Disciplines.Wurf]},
    ];
  }

  static getAllDisciplines() {
    return Disciplines;
  }

  static generate(forDisciplineIds, targetDuration) {
    if (forDisciplineIds.length==0) return null;

    let forDisciplines = forDisciplineIds.map( (id) => Disciplines[id]);

    let suitableExercises = ExerciseWorksForDiscipline.filter(
      (exercise) => forDisciplines.filter( (selected) => exercise.disciplines.includes(selected)).length > 0
      ).map( (exercise) => exercise.exercise);

    // console.log("Suitable: " + JSON.stringify(suitableExercises));

    let warmups = suitableExercises.filter( (exercise) => exercise.warmup );
    let runabcs = suitableExercises.filter( (exercise) => exercise.runabc );
    let mainexs = suitableExercises.filter( (exercise) => exercise.mainex );
    let endings = suitableExercises.filter( (exercise) => exercise.ending ); 
  
    // console.log(warmups.length + " Warm-ups: " + JSON.stringify(warmups));
    // console.log("RunABCs: " + JSON.stringify(runabcs));
    // console.log("Main exercises: " + JSON.stringify(mainexs));
    // console.log("Endings: " + JSON.stringify(endings));

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
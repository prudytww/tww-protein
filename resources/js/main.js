const calcData = {
    system: window.twwc_object?.protein_settings?.system ?? 'imperial',
    weight: '',
    pregnant: 'No',
    activeLevel: '',
    goal: ''
};

const ui = {
    protienForm: document.querySelector('.protein-calculator-form'),
    unitsMeasurement: document.querySelectorAll('.protein-calculator__units-measurement'),
    toggleEls: document.querySelectorAll('.protein-calculator__toggle-units'),
    submitBtn: document.querySelector('.protein-calculator__submit'),
    results: document.querySelector('.protein-calculator--results__value span.the-result'),
    pregnant: document.querySelectorAll('.protein-calculator__pregnant'),
    goals: document.querySelectorAll('.protein-calculator__goal'),
    goal: document.querySelectorAll('.protein-calculator__goal:checked'),
    pregnantFields: document.querySelectorAll('.protein-calculator__pregnant-fields'),
    notPregnantFields: document.querySelectorAll('.protein-calculator__not-pregnant-fields'),
    activeLevel: document.querySelector('.protein-calculator__active-level'),
    weight: document.querySelector('.protein-calculator__weight'),
    resultsHighEnd: document.querySelector('.protein-calculator--results__high-end span.the-result-high'),
    
    //Theme Compact
    weightToggles: document.querySelectorAll('.protein-calculator__weight-toggle'),
    goalSelect: document.querySelector('.protein-calculator__goal-select'),
}

const setCalcData = (key, value) => {
    if (calcData.hasOwnProperty(key)) {
        calcData[key] = value;
    }
};

//Theme Compact
const initWeightToggles = () => {
    ui.weightToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            let system = e.target.value;

            if(system === 'imperial') {
                document.getElementById('weight_lbs').classList.remove('hide');
                document.getElementById('weight_kg').classList.add('hide');
            } else {            
                document.getElementById('weight_kg').classList.remove('hide');
                document.getElementById('weight_lbs').classList.add('hide');
            }

            convertToSystem(system);
        });
    });
}

    
    
const calcSettings = window.twwc_object;
console.log(calcSettings, 'calcSettings');

const initUnitsAndMeasures = () => {
    if(ui.unitsMeasurement.length === 0) return;
    if(ui.toggleEls.length === 0) return;
    
    ui.unitsMeasurement.forEach((unit) => {    
        unit.addEventListener('click', (e) => {
            const system = document.querySelector('.protein-calculator__units-measurement:checked').value;
            ui.toggleEls.forEach((el) => {
                if(el.classList.contains('hide')) {
                    el.classList.remove('hide');
                } else {
                    el.classList.add('hide');
                }
            });

            convertToSystem(system);
        });
    });
};

const convertToSystem = (system) => {
    if(!system) return;

    const weight_lbs = document.querySelector('.protein-calculator__weight--lbs').value;
    const weight_kg = document.querySelector('.protein-calculator__weight--kg').value;
    let value = '';
    
    //now we need to convert the value in weight to the selected system
    if('metric' === system) {
        value = Math.round((weight_lbs / 2.20462) * 100) / 100;
        value = 0 === value ? '' : value;
       document.querySelector('.protein-calculator__weight--kg').value = value;
    } else if('imperial' === system) {
        value = Math.round((weight_kg * 2.20462) * 100) / 100;
        value = 0 === value ? '' : value;
        document.querySelector('.protein-calculator__weight--lbs').value = value;
    }
}

const initPregnant = () => {
    if(!ui.pregnant.length) return;

    ui.pregnant.forEach((pregnant) => {
        pregnant.addEventListener('click', (e) => {
            setCalcData('pregnant', e.target.value);

            if('No' === e.target.value) {
                ui.notPregnantFields.forEach((field) => {
                    field.classList.remove('hide');
                });
            } else {
                ui.notPregnantFields.forEach((field) => {
                    field.classList.add('hide');
                });
            }
        });
    });
}

const initGoal = () => {
    if(!ui.goals.length) return;

    ui.goals.forEach((goal) => {
        goal.addEventListener('click', (e) => {
            setCalcData('goal', e.target.value);
        });
    });
}
        
const initCalculation = () => { 
    if(!ui.protienForm) return;

    ui.protienForm.addEventListener('input', (e) => {
        let totalProtein = null;
        let totalProteinHigh = null;

        const system = document.querySelector('.protein-calculator__units-measurement:checked').value;
        const weight = getWeight(system);
        const pregnant_and_lactating = calcData.pregnant;
        const activeLevel = ui.activeLevel.value;
        let goal = '';
        console.log(activeLevel, 'activeLevel');
        if(ui.goalSelect && ui.goalSelect.length) {
            goal = ui.goalSelect.value;
            console.log(goal, 'goal yup');
            console.log(pregnant_and_lactating, 'pregnant_and_lactating');
            setCalcData('goal', goal);
        } else {
            goal = calcData.goal;
            console.log(goal, 'goal');
            console.log(pregnant_and_lactating, 'pregnant_and_lactating');
        }

        if(system && 'No' !== pregnant_and_lactating) {
            totalProtein = pregnant(system, weight, pregnant_and_lactating);
        } else if (system && weight) {
            const baseProtein = basicProteinCalculation(system, weight, activeLevel, goal);
            const baseProteinHigh = basicProteinCalculationHigh(system, weight, activeLevel, goal);

            totalProteinHigh = baseProteinHigh;
            totalProtein = baseProtein;
        }

        totalProtein = parseInt(totalProtein) || 0;
        totalProteinHigh = parseInt(totalProteinHigh) || 0;

        ui.results.innerText = Math.round(totalProtein) || '—';
        ui.resultsHighEnd.innerText = Math.round(totalProteinHigh) || '—';
    });
}

const initActiveLevel = () => {
    if(!ui.activeLevel.length) return;

    ui.activeLevel.forEach((level) => {
        level.addEventListener('click', (e) => {
            calcData.activeLevel = e.target.value;
        });
    });
}

const basicProteinCalculation = (system, weight, activeLevel, goal, multiplier = 1.2) => {
    multiplier = 'imperial' !== system ? parseFloat(calcSettings?.protein_settings?.multiplier_weight_kg) : parseFloat(calcSettings?.protein_settings?.multiplier_weight_lbs);
    let prefix = 'm_';
    let suffix = 'imperial' !== system ? '_kg' : '_lbs';
    let goalField = prefix + goal + suffix;

    console.log(goalField, 'goalField')
    console.log(calcSettings?.protein_settings?.activity_level[activeLevel].goal[goalField], 'goalField')

    if (activeLevel) { 
        multiplier = parseFloat(calcSettings?.protein_settings?.activity_level[activeLevel][prefix + activeLevel + suffix]);
        console.log(multiplier, 'multiplier')
    }

    if(goal && calcSettings?.protein_settings?.activity_level[activeLevel].goal[goalField] !== undefined) {
        multiplier = parseFloat(calcSettings?.protein_settings?.activity_level[activeLevel].goal[goalField]);
        console.log(multiplier, 'multiplier two')
    }
    
    const value = weight * multiplier;

    return value;
}

const basicProteinCalculationHigh = (system, weight, activeLevel, goal, multiplier = 1.2) => {
    multiplier_high = 'imperial' !== system ? parseFloat(calcSettings?.protein_settings?.multiplier_weight_high_kg) : parseFloat(calcSettings?.protein_settings?.multiplier_weight_high_lbs);
    let prefix = 'm_';
    let suffix = 'imperial' !== system ? '_high_kg' : '_high_lbs';
    let goalFieldHighEnd = prefix + goal + suffix;

    console.log(goal, 'goaal')

    if(activeLevel) {
        multiplier_high = parseFloat(calcSettings?.protein_settings?.activity_level[activeLevel][prefix + activeLevel + suffix]);
    }

   if(goal && calcSettings?.protein_settings?.activity_level[activeLevel].goal[goalFieldHighEnd] !== undefined) {
        multiplier_high = parseFloat(calcSettings?.protein_settings?.activity_level[activeLevel].goal[goalFieldHighEnd]);
    } else {
        //remove _high from the goal field
        goalField = goalFieldHighEnd.replace('_high', '');
        if(goal && calcSettings?.protein_settings?.activity_level[activeLevel].goal[goalField] !== undefined) {
            multiplier_high = 0;
        }
    }
    
    const value = weight * multiplier_high;

    return value;
}

const pregnant = (system, weight, pregnant_and_lactating) => {
    if(!weight) return;

    const multiplier = 'imperial' !== system ? parseFloat(calcSettings?.protein_settings?.multiplier_weight_kg) : parseFloat(calcSettings?.protein_settings?.multiplier_weight_lbs);
    let baseProtein = weight * multiplier; 

    if ('I am pregnant' === pregnant_and_lactating) {
        // Ensure we add numbers, not strings
        const pregnantValue = parseFloat(calcSettings?.protein_settings?.pregnant);
        baseProtein += pregnantValue; // Perform addition before formatting
    }

    if('I am nursing' === pregnant_and_lactating) {
        // Ensure we add numbers, not strings
        const nursingValue = parseFloat(calcSettings?.protein_settings?.pregnant_lactating);
        baseProtein += nursingValue; // Perform addition before formatting
    }

    // If you need to output baseProtein elsewhere with 3 decimal places
   return baseProtein;
}

const getWeight = (system) => {
    if('metric' === system) {
        return document.querySelector('.protein-calculator__weight--kg').value;
    } else if('imperial' === system) {
        return document.querySelector('.protein-calculator__weight--lbs').value;
    }
}

const getHeight = (system) => {
    if('metric' === system) {
        return document.querySelector('.protein-calculator__height--cm').value;
    } else if('imperial' === system) {        
        return height.feet * 12 + height.inches;
    }
}

(function () { 
    initUnitsAndMeasures()
    initPregnant()
    initGoal()
    initCalculation()
    initWeightToggles()
})();
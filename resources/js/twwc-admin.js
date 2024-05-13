const calcData = {
    system: window.twwc_admin_object?.protein_settings?.system ?? 'imperial',
    weight: '',
    pregnant: 'No',
    activeLevel: '',
    goal: ''
};

console.log(twwc_admin_object, 'twwc_admin_object');

const ui = {
    system: document.querySelectorAll('.protein-calculator__units-measurement'),
    weight: document.querySelector('.protein-calculator__weight'),
    metricInputs: document.querySelectorAll('.protein-calculator__metric-input'),
    imperialInputs: document.querySelectorAll('.protein-calculator__imperial-input'),
};

const setCalcData = (key, value) => {
    if (calcData.hasOwnProperty(key)) {
        calcData[key] = value;
    }
};

const initSystem = () => {
    ui.system.forEach(system => {
        system.addEventListener('click', (e) => {
            setCalcData('system', e.target.value);
            console.log("system", e.target.value);
            convertToSystem();
        });
    });

    convertToSystem(); // Run on initialization to set up correct state
};

const convertToSystem = () => {
    const useMetric = calcData.system === 'metric';
    ui.metricInputs.forEach(input => input.disabled = !useMetric);
    ui.imperialInputs.forEach(input => input.disabled = useMetric);
};

(function() {
    if (ui.system.length > 0) {
        initSystem();
    }
    //initCalculation(); // Uncomment or define initCalculation as needed.
})();

console.log(calcData, 'calcData');
console.log(window.twwc_admin_object);

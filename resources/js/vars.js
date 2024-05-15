calcData = {
    defaultSystem: window.twwc_object.system ?? 'metric',
    pregnant: document.querySelector('.protein-calculator__pregnant:checked').value ?? 'No',
    goal: document.querySelector('.protein-calculator__goal:checked').value ?? 'maintenance',
    activeLevel: window.twwc_object.defaults.active_level ?? '',
}

setCalcData = (key, value) => {
    calcData[key] = value;
}


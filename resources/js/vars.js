calcData = {
    defaultSystem: window.twwc_object.system ?? 'metric',
    pregnant: document.querySelector('.protein-calculator__pregnant:checked').value,
    goal: document.querySelector('.protein-calculator__goal:checked').value,
    activeLevel: 'sedentary'
}

setCalcData = (key, value) => {
    calcData[key] = value;
}

const activeLevels = {
    sedentary: {
        label: 'Sedentary',
        'metric': 1.2, // grams of protein per kg of body weight
        'imperial': 0.56, // grams of protein per pound of body weight
        goal: {
            'weight_loss': {
                'metric': 0.8,
                'imperial': 0.36
            },
            'weight_gain': {
                'metric': 1.0,
                'imperial': 0.45
            },
            'maintenance': {
                'metric': 0.8,
                'imperial': 0.36
            },
        }
    },
    lightly_active: {
        label: 'Lightly Active',
        'metric': 1.0,
        'imperial': 0.45,
        goal: {
            'weight_loss': {
                'metric': 0.8,
                'imperial': 0.36
            },
            'weight_gain': {
                'metric': 1.2,
                'imperial': 0.54
            },
            'maintenance': {
                'metric': 1.0,
                'imperial': 0.45
            }
        }
    },
    moderately_active: {
        label: 'Moderately Active',
        'metric': 1.2,
        'imperial': 0.54,
        goal: {
            'weight_loss': {
                'metric': 0.8,
                'imperial': 0.36
            },
            'weight_gain': {
                'metric': 1.4,
                'imperial': 0.63
            },
            'maintenance': {
                'metric': 1.2,
                'imperial': 0.54
            }
        }
    },
    very_active: {
        label: 'Very Active',
        'metric': 1.4,
        'imperial': 0.63,
        goal: {
            'weight_loss': {
                'metric': 0.8,
                'imperial': 0.36
            },
            'weight_gain': {
                'metric': 1.6,
                'imperial': 0.72
            },
            'maintenance': {
                'metric': 1.4,
                'imperial': 0.63
            }
        }
    },
    super_active: {
        label: 'Super Active',
        'metric': 1.6,
        'imperial': 0.72,
        goal: {
            'weight_loss': {
                'metric': 0.8,
                'imperial': 0.36
            },
            'weight_gain': {
                'metric': 1.8,
                'imperial': 0.81
            },
            'maintenance': {
                'metric': 1.6,
                'imperial': 0.72
            }
        }
    }
}
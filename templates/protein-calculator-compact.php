<?php 
$system = $protein_settings['system'] ?? null;
$activity_level = $protein_settings['activity_level'] ?? null;
?>

<div class="protein-calculator protein-calculator--compact">
    <div class="protein-calculator-inner">
        <form class="protein-calculator-form">

        <div class="protein-calculator__form-group protein-calculator__form-group--radio">
            <div class="protein-calculator__label">
                <label class="main-label" for="Units">Weight</label>
            </div>
            <!-- here we will have a radio toggle for the units after the input -->
            <div class="protein-calculator__inputs ">
                <input class="protein-calculator__weight protein-calculator__weight--lbs <?php echo 'metric' === $system ? 'hide' : '';  ?>" type="number" id="weight_lbs" name="weight_lbs" placeholder="Weight in Pounds">
                <input class="protein-calculator__weight protein-calculator__weight--kg <?php echo 'imperial' === $system ? 'hide' : '';  ?>" type="number" id="weight_kg" name="weight_kg" placeholder="Weight in Kilograms">

                <div class="protein-calculator__inputs--radio-reg-label"> 
                    <div>
                    <input step=".001" class="protein-calculator__weight-toggle protein-calculator__units-measurement"  type="radio" id="imperial" name="units" value="imperial" <?php echo 'imperial' === $system ? 'checked' : '';  ?>>
                    <label  for="imperial">lbs</label>
                    </div>
                    <div>
                    <input step=".001" class="protein-calculator__weight-toggle protein-calculator__units-measurement" type="radio" id="metric" name="units" value="metric" <?php echo 'metric' === $system ? 'checked' : '';  ?>><label for="metric">kg</label>
                    </div>
                </div>
            </div>
        </div>
            
        <!-- Activity Level -->
        <div class="protein-calcualtor__form-group">
            <div class="protein-calculator__label">
                <label class="main-label" for="activity">How active are you?</label>
            </div>

            <div class="protein-calculator__inputs protein-calculator__inputs--select">
                <?php $activity_level = $protein_settings['activity_level'] ?? null; ?>
                <select class="protein-calculator__active-level" name="activity" id="activity">
                    
                    <?php foreach($activity_level as $key => $value) : ?>
                        <?php $label = $value['label'] ? $value['label'] : ucwords(str_replace('_', ' ', $key)); ?>

                        <?php if($value['enable']) :?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>     
        </div>

        <!-- Goals -->
        <div class="protein-calculator__form-group">   
            <div class="protein-calculator__label"><label class="main-label" for="activity">Goals?</label></div>
            <div class="protein-calculator__inputs protein-calculator__inputs--select">
                <select class="protein-calculator__goal-select" name="goal" id="goal">
                    <option value="maintain">Maintain</option>
                    <option value="toning">Toning</option>
                    <option value="muscle_growth">Muscle Growth</option>
                    <option value="weight_loss">Lose Weight</option>
                </select>
            </div>
        </div>  
    </form>
        <div class="protein-calculator--results">
            <div class="protein-calculator--results-inner">
                <div class="protein-calculator--results-default">
                    <div class="protein-calculator--results__label">
                        <label for="protein">Protein Intake</label>
                    </div>
                    <div class="protein-calculator--results__value">
                        <span class='the-result'>&mdash;</span><span id="calculator-system-suffix">g</span>
                    </div>
                </div>

                <div class="protein-calculator--results-high-end">
                    <div class="protein-calculator--results__label">
                        <label for="protein">Protein Intake <br /> (High End)</label>
                    </div>
                    <div class="protein-calculator--results__high-end">
                        <span class='the-result-high'>&mdash;</span><span id="calculator-system-suffix">g</span>
                    </div>
                </div>

                <div class="protein-message">
                    <span>*</span> Weight is required.
                </div>
            </div> <!-- Closing tag for protein-calculator--results-inner -->
        </div> <!-- Closing tag for protein-calculator--results -->
    </div><!-- Closing tag for protein-calculator-inner -->
</div> <!-- Closing tag for protein-calculator -->

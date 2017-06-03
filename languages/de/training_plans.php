<?php

    return [
        'label_create_training_plan' => 'Trainingsplan erstellen',
        'label_normal_training_plan' => 'Einfacher Trainingsplan',
        'label_split_training_plan' => 'Split Trainingsplan',
        'label_edit_training_plan' => 'Trainingsplan bearbeiten',
        'label_start_training_plan' => 'Trainingsplan starten',
        'label_add_new_training_plan' => 'Neuen Trainingsplan hinzufügen',
        'label_training_plan_name' => 'Name des Trainingsplanes',
        'label_training_plan_active' => 'Trainingsplan aktiv?',
        'label_current_training_plan' => 'Aktueller Trainingsplan',
        'label_old_training_plans' => 'Alte Trainingspläne',
        'tooltip_click_to_show_current_exercise_in_training' => 'Klicken, um die aktuelle Übung im Trainingstagebuch anzusehen',

        'select_existing_training_plan_as_template' => 'bestehenden Trainingsplan als Vorlage auswählen',

        'text_need_permissions_to_show_training_plan' => 'Sie haben nicht die erforderlichen Rechte, um sich diesen Trainingsplan anzuzeigen!<br /><br />Nicht eingeloggt?',

        'training_plans_default_content' => 'Das ist die Übersichtsseite für die Trainingspläne,<br /><br />'.
            'Hier sieht man den aktuellen Trainingsplan und darunter ein drop down mit allen archivierten trainingsplänen.' .
            'klickt man einen der Trainingspläne an, kann man sich dazu die Details anzeigen lassen.<br /><br />'.
            '<div class="row">
                <div class="col-sm-12 text-center">
                    <p>
                        <button type="button" id="demo" class="btn btn-default btn-lg" data-demo="">
                            <span class="glyphicon glyphicon-play"></span>
<!--                            Start the ' . call_user_func([$this, 'translate'], "label_exercises") . ' demo-->
                            Start exercises demo
                        </button>
                    </p>
                </div>
            </div>'
    ];
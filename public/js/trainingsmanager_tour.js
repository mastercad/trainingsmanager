var demoButton = jQuery('#demo');
var tourEndPosition = null;

var tour = new Tour({
    onStart: function () {
        jQuery('body').data('overflow', jQuery('body').css('overflow')).css('overflow', 'visible');
        jQuery('#left').data('overflow', jQuery('#left').css('overflow')).css('overflow', 'visible');
        jQuery('#right').data('overflow', jQuery('#right').css('overflow')).css('overflow', 'visible');
        return demoButton.addClass("disabled", true);
    },
    onEnd: function () {
        jQuery('body').css('overflow', jQuery('body').data('overflow'));
        jQuery('#left').css('overflow', jQuery('#left').data('overflow'));
        jQuery('#right').css('overflow', jQuery('#right').data('overflow'));
        return demoButton.removeClass("disabled", true);
    },
    onNext: function (tour) {
        if (null !== tourEndPosition
            && tour.getCurrentStep() == tourEndPosition
        ) {
            tour.end();
        }
    },
    backdrop: true,
    backdropPadding: 10,
    debug: true,
    smartPlacement: true,
    duration: 5000,
    storage: window.localStorage,
    orphan: true
});

jQuery(document).ready(function() {

    tour.addStep({
        element: "#nav",
        title: "Navigation",
        placement: isMobile ? 'bottom' : 'right',
        content: "Das ist das Navigationsmenü",
        onShow: function(tour) {
            if (isMobile) {
                jQuery('.navbar-toggle').click();
                return false;
            }
        },
        onHide: function(tour) {
            //if (isMobile) {
            //    jQuery('.navbar-toggle').click();
            //    return false;
            //}
        }
    });

    tour.addStep({
        element: "li.uebungen .uebersicht",
        title: "Navigation",
        placement: isMobile ? 'bottom' : 'right',
        content: "Hier kann man sich eine Übersicht aller Übungen anzeigen lassen.<br /><br />Hat man die notwendigen Rechte, können sie auch Übungen editieren und neue anlegen, sowie alte löschen.",
        onShow:  function (tour) {
            //if (isMobile) {
            //    jQuery('.navbar-toggle').click();
            //}
            jQuery('li.uebungen ul.dropdown-menu').dropdown('toggle');
            return false;

        },
        onHide: function(tour) {
            //if (isMobile) {
            //    jQuery('.navbar-toggle').click();
            //}
            jQuery('li.uebungen ul.dropdown-menu').dropdown('toggle');
            return false;
        }
    });

    if ('MEMBER' == UserRightGroup
        || 'TEST_MEMBER' == UserRightGroup
        || 'ADMIN' == UserRightGroup
        || 'TEST_ADMIN' == UserRightGroup
        || 'GROUP_ADMIN' == UserRightGroup
        || 'SUPERADMIN' == UserRightGroup
    ) {
        tour.addStep({
            element: 'li.uebungen .Neu',
            title: 'Navigation - neue Übung anlegen',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Hier kann man eine neue Übung anlegen',
            onShow:  function (tour) {
                //if (isMobile) {
                //    jQuery('.navbar-toggle').click();
                //}
                jQuery('li.uebungen ul.dropdown-menu').dropdown('toggle');
                return false;

            },
            onHide: function(tour) {
                //if (isMobile) {
                //    jQuery('.navbar-toggle').click();
                //}
                jQuery('li.uebungen ul.dropdown-menu').dropdown('toggle');
                return false;
            }
        });
    }

    tour.addStep({
        element: "li.Trainingsplaene .uebersicht",
        title: "Navigation",
        placement: isMobile ? 'bottom' : 'right',
        content: "Hier kann man sich eine Übersicht aller Trainingspläne ansehen und neue anlegen.<br /><br />Hat man hier die notwendingen Rechte, kann man ebenfalls die Trainingspläne von Usern der selben Gruppe editieren oder neue anlegen.",
        onShow:  function (tour) {
            //if (isMobile) {
            //    jQuery('.navbar-toggle').click();
            //}
            jQuery('li.Trainingsplaene ul.dropdown-menu').dropdown('toggle');
            return false;

        },
        onHide: function(tour) {
            //if (isMobile) {
            //    jQuery('.navbar-toggle').click();
            //}
            jQuery('li.Trainingsplaene ul.dropdown-menu').dropdown('toggle');
            return false;
        }
    });

    if ('MEMBER' == UserRightGroup
        || 'TEST_MEMBER' == UserRightGroup
        || 'ADMIN' == UserRightGroup
        || 'TEST_ADMIN' == UserRightGroup
        || 'GROUP_ADMIN' == UserRightGroup
        || 'SUPERADMIN' == UserRightGroup
    ) {
        tour.addStep({
            element: 'li.Trainingsplaene .Neu',
            title: 'Navigation - neuen Trainingsplan anlegen',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Hier kann man einen neuen Trainingsplan anlegen',
            onShow:  function (tour) {
                //if (isMobile) {
                //    jQuery('.navbar-toggle').click();
                //}
                jQuery('li.Trainingsplaene ul.dropdown-menu').dropdown('toggle');
                return false;

            },
            onHide: function(tour) {
                if (isMobile) {
                    jQuery('.navbar-toggle').click();
                }
                jQuery('li.Trainingsplaene ul.dropdown-menu').dropdown('toggle');
                return false;
            }
        });
    }
/*
    if ('MEMBER' == UserRightGroup
        || 'TEST_MEMBER' == UserRightGroup
        || 'ADMIN' == UserRightGroup
        || 'TEST_ADMIN' == UserRightGroup
        || 'GROUP_ADMIN' == UserRightGroup
        || 'SUPERADMIN' == UserRightGroup
    ) {
        tour.addStep({
            element: 'li.Trainingsplaene .Archiv',
            title: 'Navigation - Trainingsplan Archiv',
            content: 'Hier liegen alle alten Trainingspläne',
            onShow:  function (tour) {
                jQuery('li.Trainingsplaene ul.dropdown-menu').dropdown('toggle');
                return false;

            },
            onHide: function(tour) {
                jQuery('li.Trainingsplaene ul.dropdown-menu').dropdown('toggle');
                return false;
            }
        });
    }
*/
/*
    tour.addStep({
        element: "li.Trainingstagebuch .uebersicht",
        title: "Navigation",
        content: "Hier kann man sich eine Übersicht aller bereits absolvierten Trainingspläne sowie den aktuellen Trainingsplan ansehen.",
        onShow:  function (tour) {
            jQuery('li.Trainingstagebuch ul.dropdown-menu').dropdown('toggle');
            return false;

        },
        onHide: function(tour) {
            jQuery('li.Trainingstagebuch ul.dropdown-menu').dropdown('toggle');
            return false;
        }
    });
*/

    if ('GUEST' != UserRightGroup) {
        tour.addStep({
            element: '.widget-button',
            title: 'Dashboard - Widget Button',
            placement: 'left',
            content: 'Mit einem Klick auf diesen Button kann man seinem Dashboard Widgets hinzufügen.<br /><br />' +
                'Diese Widgets können z.b. den aktuellen Trainingsplan, oder den Frotschritt bei Übungen anzeigen.'
        });
    }
    tour.addStep({
        path: '/exercises',
        element: 'UNKNOWN',
        title: 'Übungen',
        placement: 'center',
        orphan: true,
        content: 'Hier kann man alle verfügbaren Übungen ansehen, <br />sowie mit den notwendigen Rechten neue anlegen, bereits bestehende editieren und löschen.'
    });

    tour.addStep({
        element: '#left #accordion',
        title: 'Übungen - Liste',
        placement: isMobile ? 'top' : 'right',
        content: 'Hier wird eine Liste aller vorhandenen Übungen anzeigt.'
    });

    tour.addStep({
        element: '.exercises-filter',
        title: 'Übungen - Filter',
        placement: isMobile ? 'bottom' : 'right',
        content: 'Hier ist es möglich die Übersicht der Übungen nach bestimmten Kriterien zu filtern.'
    });

    tour.addStep({
        element: '.exercises-filter #filter_exercise_type_id',
        title: 'Übungen - Filter Übungstyp',
        placement: isMobile ? 'bottom' : 'right',
        content: 'Hier werden die Übungen nach Typen gefiltert'
    });

    tour.addStep({
        element: '.exercises-filter #device_id',
        title: 'Übungen - Filter Gerät',
        placement: isMobile ? 'bottom' : 'right',
        content: 'Hier filtert man die angezeigten Übungen nach den Geräten',
        onNext: function (tour) {
            jQuery('#left #accordion .exercise:first').trigger('click');
            var checkExist = setInterval(function () {
                if (jQuery('img.preview-picture').is(':visible')) {
                    clearInterval(checkExist);
                    setTourPositionByTitle('Übungen - Detailansicht');
                }
            }, 100);
        }
    });

    tour.addStep({
        element: '#left #accordion .exercise:first',
        title: 'Übungen - Detailansicht',
        placement: isMobile ? 'bottom' : 'right',
        orphan: true,
        content: 'Klickt man einmal auf eine Übung, wird für diese Übung die Detailansicht geladen'
    });

    if ('MEMBER' == UserRightGroup
        || 'TEST_MEMBER' == UserRightGroup
        || 'ADMIN' == UserRightGroup
        || 'TEST_ADMIN' == UserRightGroup
        || 'GROUP_ADMIN' == UserRightGroup
        || 'SUPERADMIN' == UserRightGroup
    ) {
        tour.addStep({
            element: '.detail-options',
            title: 'Übungen - Optionen',
            placement: isMobile ? 'left' : 'left',
            content: 'Hier sind verschiedene Optionen für die angezeigte Übung verfügbar, so unter anderem Editieren oder Löschen'
        });
    }

    tour.addStep({
        element: 'h3:first',
        title: 'Übungen - Name',
        placement: 'bottom',
        content: 'Hier steht der Name der Übung'
    });

    tour.addStep({
        element: 'img.preview-picture',
        title: 'Übungen - Vorschaubild',
        placement: 'bottom',
        content: 'Hier ist ein Vorschaubild zu der Übung zu sehen'
    });

    tour.addStep({
        element: '.exercise-type',
        title: 'Übung - Typ',
        placement: 'top',
        content: 'Hier steht, von welchem Typ die aktuelle Übung ist, nach diesen Typen läßt sich auch in auf der linken Seite filtern.<br /><br />Unter anderem gibt es da Ausdauer, Masse, Dehnen und Reha'
    });

    tour.addStep({
        element: '.exercise-description',
        title: 'Übung - Beschreibung',
        placement: 'top',
        content: 'Hier steht die Beschreibung zu der jeweiligen Übung, dabei wird die Durchführung beschrieben'
    });

    tour.addStep({
        element: '.exercise-special-features',
        title: 'Übungen - Specielle Bemerkungen',
        placement: 'top',
        content: 'Einige Übungen haben bestimmte Dinge, auf die gesondert geachtet werden muss, gerade in Verbindung mit bestimmten Geräten, das findet hier Platz.'
    });

    tour.addStep({
        element: '.exercise-device',
        title: 'Übungen - Gerät',
        placement: 'top',
        content: 'Ist für die aktuelle Übung ein Gerät notwendig, steht dieses Gerät hier.'
    });

    tour.addStep({
        element: '.exercise-device-options',
        title: 'Übungen - Geräte Optionen',
        placement: 'top',
        content: 'Gibt es für die aktuelle Übung optionale Geräte - Parameter, wie Gewichte, Sitzposition, etc. wird das hier angezeigt.'
    });

    tour.addStep({
        element: '.exercise-exercise-options',
        title: 'Übungen - Übungs Optionen',
        placement: 'top',
        content: 'Gibt es für die aktuelle Übung optionale Übungs - Parameter, wie Wiederholungen, Sätze, Dauer, etc. wird das hier angezeigt.'
    });

    tour.addStep({
        element: '.exercise-muscle-groups',
        title: 'Übungen - Muskel Gruppen',
        placement: 'top',
        content: 'Hier werden die durch diese Übung beanspruchten Muskeln angezeigt, bewertet wird dabei auf einer Skala von 1 - 5.'
    });

    if ('MEMBER' == UserRightGroup
        || 'TEST_MEMBER' == UserRightGroup
        || 'ADMIN' == UserRightGroup
        || 'TEST_ADMIN' == UserRightGroup
        || 'GROUP_ADMIN' == UserRightGroup
        || 'SUPERADMIN' == UserRightGroup
    ) {
        tour.addStep({
            element: '.detail-options .edit-button',
            title: 'Übungen - Edit Button',
            placement: 'left',
            content: 'Um eine Übung zu bearbeiten, kann man auf das Edit Icon klicken.',
            //onShow: function (tour) {
            //    backdropElementZIndex = jQuery('.detail-options').css('z-index');
            //    jQuery('.detail-options').css('z-index', 9999);
            //
            //},
            //onHide: function (tour) {
            //    jQuery('.detail-options').css('z-index', backdropElementZIndex);
            //},
            onNext: function (tour) {
                //jQuery('#left #accordion .exercise:first').trigger('dblclick');
                //jQuery('#left #accordion .exercise:first').trigger('click').trigger('click');
                jQuery('#left #accordion .exercise:first').trigger('click').trigger('click').trigger('click');
                var checkExist = setInterval(function () {
                    if (jQuery('#exercise_name').is(':visible')) {
                        clearInterval(checkExist);
                        setTourPositionByTitle('Übungen - Editieren');
                    }
                }, 100);
            }
        });

        tour.addStep({
            element: '#left #accordion .exercise:first',
            title: 'Übungen - Editieren',
            placement: isMobile ? 'bottom' : 'right',
            content: 'oder auf den Eintrag der Übung in der Übungsübersicht doppelt klicken.'
        });

        tour.addStep({
            element: '#exercise_name',
            title: 'Übung - Name bearbeiten',
            placement: 'bottom',
            content: 'Hier kann man einen Namen für die Übung angeben, oder einen bereits existierenden anpassen.'
        });

        tour.addStep({
            element: '#dropzone',
            title: 'Übung - Vorschaubild',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Hier kann man für die Übung ein oder mehrere Bilder einstellen.<br /><br />' +
            'Die Bilder können per Drag&Drop hierher gezogen werden. Mit einem Klick auf diese Zone kann man aber ' +
            'auch den gewohnten Dialog für das hochladen von Dateien öffnen. <br /><br />' +
            'User mit einem Mobile Gerät können hier direkt auf ihre Kamera zugreifen und Bilder aufnehmen oder bereits aufgenommene hochladen.'
        });

        tour.addStep({
            element: '#dropzone_previews',
            title: 'Übung - Thumbnails',
            placement: isMobile ? 'bottom' : 'left',
            content: 'Hier sieht man jedes bereits für diese Übung hochgeladene Bild als kleines Vorschaubild.'
        });

        tour.addStep({
            element: '#dropzone_previews .dz-preview:first',
            title: 'Übung - Thumbnail',
            placement: isMobile ? 'bottom' : 'left',
            content: 'Klickt man auf eines dieser Bilder, wird dieses Bild als neues Vorschaubild der Übung gesetzt<br /><br />' +
            'Die zusätzlichen Bilder können beim Training als Beispielbilder für die Durchführung der Übung genutzt werden.',
            onShown: function(tour) {
                jQuery('#dropzone_previews .dz-preview:first .dz-details').trigger('click');
            }
        });

        tour.addStep({
            element: '#dropzone_previews .dz-preview:first a.dz-remove',
            title: 'Übung - Bild löschen',
            placement: isMobile ? 'bottom' : 'left',
            content: 'Mit einem Klick auf Remove löscht man das Bild dauerhaft.',
            onHide: function(tour) {
                //jQuery('#exercise_type_id .data-toggle').toggle();
                jQuery('#exercise_type_id button.dropdown-toggle').dropdown('toggle');
                return false;
            }
        });

        tour.addStep({
            element: '#exercise_type_id',
            title: 'Übung - Typ',
            placement: 'top',
            content: 'Hier stellt man den Typ für die jeweilige Übung ein:',
            onHidden: function(tour) {
                //jQuery('#exercise_type_id .data-toggle').toggle();
                jQuery('#exercise_type_id button.dropdown-toggle').dropdown('toggle');
                return false;
            }
        });

        tour.addStep({
            element: '.exercise-description',
            title: 'Übungen - Beschreibung bearbeiten',
            placement: 'bottom',
            content: 'Hier kann man die Beschreibung zur Übung bearbeiten oder eine neue hinterlegen.'
        });

        tour.addStep({
            element: '.exercise-special-features',
            title: 'Übungen - Besonderheiten bearbeiten',
            placement: 'bottom',
            content: 'Gibt es zu der aktuellen Übung Besonderheiten, wie z.b. bestimmte Dinge auf die man bei der ' +
                'Ausführung achten muss, oder ähnliches, wird das hier hinterlegt.'
        });

        tour.addStep({
            element: '.exercise-device-select',
            title: 'Übungen - Gerät bearbeiten',
            placement: 'bottom',
            content: 'In diesem Eingabefeld kann man nach einem Gerät suchen, es wird einem bei Eingabe der Anfangsbuchstaben eine Liste '+
                'von möglichen Übungen vorgeschlagen, beim Klick darauf, wird diese Übung gesetzt.<br /><br />' +
                'Es ist dabei wichtig, das man dieses Gerät aus den Vorschlägen auswählt. Ist dieses Gerät nicht vorhanden, muss es vorher angelegt werden!',
            onHide: function(tour) {
                //jQuery('#exercise_type_id .data-toggle').toggle();
                jQuery('.device-options-drop-down button.dropdown-toggle').dropdown('toggle');
                return false;
            }
        });

        tour.addStep({
            element: '.device-options-drop-down',
            title: 'Übungen - Geräteoptionen bearbeiten',
            placement: 'bottom',
            content: 'Mit diesem Dropdown kann man bestimmte Geräte Einstellungen für diese Übung hinterlegen.<br /><br />' +
                'Diese Einstellungen sind vom benutzten Gerät abhängig und von Gerät zu Gerät unterschiedlich!<br /><br />' +
                'Hier hinterlegte Einstellungen sind in den Trainingsplänen auswählbar, aber nicht verpflichtend.',
            onHidden: function(tour) {
                //jQuery('#exercise_type_id .data-toggle').toggle();
                jQuery('.device-options-drop-down button.dropdown-toggle').dropdown('toggle');
                return false;
            }
        });

        tour.addStep({
            element: '.exercise-selected-device-options',
            title: 'Übungen - ausgewählte Geräteoptionen bearbeiten',
            placement: 'bottom',
            content: 'Hier sind alle bisher gesetzten Geräteoptionen aufgelistet.'
        });

        tour.addStep({
            element: '.exercise-selected-device-options div:first',
            title: 'Übungen - Geräteoption bearbeiten - Gesamtansicht',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Dies ist eine gewählte Geräteoption.'
        });

        tour.addStep({
            element: '.exercise-selected-device-options div:first input',
            title: 'Übungen - Geräteoption bearbeiten - Optionen hinterlegen',
            placement: 'bottom',
            content: 'In diesem Eingabefeld kann man die möglichen Optionen hinterlegen.<br /><br />' +
            'Gibt es nur einen Wert, trägt man den einfach ein.<br /><br />' +
            'Gibt es mehrere Werte, trägt man die getrennt durch ein "|" hintereinander ein,' +
            'diese Werte werden dann automatisch zu einem Dropdown für das setzen im Trainingsplan umgewandelt.'
        });

        tour.addStep({
            element: '.exercise-selected-device-options div:first .delete',
            title: 'Übungen - Geräteoption bearbeiten - bereits hinterlegte Optionen',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Mit diesem Button kann man die Option wieder aus der Übersicht entfernen,' +
                'durch erneutes auswählen im Dropdown darüber ist sie natürlich auch wieder verfügbar.',
            onHidden: function(tour) {
                //jQuery('#exercise_type_id .data-toggle').toggle();
                jQuery('.exercise-option-select button.dropdown-toggle').dropdown('toggle');
                return false;
            }
        });

        tour.addStep({
            element: '.exercise-option-select',
            title: 'Übungen - Übungsoptionen bearbeiten',
            placement: 'bottom',
            content: 'Mit diesem Dropdown kann man bestimmte Optionen für diese Übung hinterlegen.<br /><br />' +
                'Diese Einstellungen repräsentieren z.b. die Anzahl der möglichen Sätze dieser Übung und/oder die Wiederholungen.<br /><br />' +
                'Hier hinterlegte Einstellungen sind in den Trainingsplänen auswählbar, aber nicht verpflichtend.',
            onHidden: function(tour) {
                //jQuery('#exercise_type_id .data-toggle').toggle();
                jQuery('.exercise-option-select button.dropdown-toggle').dropdown('toggle');
                return false;
            }
        });

        tour.addStep({
            element: '.exercise-options',
            title: 'Übungen - Übungsoptionen bearbeiten - bereits hinterlegte Optionen',
            placement: 'bottom',
            content: 'Hier sind alle bisher gesetzten Übungsoptionen aufgelistet.'
        });

        tour.addStep({
            element: '.exercise-muscle-groups-input',
            title: 'Übungen - Muskelgruppen bearbeiten',
            placement: 'top',
            content: 'Analog zu der Auswahl eines Gerätes für diese Übung kann man hier die beanspruchten Muskeln setzen.<br /><br />' +
                'Das wird über Muskelgrupen realisiert. Man gibt hier den Namen oder die Anfangsbuchstaben einer Muskelgruppe ein, worauf hin'+
                'die passenden Muskelgruppen vorgeschlagen werden.<br /><br />' +
                'Auch hier ist wichtig, das man eine Muskelgruppe aus den Vorschlägen auswählt, ist die gewünschte Muskelgruppe nicht verfügbar, muss sie in den Muskelgruppen angelegt werden.'
        });

        tour.addStep({
           element: '.exercise-muscle-groups',
            title: 'Übungen - Muskelgruppen bearbeiten - bereits hinterlegte Muskelgruppen',
            placement: 'top',
            content: 'Hier werden alle oben ausgewählten und mit mindestens einem bewerteten Muskel gesetzten Muskelgruppen angezeigt.'
        });

        tour.addStep({
            element: '.exercise-muscle-groups .muscle-group:first',
            title: 'Übungen - Muskelgruppe bearbeiten - Muskelgruppe',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Dies ist eine der ausgewählten und bewerteten Muskelgruppen'
        });

        tour.addStep({
            element: '.exercise-muscle-groups .muscle-group:first .muscle img:first',
            title: 'Übungen - Muskelgruppe bearbeiten - Muskel bewerten',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Mit einem Klick auf einen dieser Sterne kann man den aktuellen Muskel bewerten.<br /><br />' +
                'Hat diese Muskelgruppe mehrere Muskel, müssen nicht zwangsläufig alle bewerten werden.'
        });

        tour.addStep({
            element: '.exercise-muscle-groups .muscle-group:first .muscle .muscle-delete:first',
            title: 'Übungen - Muskelgruppe bearbeiten - Muskel löschen',
            placement: isMobile ? 'bottom' : 'left',
            content: 'Mit einem Klick auf dieses Icon kann man die Bewertung für diesen Muskel entfernen.'
        });

        tour.addStep({
            element: '.exercise-muscle-groups .muscle-group:first .muscle-group-delete:first',
            title: 'Übungen - Muskelgruppe bearbeiten - Muskelgruppe löschen',
            placement: isMobile ? 'bottom' : 'left',
            content: 'Mit einem Klick auf dieses Icon entfernt man die gesamte Muskelgruppe aus der aktuellen Übung.<br /><br/>' +
                'Ein erneutes Auswählen aus den Muskelgruppenvorschlägen macht diese Muskelgruppe selbstverständlich erneut verfügbar.'
        });

        tour.addStep({
            element: '#save',
            title: 'Übungen - Übung bearbeiten - speichern',
            placement: isMobile ? 'top' : 'top',
            content: 'Mit einem Klick auf diesen Button speichert man die Änderungen zu dieser Übung.'
        });
    }

    if ('USER' == UserRightGroup
        || 'MEMBER' == UserRightGroup
        || 'TEST_MEMBER' == UserRightGroup
        || 'ADMIN' == UserRightGroup
        || 'TEST_ADMIN' == UserRightGroup
        || 'GROUP_ADMIN' == UserRightGroup
        || 'SUPERADMIN' == UserRightGroup
    ) {
        tour.addStep({
            path: '/training-plans',
            element: 'UNKNOWN',
            title: 'Trainingspläne',
            placement: 'center',
            orphan: true,
            content: 'Hier kann man alle Trainingspläne ansehen, <br />sowie mit den notwendigen Rechten neue anlegen, bereits bestehende editieren und löschen.'
        });

        tour.addStep({
            element: '.current-training-plans',
            title: 'Trainingspläne - aktueller Trainingsplan Container',
            placement: isMobile ? 'bottom' : 'right',
            content: 'hier steht der aktuell gültige Trainingsplan des Users'
        });

        tour.addStep({
            element: '#current_training_plan',
            title: 'Trainingspläne - aktueller Trainingsplan',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Mit einem Klick auf diesen Eintrag, wird die Detailansicht des aktuellen Trainingsplanes geladen'
        });

        tour.addStep({
            element: '.nav.nav-tabs',
            title: 'Trainingspläne - Trainingsplan Splits',
            placement: isMobile ? 'bottom' : 'bottom',
            content: 'Ist der Trainingsplan in verschiedene Splits unterteilt, stehen hier die einzelnen Trainingspläne, sonst steht hier ein Trainingsplan'
        });

        tour.addStep({
            element: '.nav.nav-tabs li:first',
            title: 'Trainingspläne - Tab eines Trainingsplanes',
            placement: 'bottom',
            content: 'Mit einem klick auf einen dieser Tabs kann man sich die Details für diesen Trainingsplan anzeigen.'
        });

        if ('MEMBER' == UserRightGroup
            || 'TEST_MEMBER' == UserRightGroup
            || 'ADMIN' == UserRightGroup
            || 'TEST_ADMIN' == UserRightGroup
            || 'GROUP_ADMIN' == UserRightGroup
            || 'SUPERADMIN' == UserRightGroup
        ) {
            tour.addStep({
                element: '.training-plans.detail-options',
                title: 'Trainingspläne - detail Options',
                placement: isMobile ? 'bottom' : 'left',
                content: 'Hat man die notwendigen Rechte, kann man diesen Trainingsplan editieren und/oder löschen.'
            });
        }

        tour.addStep({
            element: '.muscles-for-training-plan',
            title: 'Trainingspläne - Übersicht der beanspruchten Muskeln',
            placement: 'bottom',
            content: 'Hier werden alle für den aktuellen Trainingsplan beanspruchten Muskeln aufgelistet.<br /><br/>' +
                '1 Stern bedeutet wenig, 5 Sterne bedeuten dabei maximal beansprucht'
        });

        tour.addStep({
            element: '.row.exercise:first',
            title: 'Trainingspläne - Übersicht der Übungen',
            placement: 'top',
            content: 'Hier stehen alle für diesen Trainingsplan vorgesehenen Übungen.<br /><br />' +
                'Die Details zu den jeweiligen Übungen sind analog zu denen In der Detailansicht auf der Übungsseite.'
        });

        tour.addStep({
            element: '.training-plan-start',
            title: 'Trainingspläne - Start des aktuell angezeigten Trainingsplanes',
            placement: 'top',
            content: 'Mit einem klick auf diesen Button startet man direkt das Training mit dem aktuell angezeigten Trainingsplan.'
        });

        tour.addStep({
            element: '#accordion_old_training_plans',
            title: 'Trainingspläne - Liste der alten Trainingspläne',
            placement: isMobile ? 'bottom' : 'right',
            content: 'Hier kann man sich alle archivierten Trainingspläne ansehen.'
        });
    }

    tour.init();
});

function setTourStartPositionByTitle(title) {
    jQuery.each(tour._options.steps, function(position, step) {
        if (title == step.title) {
            tour.goTo(position);
            return false;
        }
    });
}

function setTourEndPositionByTitle(title) {
    jQuery.each(tour._options.steps, function(position, step) {
        if (title == step.title) {
            tourEndPosition = position;
            return false;
        }
    });
}
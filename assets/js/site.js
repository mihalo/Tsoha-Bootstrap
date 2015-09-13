$('.datepicker').datepicker({
    format: 'dd/mm/yyyy'
});

function msToTime(ms) {
    if (ms <= 0) {
        return "";
    }

    var remaining = ms;

    var hours = Math.floor(ms / (1000 * 60 * 60));
    remaining = remaining - (hours * 1000 * 60 * 60);
    var minutes = Math.floor(remaining / (1000 * 60));
    remaining = remaining - (minutes * 1000 * 60);
    var seconds = Math.floor(remaining / 1000);
    var ms = remaining - seconds * 1000;

    return hours.pad(2) + ":" + minutes.pad(2) + ":" + seconds.pad(2) + "." + ms.pad(3);
}

Number.prototype.pad = function (size) {
    var s = String(this);
    while (s.length < (size || 2)) {
        s = "0" + s;
    }
    return s;
}

function timeToMs(time) {
    var pieces = time.split(':');
    var hours = parseInt(pieces[0]) * 60 * 60 * 1000;
    var minutes = pieces[1] * 60 * 1000;
    var seconds = pieces[2].split('.')[0] * 1000;
    var milliseconds = pieces[2].split('.')[1];
    return hours + minutes + seconds + parseInt(milliseconds);
}

function rules() {
    $(document).ready(function () {
        $('#rules').summernote({
            height: "200px",
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']]
            ]
        });
    });
}

function code() {
    var sHTML = $('#rules').code();
    console.log(sHTML);
    document.getElementById("r").value = sHTML;
    document.getElementById('rls').submit();
}


function calculate() {
    $(document).ready(function () {
        var i = 1;
        var bestMs;
        $("#quali td:nth-child(4)").each(function () {
            if (i === 1) {
                bestMs = timeToMs($(this).children().val());
            }
            var gap = timeToMs($(this).children().val()) - bestMs;
            if (gap !== 0 && !isNaN(gap)) {
                document.getElementById('q' + i).innerHTML = msToTime(gap);
            }
            i++;
        });

        var cells = document.getElementById('race').getElementsByTagName('td');

        var bestLaps;
        var bestTime;
        var j = 0;
        for (var i = 3; i < cells.length; i = i + 7) {
            j++;
            if (i === 3) {
                bestLaps = cells[i].children[0].value;
                bestTime = timeToMs(cells[i - 1].children[0].value);
                continue;
            }
            var laps = parseInt(cells[i].children[0].value);
            var time = timeToMs(cells[i - 1].children[0].value);
            if (bestLaps - laps === 0) {
                document.getElementById('r' + j).innerHTML = msToTime(bestTime - time);
            } else {
                var gap = bestLaps - laps;
                if (gap === 1) {
                    document.getElementById('r' + j).innerHTML = bestLaps - laps + " lap";
                } else {
                    document.getElementById('r' + j).innerHTML = bestLaps - laps + " laps";
                }
            }
        }
    });
}


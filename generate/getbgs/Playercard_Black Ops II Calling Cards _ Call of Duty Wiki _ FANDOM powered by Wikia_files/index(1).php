/* Contains parts made by Quarenon */
$(function () {
    if (!$('#QuickCVUAdder')) {
        return;
    }

    function callAPI(data, method, callback) {
        data['format'] = 'json';

        $.ajax({
            data: data,
            dataType: 'json',
            url: wgScriptPath + '/api.php',
            type: method,
            success: function (response) {
                if (response.error) {
                    alert('API error: ' + response.error.info);
                } else {
                    callback(response);
                }
            },
            error: function (xhr, error) {
                showError('AJAX error: ' + error);
            },
            timeout: 10000 // msec
        });
    }

    function CVUInsert() {
        if (!$('#Userinput').val()) {
            alert("Fill in the input please.");
            return false;
        }
        $('#CVUInsertSubmit').val('Reporting user');
        callAPI({
            'action': 'query',
            'prop': 'info|revisions',
            'intoken': 'edit',
            'titles': 'Call_of_Duty_Wiki:Counter_Vandalism_Unit',
            'rvprop': 'content',
            'rvlimit': '1'
        }, 'GET', function (response) {
            var pages = response.query.pages;
            var page = null;

            for (var i in pages) {
                page = pages[i];
            }

            var dataInput = '*{{CVU|';
            dataInput += $('#Userinput').val()
            if ($('#Pageinput').val()) {
                dataInput += '|' + $('#Pageinput').val();
            }
            dataInput += '}}';

            var a = page.revisions[0]['*'];
            if (a.indexOf('{{CVU|Example}}') > 1) {
                a = a.replace('*{{CVU|Example}}', dataInput);
            } else {
                a = a.replace("\n*{{CVU|", dataInput + "\n" + "*{{CVU|");
            }

            callAPI({
                'minor': 'yes',
                'summary': 'Added CVU report with Quick CVU adder.',
                'action': 'edit',
                'title': 'Call_of_Duty_Wiki:Counter_Vandalism_Unit',
                'basetimestamp': page.revisions[0].timestamp,
                'startimestamp': page.starttimestamp,
                'token': page.edittoken,
                'text': a
            }, 'POST', function (response) {
                if (response.edit.result == 'Success') {
                   document.location.replace(wgScript + '?title=' + encodeURIComponent(wgPageName) + '&action=purge');
                } else {
                    alert('An error occurred while submitting the edit.');
                }
            });
        });
        return false;
    }

    var $form = $('<form />');
    $form.submit(CVUInsert);
    var $input = $('<p />');

    $input.append($('<label />').css({
        'for': 'Userinput',
        'width': '4em',
        'float': 'left',
        'text - align': 'right',
        'margin - right': '0.5em',
        'display': 'block'
    }).text('User:'));
    $input.append($('<input />').attr({
        'id': 'Userinput',
        'type': 'text',
        'size': 10
    }));
    $input.append($('<p />'));
    $input.append($('<label />').css({
        'for': 'Userinput',
        'width': '4em',
        'float': 'left',
        'text - align': 'right',
        'margin - right': '0.5em',
        'display': 'block'
    }).text('Page:'));
    $input.append($('<input />').attr({
        'id': 'Pageinput',
        'type': 'text',
        'size': 10
    }));
    $input.append($('<p />'));
    $input.append($('<input />').attr({
        'id': 'CVUInsertSubmit',
        'type': 'submit'
    }).val('Report user'));
    $form.append($input);

    $('#QuickCVUAdder').html($form);
});
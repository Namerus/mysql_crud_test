$(document).ready(function () 
{
    $('form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        $.ajax({
            url: 'ajax.php',
            data: $form.serialize()
        }).done(function (req) {
            $('#result_form').html(req);
            $form.trigger('reset');
        }).fail(function () {
            console.log(req);
            $('#result_form').html('Ошибка добавления элемента');
        });
    });

    $(".delete_item").click(function (e) {
        e.preventDefault();
        var data = $(this).data('req');
        $.ajax({
            url: 'ajax.php',
            data: data
        }).done(function (req) {
            $('#result_form').html(req);
        }).fail(function () {
            console.log(req);
            $('#result_form').html('Ошибка удаления элемента');
        });
    });
});
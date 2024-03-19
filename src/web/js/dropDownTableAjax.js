$(document).on('click', '[data-ajax="true"]', function (event) {
    $this = $(this);
    if ($this.attr('aria-expanded') === 'false') {
        getData($this);
    }
});

$(document).on('click', '.dropdown-table a', function (event) {
    if (!$(this).hasClass('no-ajax') && !$(this).data('action')) {
        var container = $(this).closest('.expandable-body');
        var dataBlock = container.prev('[data-ajax="true"]');
        if (container.length !== 0 && dataBlock.length !== 0) {
            getData(dataBlock, this.href);
            event.stopPropagation();
            event.preventDefault();
        }
    }
});

$(document).ready(function() {
    $('[aria-expanded="true"]').each(function() {
        getData($(this));
    });
});

var getData = function ($this, dataUrl = null) {
    var relations = $this.data('relations');
    var model = $this.data('model');
    var id = $this.data('key');
    var url = dataUrl ?? $this.data('ajax-url');

    if (relations !== undefined && relations.length !== 0 && model !== undefined && id !== undefined) {
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                relations: relations,
                model: model,
                id: id
            },
            success: function(response) {
                $this.next('.expandable-body').find('td').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
            }
        });
    } else {
        console.error('No relations specified');
    }
}

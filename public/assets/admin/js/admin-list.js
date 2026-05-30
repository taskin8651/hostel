function initAdminDataTable(selector, options = {}) {
    if (!$(selector).length) return;

    const dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons || []);

    if (options.canDelete && options.massDeleteUrl) {
        dtButtons.push({
            text: '<i class="fas fa-trash-alt" style="margin-right:5px;"></i> ' + (options.deleteText || 'Delete selected'),
            url: options.massDeleteUrl,
            className: 'btn-danger',
            action: function (e, dt, node, config) {
                const ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                    return $(entry).data('entry-id');
                });

                if (!ids.length) {
                    alert(options.zeroSelectedText || 'No rows selected');
                    return;
                }

                if (confirm(options.confirmText || 'Are you sure?')) {
                    $.ajax({
                        headers: { 'x-csrf-token': window._token || $('meta[name="csrf-token"]').attr('content') },
                        method: 'POST',
                        url: config.url,
                        data: {
                            ids: ids,
                            _method: 'DELETE'
                        }
                    }).done(function () {
                        location.reload();
                    });
                }
            }
        });
    }

    $(selector + ':not(.ajaxTable)').DataTable({
        buttons: dtButtons,
        order: options.order || [[1, 'desc']],
        pageLength: options.pageLength || 25,
        scrollX: true,
        language: {
            search: '',
            searchPlaceholder: options.searchPlaceholder || 'Search...',
            lengthMenu: 'Show _MENU_ entries',
            info: options.infoText || 'Showing _START_–_END_ of _TOTAL_ entries',
        }
    });
}
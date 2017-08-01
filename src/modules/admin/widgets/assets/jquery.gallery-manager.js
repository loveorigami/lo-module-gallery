(function ($) {
    'use strict';

    var galleryDefaults = {
        csrfToken: $('meta[name=csrf-token]').attr('content'),
        csrfTokenName: $('meta[name=csrf-param]').attr('content'),
        pjaxContainer: '',
        pjaxUrl: '',
        updateUrl: '',
        deleteUrl: '',
        orderUrl: '',
        statusUrl: ''
    };

    function galleryManager(el, options) {
        //Extending options:
        var opts = $.extend({}, galleryDefaults, options);
        //code
        var csrfParams = opts.csrfToken ? '&' + opts.csrfTokenName + '=' + opts.csrfToken : '';

        var $gallery = $(el);

        var $sorter = $('.sorter', $gallery);
        var $images = $('.images', $sorter);


        $images
            .off('click')
            .off('keydown')
            .on('keydown', '.photo .edit-photo', editClick)
            .on('click', '.photo .delete-photo', deleteClick)
            .on('click', '.photo .status-photo', statusClick)
            .on('click', '.photo .onmain-photo', onmainClick)
            .on('click', '.photo .photo-select', selectChanged)
        ;

        $gallery
            .off('click')
            .on('click', '.remove-selected', removeSelected);

        function editClick(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code === 13) {
                //$('.edit-photo', $gallery).prop('readonly', true);
                var photo = $(this).closest('.photo');
                editPhotos(photo);
                e.preventDefault();
            }
        }

        function deleteClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            // here can be question to confirm delete
            if (!confirm('Действительно удалить этот файл?')) return false;
            removePhotos([id]);
            return false;
        }

        function statusClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            togglePhotos(photo, 'status');
            return false;
        }

        function onmainClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            togglePhotos(photo, 'on_main');
            return false;
        }

        function removeSelected(e) {
            e.preventDefault();
            if (!confirm('Действительно удалить?')) return false;
            var ids = [];
            $('.photo.selected', $sorter).each(function () {
                ids.push($(this).data('id'));
            });
            removePhotos(ids);
        }

        /**
         * functions
         * @param photo
         */
        function editPhotos(photo) {
            var id = photo.data('id');
            var data = [];
            var input = $("#inptxt_" + id);
            var name = input.val();
            data.push('photo[' + photo.data('id') + '][name]=' + name);
            input.hide();

            $.ajax({
                type: 'POST',
                url: opts.updateUrl,
                data: data.join('&') + csrfParams,
                success: function () {
                    //pjaxReload();
                    input.show().focus();
                }
            });
        }

        function togglePhotos(photo, attr) {
            var id = photo.data('id');
            var data = [];
            data.push('photo[' + photo.data('id') + '][' + attr + ']=' + photo.data(attr));

            $.ajax({
                type: 'POST',
                url: opts.statusUrl,
                data: data.join('&') + csrfParams,
                dataType: "json",
                success: function () {
                    pjaxReload();
                }
            })
        }

        function removePhotos(ids) {
            $.ajax({
                type: 'POST',
                url: opts.deleteUrl,
                data: 'id[]=' + ids.join('&id[]=') + csrfParams,
                success: function () {
                    pjaxReload();
                }
            });
        }

        $('.images', $sorter).sortable({
            tolerance: "pointer"
        }).disableSelection().bind("sortstop", function () {
            var data = [];
            $('.photo', $sorter).each(function () {
                var t = $(this);
                data.push('order[]=' + t.data('id'));
            });
            $.ajax({
                type: 'POST',
                url: opts.orderUrl,
                data: data.join('&') + csrfParams,
                dataType: "json",
                success: function () {
                    pjaxReload();
                }
            });
        });

        /**
         * reload gallery
         */
        function pjaxReload() {
            $.pjax.reload({
                timeout: 5000,
                container: opts.pjaxContainer,
                url: opts.url,
                showNoty: false
            })
        }


        $('.select_all', $gallery).change(function () {
            if ($(this).prop('checked')) {
                $('.photo', $sorter).each(function () {
                    $('.photo-select', this).prop('checked', true)
                }).addClass('selected');
            } else {
                $('.photo.selected', $sorter).each(function () {
                    $('.photo-select', this).prop('checked', false)
                }).removeClass('selected');
            }
            updateButtons();
        });

        function selectChanged() {
            var $this = $(this);
            if ($this.is(':checked')) {
                $this.closest('.photo').addClass('selected');
            }
            else {
                $this.closest('.photo').removeClass('selected');
            }
            updateButtons();
        }

        function updateButtons() {
            var selectedCount = $('.photo.selected', $sorter).length;
            $('.select_all', $gallery).prop('checked', $('.photo', $sorter).length === selectedCount);
            if (selectedCount === 0) {
                $('.remove-selected', $gallery).addClass('disabled');
            } else {
                $('.remove-selected', $gallery).removeClass('disabled');
            }
        }

    }

    // The actual plugin
    $.fn.galleryManager = function (options) {
        if (this.length) {
            this.each(function () {
                galleryManager(this, options);
            });
        }
    };
})(jQuery);
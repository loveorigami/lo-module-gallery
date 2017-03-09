(function ($) {
    'use strict';

    var galleryDefaults = {
        csrfToken: $('meta[name=csrf-token]').attr('content'),
        csrfTokenName: $('meta[name=csrf-param]').attr('content'),
        nameLabel: 'Name',
        descriptionLabel: 'Description',
        hasName: true,
        hasDesc: false,
        uploadUrl: '',
        deleteUrl: '',
        updateUrl: '',
        orderUrl: '',
        statusUrl: '',
        photos: []
    };

    function galleryManager(el, options) {
        //Extending options:
        var opts = $.extend({}, galleryDefaults, options);
        //code
        var csrfParams = opts.csrfToken ? '&' + opts.csrfTokenName + '=' + opts.csrfToken : '';
        var photos = {}; // photo elements by id
        var $gallery = $(el);
        if (!opts.hasName) {
            if (!opts.hasDesc) {
                $gallery.addClass('no-name-no-desc');
                $('.edit-selected', $gallery).hide();
            }
            else {
                $gallery.addClass('no-name');
            }
        } else if (!opts.hasDesc)
            $gallery.addClass('no-desc');

        var $log = $('.log', $gallery);
        var $sorter = $('.sorter', $gallery);
        var $images = $('.images', $sorter);

        var $addToStartName = 'toStart';
        var $addToStartVal = false;


        var $editorModal = $('.editor-modal', $gallery);
        var $progressOverlay = $('.progress-overlay', $gallery);
        var $uploadProgress = $('.upload-progress', $progressOverlay);
        var $editorForm = $('.form', $editorModal);

        $('.add_start', $gallery).change(function () {
            $addToStartVal = $(this).prop('checked');
        });

        function htmlEscape(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function createEditorElement(id, src, name, description) {

            var html = '<div class="photo-editor row">' +
                '<div class="col-xs-4">' +
                '<img src="' + htmlEscape(src) + '"  style="max-width:100%;">' +
                '</div>' +
                '<div class="col-xs-8">' +
                (opts.hasName
                    ?
                '<div class="form-group">' +
                '<label class="control-label" for="photo_name_' + id + '">' + opts.nameLabel + ':</label>' +
                '<input class="form-control" type="text" name="photo[' + id + '][name]" class="input-xlarge" value="' + htmlEscape(name) + '" id="photo_name_' + id + '"/>' +
                '</div>' : '') +
                (opts.hasDesc
                    ?
                '<div class="form-group">' +
                '<label class="control-label" for="photo_description_' + id + '">' + opts.descriptionLabel + ':</label>' +
                '<textarea class="form-control" name="photo[' + id + '][description]" rows="3" cols="40" class="input-xlarge" id="photo_description_' + id + '">' + htmlEscape(description) + '</textarea>' +
                '</div>' : '') +
                '</div>' +
                '</div>';
            return $(html);
        }

        var photoTemplate = '<div class="photo col-md-2 col-sm-6 col-xs-12">'
            + '<div class="photo-wrap">'
            + '<div class="image-preview"><img src=""/></div>'
            + '<div class="caption">';
        if (opts.hasName) {
            photoTemplate += '<h5></h5>';
        }
        if (opts.hasDesc) {
            photoTemplate += '<p></p>';
        }
        photoTemplate += '</div>'
            + '<div class="wrap-actions">'
            + '<div class="actions pull-right">';

        photoTemplate += '<span class="onmain-photo btn btn-xs" data-toggle="tooltip" title="на главной"><i class="glyphicon"></i></span> ';
        photoTemplate += '<span class="status-photo btn btn-xs" data-toggle="tooltip" title="статус"><i class="glyphicon"></i></span> ';
        photoTemplate += '<a class="preview-photo btn btn-primary btn-xs" data-toggle="tooltip" title="просмотр"><i class="glyphicon glyphicon-zoom-in"></i></a> ';

        if (opts.hasName || opts.hasDesc) {
            photoTemplate += '<span class="edit-photo btn btn-primary btn-xs" data-toggle="tooltip" title="редактировать"><i class="glyphicon glyphicon-pencil"></i></span> ';
        }

        photoTemplate += '<span class="delete-photo btn btn-primary btn-xs" data-toggle="tooltip" title="удалить"><i class="glyphicon glyphicon-remove glyphicon-white"></i></span>'
            + '</div>'
            + '<div class="pull-left"><input type="checkbox" class="photo-select"/></div>'
            + '</div>'
            + '</div>'
            + '</div>';


        function loadPhoto(img) {
            var photo = $(photoTemplate);
            photos[img['id']] = photo;

            photo.data('id', img['id']);
            photo.data('pos', img['pos']);
            photo.data('status', img['status']);
            photo.data('on_main', img['on_main']);

            $('img', photo).attr('src', img['preview']);

            $('a.preview-photo', photo).attr('href', img['image']);

            $('.photo-wrap', photo).addClass('active' + img['status']);

            if (opts.hasName) {
                $('.caption h5', photo).text(img['name']);
            }
            if (opts.hasDesc) {
                $('.caption p', photo).text(img['description']);
            }

            if (img['status']) {
                $('.status-photo', photo).addClass('btn-primary').children('i').addClass('glyphicon-eye-open');
            } else {
                $('.status-photo', photo).addClass('btn-danger').children('i').addClass('glyphicon-minus');
            }

            if (img['on_main']) {
                $('.onmain-photo', photo).addClass('btn-success').children('i').addClass('glyphicon-home');
            } else {
                $('.onmain-photo', photo).addClass('btn-primary').children('i').addClass('glyphicon-list');
            }

            return photo;
        }


        function editPhotos(ids) {
            var l = ids.length;
            var form = $editorForm.empty();
            for (var i = 0; i < l; i++) {
                var id = ids[i];
                var photo = photos[id],
                    src = $('img', photo).attr('src'),
                    name = $('.caption h5', photo).text(),
                    description = $('.caption p', photo).text();
                form.append(createEditorElement(id, src, name, description));
            }
            if (l > 0) {
                $editorModal.modal('show');
            }
        }

        function pastePhotos(photo) {
            if ($addToStartVal) {
                $images.prepend(photo);
            } else {
                $images.append(photo);
            }
        }

        function togglePhotos(photo, attr) {
            var id = photo.data('id');
            var data = [];
            data.push('photo[' + photo.data('id') + '][' + attr + ']=' + photo.data(attr));
            $.ajax({
                type: 'POST',
                url: opts.statusUrl,
                data: data.join('&') + csrfParams,
                dataType: "json"
            }).done(function (resp) {
                var img = loadPhoto(resp);
                $('.photo', $gallery).each(function () {
                    if (id == $(this).data('id')) {
                        $(this).replaceWith(img);
                    }
                });
            });
        }

        function removePhotos(ids) {
            $.ajax({
                type: 'POST',
                url: opts.deleteUrl,
                data: 'id[]=' + ids.join('&id[]=') + csrfParams,
                success: function (t) {
                    if (t == 'OK') {
                        for (var i = 0, l = ids.length; i < l; i++) {
                            photos[ids[i]].remove();
                            delete photos[ids[i]];
                        }
                    } else {
                        alert(t);
                    }
                }
            });
        }


        function deleteClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            // here can be question to confirm delete
            if (!confirm('Действительно удалить?')) return false;
            removePhotos([id]);
            return false;
        }

        function previewClick(e) {
            e.preventDefault();
            var items = [];
            items.push({
                src: $(this).attr('href')
            });

            $.magnificPopup.open({
                type: 'image',
                items: items
            });
        }

        function editClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            editPhotos([id]);
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

        function updateButtons() {
            var selectedCount = $('.photo.selected', $sorter).length;
            $('.select_all', $gallery).prop('checked', $('.photo', $sorter).length == selectedCount);
            if (selectedCount == 0) {
                $('.edit-selected, .remove-selected', $gallery).addClass('disabled');
            } else {
                $('.edit-selected, .remove-selected', $gallery).removeClass('disabled');
            }
        }

        function selectChanged() {
            var $this = $(this);
            if ($this.is(':checked'))
                $this.closest('.photo').addClass('selected');
            else
                $this.closest('.photo').removeClass('selected');
            updateButtons();
        }

        $images
            .on('click', '.photo .delete-photo', deleteClick)
            .on('click', '.photo .edit-photo', editClick)
            .on('click', '.photo .status-photo', statusClick)
            .on('click', '.photo .onmain-photo', onmainClick)
            .on('click', '.photo .preview-photo', previewClick)
            .on('click', '.photo .photo-select', selectChanged);


        $('.images', $sorter).sortable({tolerance: "pointer"}).disableSelection().bind("sortstop", function () {
            var data = [];
            $('.photo', $sorter).each(function () {
                var t = $(this);
                data.push('order[]=' + t.data('id'));
            });
            $.ajax({
                type: 'POST',
                url: opts.orderUrl,
                data: data.join('&') + csrfParams,
                dataType: "json"
            }).done(function (data) {
                for (var id in data[id]) {
                    photos[id].data('pos', data[id]);
                }
                // order saved!
                // we can inform user that order saved
            });
        });

        if (window.FormData !== undefined) { // if XHR2 available
            var uploadFileName = $('.afile', $gallery).attr('name');

            var multiUpload = function (files) {
                if (files.length == 0)
                    return;

                $log.text('');
                $progressOverlay.show();
                $uploadProgress.css('width', '5%');

                var filesCount = files.length;
                var uploadedCount = 0;
                var ids = [];
                for (var i = 0; i < filesCount; i++) {
                    var fd = new FormData();

                    fd.append(uploadFileName, files[i]);
                    fd.append($addToStartName, $addToStartVal);

                    if (opts.csrfToken) {
                        fd.append(opts.csrfTokenName, opts.csrfToken);
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', opts.uploadUrl, true);
                    xhr.onload = function () {
                        uploadedCount++;
                        if (this.status == 200) {
                            var resp = JSON.parse(this.response);
                            if (resp['result']) {
                                var img = resp['image'];
                                var photo = loadPhoto(img);
                                pastePhotos(photo);
                                ids.push(img['id']);
                            }
                            else {
                                $log.append(resp['errors']);
                            }
                        } else {
                            // exception !!!
                        }
                        $uploadProgress.css('width', '' + (5 + 95 * uploadedCount / filesCount) + '%');

                        if (uploadedCount === filesCount) {
                            $uploadProgress.css('width', '100%');
                            $progressOverlay.hide();
                            if (opts.hasName || opts.hasDesc) {
                                //editPhotos(ids);
                            }
                        }
                    };
                    xhr.send(fd);
                }

            };

            (function () { // add drag and drop
                var el = $gallery[0];
                var isOver = false;
                var lastIsOver = false;

                setInterval(function () {
                    if (isOver != lastIsOver) {
                        if (isOver)
                            el.classList.add('over');
                        else
                            el.classList.remove('over');
                        lastIsOver = isOver
                    }
                }, 30);

                function handleDragOver(e) {
                    e.preventDefault();
                    isOver = true;
                    return false;
                }

                function handleDragLeave() {
                    isOver = false;
                    return false;
                }

                function handleDrop(e) {
                    e.preventDefault();
                    e.stopPropagation();


                    var files = e.dataTransfer.files;
                    multiUpload(files);

                    isOver = false;
                    return false;
                }

                function handleDragEnd() {
                    isOver = false;
                }


                el.addEventListener('dragover', handleDragOver, false);
                el.addEventListener('dragleave', handleDragLeave, false);
                el.addEventListener('drop', handleDrop, false);
                el.addEventListener('dragend', handleDragEnd, false);
            })();

            $('.afile', $gallery).attr('multiple', 'true').on('change', function (e) {
                e.preventDefault();
                multiUpload(this.files);
            });
        } else {
            $('.afile', $gallery).on('change', function (e) {
                e.preventDefault();
                var ids = [];
                $progressOverlay.show();
                $uploadProgress.css('width', '5%');

                var data = {};
                if (opts.csrfToken) {
                    data[opts.csrfTokenName] = opts.csrfToken;
                }
                data[$addToStartName] = $addToStartVal;

                $.ajax({
                    type: 'POST',
                    url: opts.uploadUrl,
                    data: data,
                    files: $(this),
                    iframe: true,
                    processData: false,
                    dataType: "json"
                }).done(function (resp) {
                    if (resp['result']) {
                        var img = resp['image'];
                        var photo = loadPhoto(img);
                        pastePhotos(photo);
                        ids.push(img['id']);
                        $uploadProgress.css('width', '100%');
                        $progressOverlay.hide();
                        if (opts.hasName || opts.hasDesc) {
                            editPhotos(ids);
                        }
                    }
                    else {
                        $log.append(resp['errors']);
                    }
                });
            });
        }

        $('.save-changes', $editorModal).click(function (e) {
            e.preventDefault();
            $.post(opts.updateUrl, $('input, textarea', $editorForm).serialize() + csrfParams, function (data) {
                var count = data.length;
                for (var key = 0; key < count; key++) {
                    var p = data[key];
                    var photo = photos[p.id];
                    $('img', photo).attr('src', p['src']);
                    if (opts.hasName)
                        $('.caption h5', photo).text(p['name']);
                    if (opts.hasDesc)
                        $('.caption p', photo).text(p['description']);
                }
                $editorModal.modal('hide');
                //deselect all items after editing
                $('.photo.selected', $sorter).each(function () {
                    $('.photo-select', this).prop('checked', false)
                }).removeClass('selected');
                $('.select_all', $gallery).prop('checked', false);
                updateButtons();
            }, 'json');

        });

        $('.edit-selected', $gallery).click(function (e) {
            e.preventDefault();
            var ids = [];
            $('.photo.selected', $sorter).each(function () {
                ids.push($(this).data('id'));
            });
            editPhotos(ids);
            return false;
        });

        $('.remove-selected', $gallery).click(function (e) {
            e.preventDefault();
            if (!confirm('Действительно удалить?')) return false;
            var ids = [];
            $('.photo.selected', $sorter).each(function () {
                ids.push($(this).data('id'));
            });
            removePhotos(ids);
        });

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

        for (var i = 0, l = opts.photos.length; i < l; i++) {
            var resp = opts.photos[i];
            var photo = loadPhoto(resp);
            pastePhotos(photo);
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
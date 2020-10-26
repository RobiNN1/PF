$(function () {
    $('#tpl_file').change(function () {
        let val = $(this).val();
        if (val === 'none') {
            $('#tpl-text').show();
        } else {
            $('#tpl-text').hide();
        }
    });

    resize_editor();
    $(window).resize(resize_editor);

    function resize_editor() {
        $('#editor-container').css('height', (parseInt($(window).height()) > 1019) ? parseInt($(window).height()) - 439 : 580);
    }
});

let editor = grapesjs.init({
    cssIcons: '',
    container: '#gjs',
    height: '100%',
    width: '100%',
    avoidInlineStyle: true,
    noticeOnUnload: false,
    plugins: [
        'gjs-preset-newsletter',
        editor => {
            editor.Panels.addButton('options', [{
                id: 'undo',
                className: '',
                label: '<i class="fa fa-undo"></i>',
                command: 'undo',
                attributes: {title: 'Undo (CTRL/CMD + Z)'}
            }, {
                id: 'redo',
                className: '',
                label: '<i class="fa fa-redo"></i>',
                command: 'redo',
                attributes: {title: 'Redo (CTRL/CMD + SHIFT + Z)'}
            }]);
        }
    ],
    fromElement: true,
    storageManager: {type: null},
    storageType: '',
    storeOnChange: true,
    storeAfterUpload: true,
    assetManager: {
        storageType: '',
        storeOnChange: true,
        storeAfterUpload: true,
        upload: SITE_URL + 'infusions/newsletter_panel/includes/upload.php' + AID,
        uploadName: 'files',
        multiUpload: true,
        assets: [],
        uploadFile: function (e) {
            let files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
            let formData = new FormData();
            for (let i in files) {
                formData.append('file-' + i, files[i])
            }
            $.ajax({
                url: SITE_URL + 'infusions/newsletter_panel/includes/upload.php' + AID,
                type: 'POST',
                data: formData,
                contentType: false,
                crossDomain: true,
                dataType: 'json',
                mimeType: 'multipart/form-data',
                processData: false,
                success: function (result) {
                    let img_json = [];
                    $.each(result['data'], function (key, value) {
                        img_json[key] = value;
                    });
                    let images = img_json;
                    editor.AssetManager.add(images);
                }
            });
        }
    }
});

editor.on('load', function () {
    editor.Panels.removeButton('options', 'gjs-toggle-images');
    editor.Panels.removeButton('options', 'gjs-open-import-template');
    editor.Panels.removeButton('options', 'export-template');
});

editor.RichTextEditor.add('custom-vars', {
    icon: `<select class="gjs-field">
        <option value="">- Select -</option>
        <option value="[TITLE]">[TITLE] - Template name/Subject</option>
        <option value="[LOGO]">[LOGO] - Site logo</option>
        <option value="[SITENAME]">[SITENAME] - Site name</option>
        <option value="[SITEURL]">[SITEURL] - Site URL</option>
        <option value="[SITE_COPYRIGHT]">[SITE_COPYRIGHT] - Site copyright</option>
        <option value="[EMAIL]">[EMAIL] - Subscriber email</option>
        <option value="[UNSUBSCRIBE]">[UNSUBSCRIBE] - Unsubscribe link</option>
      </select>`,
    // Bind the 'result' on 'change' listener
    event: 'change',
    result: (rte, action) => rte.insertHTML(action.btn.firstChild.value),
    // Reset the select on change
    update: (rte, action) => {
        action.btn.firstChild.value = "";
    }
})

editor.addComponents('<style>' + TPL_STYLE + '</style>');

const htmlTextarea = document.getElementById('tpl_body');
const cssTextarea = document.getElementById('tpl_style');

const updateTextarea = (component, editor) => {
    const e = component.em.get('Editor');
    htmlTextarea.value = e.getHtml();
    cssTextarea.value = e.getCss();
}

editor.on('component:add', updateTextarea);
editor.on('component:update', updateTextarea);
editor.on('component:remove', updateTextarea);

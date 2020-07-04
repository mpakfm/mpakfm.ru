$(document).ready(function(){
    tinyMCE.init({
        // General options
        selector: 'textarea.blog_text',
        language:"ru",
        //theme : "advanced",
        plugins: 'advcode casechange formatpainter autolink lists checklist media pageembed powerpaste',
        toolbar: 'casechange checklist code formatpainter pageembed',
        toolbar_mode: 'floating'
    });
    $('.js-post-hidden').click(function(){
        let data = {
            action: 'hidden',
            id: $(this).data('id')
        };
        $.ajax({
            url: '/blog/action',
            dataType: 'json',
            type:'POST',
            data: data,
            success: function (data, status) {
                if (!data.result) {
                    console.log(data.error);
                    return;
                }
                let el = $('[data-post="'+data.id+'"]');
                if (el.length == 0) {
                    return;
                }
                let btn = el.find('.js-post-hidden');
                if (data.hidden) { // set hide
                    $(el).addClass('post-hidden');
                    $(btn).addClass('btn-info').removeClass('btn-secondary').text('показать');
                } else { // set visible
                    $(el).removeClass('post-hidden');
                    $(btn).addClass('btn-secondary').removeClass('btn-info').text('спрятать');
                }
            }
        });
    });
    $('.js-post-delete').click(function(){
        if (!confirm('Подтвердить. Удаление необратимо.')) {
            return false;
        }
        let data = {
            action: 'delete',
            id: $(this).data('id')
        };
        $.ajax({
            url: '/blog/action',
            dataType: 'json',
            type:'POST',
            data: data,
            success: function (data, status) {
                if (!data.result) {
                    console.log(data.error);
                    return;
                }
                let el = $('[data-post="'+data.id+'"]');
                if (el.length == 0) {
                    return;
                }
                let btn = el.find('.js-post-hidden');
                if (data.deleted) { // set hide
                    $(el).empty().remove();
                } else { // set visible
                    return;
                }
            }
        });
    });
});
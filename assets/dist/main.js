$(window).on('load', function() {

    $('.level-bar-inner').each(function() {
    
        var itemWidth = $(this).data('level');
        
        $(this).animate({
            width: itemWidth
        }, 800);
        
    });

});

jQuery(document).ready(function($) {
    /*======= Skillset *=======*/
    $('.level-bar-inner').css('width', '0');
    
    /* Bootstrap Tooltip for Skillset */
    $('.level-label').tooltip();
    
    /* Github Calendar - https://github.com/IonicaBizau/github-calendar */
    //if ($('#github-graph').length) {
    //    GitHubCalendar("#github-graph", "mpakfm", { responsive: true });
    //}
    
    /* Github Activity Feed - https://github.com/caseyscarborough/github-activity */
    //GitHubActivity.feed({ username: "mpakfm", selector: "#ghfeed" });

    let timingField = document.getElementById('timing');
    let rateField = document.getElementById('rate');
    let moneyField = document.getElementById('money');
    if (timingField) {
        timingField.oninput = function() {
            rateHour = parseFloat(timingField.value.replace(',', '.'));
            try {
                let money = Math.round(rateHour * rateField.value * 100) / 100;
                console.log('money: ' + money);
                console.log('typeof money: ' + typeof money);
                if (money && !isNaN(money)) {
                    moneyField.value = money;
                    console.log('set money: ' + money);
                }
            } catch (e) {
                console.log('exception:')
                console.log(e);
            }
        };
    }

    if (moneyField) {
        moneyField.oninput = function () {
            timingField.value = '';
            console.log('set empty to timing');
        };
    }

    let btnPay = $('#js-btn-pay');
    if (btnPay.length > 0) {
        btnPay.click(function(){
            if (!paymentFormValidation()) {
                return;
            }
            $.ajax({
                url:'/payment/form',
                dataType:'json',
                success:function(data,status){
                    console.log(data);
                    console.log(status);
                    if (status !== 'success') {
                        console.log('error');
                        return;
                    }
                    $('[name="MerchantLogin"]').val(data.MerchantLogin);
                    $('[name="OutSum"]').val(data.OutSum);
                    $('[name="InvId"]').val(data.InvId);
                    $('[name="Description"]').val(data.Description);
                    $('[name="SignatureValue"]').val(data.SignatureValue);
                    $('[name="IncCurrLabel"]').val(data.IncCurrLabel);
                    $('[name="Culture"]').val(data.Culture);
                    $('[name="Email"]').val(data.Email);
                    $('[name="Encoding"]').val(data.Encoding);
                    $('#payment-form').submit();
                },
                type:'POST',
                data:{
                    money: $('#money').val(),
                    email: $('#email').val(),
                    comment: $('#comment').val(),
                    organization_name: $('#organization_name').val(),
                    organization_inn: $('#organization_inn').val(),
                    organization: $('#organization').prop('checked') ? '1' : '0',
                    foreign_organization: $('#foreign_organization').prop('checked') ? '1' : '0',
                }
            });
        });
    }

    $('.form-group.organization').hide();

    paymentFormHandler();
});

function paymentFormHandler() {
    $('#organization').click(function (){
        $('.form-group.organization').toggle();
    });
    $('#foreign_organization').click(function (){
        $('.form-group.organization.foreign_organization').toggle();
    });
    $('.js-valid input, .js-valid textarea').focusout(function(){
        paymentFieldValidation(this);
    });
}

function paymentFieldValidation(el) {
    let field = $(el).attr('id');
    let parent = $(el).parents('.js-valid');
    if ($('#' + field).val() == '') {
        $(parent).addClass('error');
    } else if (field == 'money' && (isNaN(parseFloat($('#' + field).val())) || parseFloat($('#' + field).val()) <= 0) ) {
        $(parent).addClass('error');
    } else {
        if (field == 'money') {
            let floatVal = parseFloat($('#' + field).val());
            $('#' + field).val(floatVal);
        }
        $(parent).removeClass('error');
    }
}

function paymentFormValidation() {
    let isValid = true;

    $('#payment-form .js-valid').removeClass('error');

    if ($('#money').val() == '' || isNaN(parseFloat($('#money').val())) || parseFloat($('#money').val()) <= 0 ) {
        isValid = false;
        let parent = $('#money').parents('.js-valid');
        $(parent).addClass('error');
    }

    if ($('#email').val() == '') {
        isValid = false;
        let parent = $('#email').parents('.js-valid');
        $(parent).addClass('error');
    }

    if ($('#comment').val() == '') {
        isValid = false;
        let parent = $('#comment').parents('.js-valid');
        $(parent).addClass('error');
    }

    if ($('#organization').prop('checked')) {
        if ($('#organization_name').val() == '') {
            isValid = false;
            let parent = $('#organization_name').parents('.js-valid');
            $(parent).addClass('error');
        }
        if (!$('#foreign_organization').prop('checked')) {
            if ($('#organization_inn').val() == '') {
                isValid = false;
                let parent = $('#organization_inn').parents('.js-valid');
                $(parent).addClass('error');
            }
        }
    }

    return isValid;
}

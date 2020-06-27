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
    if ($('#github-graph').length) {
        GitHubCalendar("#github-graph", "mpakfm", { responsive: true });
    }
    
    /* Github Activity Feed - https://github.com/caseyscarborough/github-activity */
    //GitHubActivity.feed({ username: "mpakfm", selector: "#ghfeed" });
});

document.addEventListener('DOMContentLoaded', function(){
    console.log('ready');
    let timingField = document.getElementById('timing');
    let rateField = document.getElementById('rate');
    let moneyField = document.getElementById('money');
    timingField.oninput = function() {
        rateHour = timingField.value;
        console.log('oninput hour: ' + rateHour);
        try {
            let money = rateHour * rateField.value;
            console.log('money: ' + money);
            console.log('typeof money: ' + typeof money);
            if (money && money !== NaN) {
                moneyField.value = money;
                console.log('set money: ' + money);
            }
        } catch (e) {
            console.log('exception:')
            console.log(e);
        }
    };

    moneyField.oninput = function() {
        timingField.value = '';
        console.log('set empty to timing');
    };

    let btnPay = $('#js-btn-pay');
    console.log(btnPay);
    btnPay.click(function(){
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
            }
        });
    });
});

function paymentFormHandler() {
    $('#timing').keypress(function (){

    });
}
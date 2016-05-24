$(function() {
    $('.customtx').focusin(function() {
        $(this).addClass('foc_tx');
    });
    $('.customtx').focusout(function() {
        $(this).removeClass('foc_tx');
    });
    //$('#time1,#time2,#date1,#date2,#date3,#phone1,#login1,#l_music,#l_tabac, #test-check1,#test-check2,#city_f,#cat_mir,#cate_bonus,.radio_vop, .act_li input').styler();

    $('.select-field, .checkbox-field').styler({
        selectVisibleOptions: 10
    });

});
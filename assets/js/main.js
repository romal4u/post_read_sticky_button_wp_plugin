var clickonread = 0;
var ajax_url = vee_plugin_ajax_object.ajax_url;

jQuery(document).ready(function () {
    if(vee_plugin_ajax_object.vee_already_read > 0)
        read_btn(3);
    else     
        read_btn(1);
    
    red_unread_btn();
});


function red_unread_btn() {
    jQuery(window).on("scroll", function () {

        var downPos = jQuery(window).scrollTop() + jQuery(window).height();
        var down80 = (jQuery(document).height() * 80) / 100;

        if(clickonread == 2)
            return false;

        if (downPos > down80 || clickonread == 1) {
            read_btn(2);
        }
        else {
            read_btn(1);
        }
    });
}

function setmeread(a) {
    if (a == 1) {
        clickonread = 1;
        read_btn(2);
    }
    else {
        var data = {
            'action': 'vee_save_read',
            'vee_post_id': vee_plugin_ajax_object.vee_post_id,
            'nonce': vee_plugin_ajax_object.vee_read_unread_nonce 
        };
        jQuery.ajax({
            url: ajax_url,
            type: 'post',
            data: data,
            // dataType: 'json',
            success: function (response) {
                if(response == 'success') {
                    read_btn(4);
                }
                else
                    console.log(response);
            }
        });
    
    }
}

function read_btn(read) {
    var btn = '';
    if (read == 1) {
        btn = `<a href="javascript:void(0);" onclick="javascript:setmeread(1);" class="float" title="Please read first!">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="book" class="svg-inline--fa fa-book fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M448 360V24c0-13.3-10.7-24-24-24H96C43 0 0 43 0 96v320c0 53 43 96 96 96h328c13.3 0 24-10.7 24-24v-16c0-7.5-3.5-14.3-8.9-18.7-4.2-15.4-4.2-59.3 0-74.7 5.4-4.3 8.9-11.1 8.9-18.6zM128 134c0-3.3 2.7-6 6-6h212c3.3 0 6 2.7 6 6v20c0 3.3-2.7 6-6 6H134c-3.3 0-6-2.7-6-6v-20zm0 64c0-3.3 2.7-6 6-6h212c3.3 0 6 2.7 6 6v20c0 3.3-2.7 6-6 6H134c-3.3 0-6-2.7-6-6v-20zm253.4 250H96c-17.7 0-32-14.3-32-32 0-17.6 14.4-32 32-32h285.4c-1.9 17.1-1.9 46.9 0 64z"></path></svg>
        </a>`;
    }
    else if (read == 2) {
        btn = `<a href="javascript:void(0);" onclick="javascript:setmeread(0);" class="float" title="Click Me">
        <span id="click">Set me read!</span>
        </a>`;
    }
    else if (read == 3){
        btn = `<a href="javascript:void(0);" class="float" title="Read complete!">
        <svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="readme" class="svg-inline--fa fa-readme fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M528.3 46.5H388.5c-48.1 0-89.9 33.3-100.4 80.3-10.6-47-52.3-80.3-100.4-80.3H48c-26.5 0-48 21.5-48 48v245.8c0 26.5 21.5 48 48 48h89.7c102.2 0 132.7 24.4 147.3 75 .7 2.8 5.2 2.8 6 0 14.7-50.6 45.2-75 147.3-75H528c26.5 0 48-21.5 48-48V94.6c0-26.4-21.3-47.9-47.7-48.1zM242 311.9c0 1.9-1.5 3.5-3.5 3.5H78.2c-1.9 0-3.5-1.5-3.5-3.5V289c0-1.9 1.5-3.5 3.5-3.5h160.4c1.9 0 3.5 1.5 3.5 3.5v22.9zm0-60.9c0 1.9-1.5 3.5-3.5 3.5H78.2c-1.9 0-3.5-1.5-3.5-3.5v-22.9c0-1.9 1.5-3.5 3.5-3.5h160.4c1.9 0 3.5 1.5 3.5 3.5V251zm0-60.9c0 1.9-1.5 3.5-3.5 3.5H78.2c-1.9 0-3.5-1.5-3.5-3.5v-22.9c0-1.9 1.5-3.5 3.5-3.5h160.4c1.9 0 3.5 1.5 3.5 3.5v22.9zm259.3 121.7c0 1.9-1.5 3.5-3.5 3.5H337.5c-1.9 0-3.5-1.5-3.5-3.5v-22.9c0-1.9 1.5-3.5 3.5-3.5h160.4c1.9 0 3.5 1.5 3.5 3.5v22.9zm0-60.9c0 1.9-1.5 3.5-3.5 3.5H337.5c-1.9 0-3.5-1.5-3.5-3.5V228c0-1.9 1.5-3.5 3.5-3.5h160.4c1.9 0 3.5 1.5 3.5 3.5v22.9zm0-60.9c0 1.9-1.5 3.5-3.5 3.5H337.5c-1.9 0-3.5-1.5-3.5-3.5v-22.8c0-1.9 1.5-3.5 3.5-3.5h160.4c1.9 0 3.5 1.5 3.5 3.5V190z"></path></svg></a>`;
        clickonread = 2;
    }
    else {
        btn = `<a href="javascript:void(0);" class="float" title="Read Complete!">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle" class="svg-inline--fa fa-check-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path></svg>
        </a>`;    
        clickonread = 2;
    }
    jQuery("#read_unread").html(btn);

}
// helper function to open centered popups
function popupwindow(url, title, w, h) {
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    return false;
}

function addBindFunctions() {
    /* change round (side-)image on hover */
    $( document ).on("mouseenter", "#dinkelmaus",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/maus2.jpg)');
    });
    $( document ).on("mouseleave", "#dinkelmaus",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/maus.jpg)');
    });

    $( document ).on("mouseenter", "#preis",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/var.jpg)');
    });
    $( document ).on("mouseleave", "#preis",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/maus.jpg)');
    });

    $( document ).on("mouseenter", "#bilder",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/images.jpg)');
    });
    $( document ).on("mouseleave", "#bilder",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/maus.jpg)');
    });

    $( document ).on("mouseenter", "#anwendung",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/dinkel.jpg)');
    });
    $( document ).on("mouseleave", "#anwendung",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/maus.jpg)');
    });

    $( document ).on("mouseenter", "#kontakt",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/email.jpg)');
    });
    $( document ).on("mouseleave", "#kontakt",
        function()
        {
            $("#sideImage").css('background-image','url(../images/roundimages/maus.jpg)');
    });
}

// helper-function for image gallery
function formatCaptions($target) {
            return '<p>' + $target.attr("title") + '</p>';
        }

// helper function to get url parameters
$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
        return null;
    }
    else{
        return results[1] || 0;
    }
}

/*
    ==================================
        Document Ready function
    ==================================
*/
$( document ).ready(function() {
    $("#impressum").load("../others/impressum.html");
    addBindFunctions();

    var htmlName = location.pathname.split('/').slice(-1)[0];

    if (htmlName != 'bilder-impressionen.html') {
        // initialize boxer (for impresum dialog image)
        $(".boxer").boxer();
    }

    /* site specific java script */
    switch (htmlName) {
        case "startseite.html":
            $(".news:gt(2)").hide();
            $("#showmorelink").click(function(){
                $(".news").show();
                $("#showmorelink").hide();
            });
            break;
        case "was-ist-eine-dinkelmaus.html":
            $("#sideImage").css('background-image','url(../images/roundimages/maus2.jpg)');
            break;
        case "varianten-preise.html":
            $("#sideImage").css('background-image','url(../images/roundimages/var.jpg)');
            break;
        case "bilder-impressionen.html":    // gallery: automatically get & display images from images/gallery folder
            $("#sideImage").css('background-image','url(../images/roundimages/images.jpg)');
            $.post('../others/getImages.php?gallery='+$.urlParam('gallery'), function(output) {
                $('.images').html(output).show();
                $('.thumbnail').nailthumb();
                $(".boxer").boxer({
                    formatter: formatCaptions
                });
            });
            break;
        case "anwendung-pflege.html":
            $("#sideImage").css('background-image','url(../images/roundimages/dinkel.jpg)');
            break;
        case "kontakt-mail.html":
            $("#sideImage").css('background-image','url(../images/roundimages/email.jpg)');
            $("#mailform-parent").load('../others/mail.php');

            $(document).on('submit', '#mailform', function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    data: { name: $('input[name="name"]').val(),
                        email: $('input[name="email"]').val(),
                        website: $('input[name="website"]').val(),
                        nachricht: $('textarea[name="nachricht"]').val()
                    },
                    dataType: "html",
                    cache: false,
                    url: "../others/mail.php",
                    success: function(data){
                        $("#mailform").html(data);
                    }
                });
            });
            break;
        default:
            break;
    }

    // update copyright year
    $("#year").text((new Date).getFullYear());
});


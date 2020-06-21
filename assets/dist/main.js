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
    GitHubCalendar("#github-graph", "mpakfm", { responsive: true });
    
    /* Github Activity Feed - https://github.com/caseyscarborough/github-activity */
    //GitHubActivity.feed({ username: "mpakfm", selector: "#ghfeed" });
});
function hideComment(element){
    $(element).parent().fadeOut(1000);
}

$(document).ready(function(){
    $("[data-approval-outcome]").on("click", function(){
        console.log($(this).data("comment-id"));
        console.log($(this).data("approval-outcome"));
        $.ajax({
            type: "POST",
            url: "/admin/comment/approve",
            data: {
                id: $(this).data("comment-id"),
                outcome: $(this).data("approval-outcome")
            },
            success: hideComment($(this))
        });
    });
});